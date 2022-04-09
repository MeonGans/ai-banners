<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $request['password'] = Hash::make($request['password']);
        $user = User::query()->create($request->toArray());
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:4',
        ]);
        if ($validator->fails())
        {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        $user = User::query()->where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $success['token'] = $user->createToken('Banners')->accessToken;
                return $this->sendResponse($success, 'User login successfully.');
            } else {
                return $this->sendError('Password mismatch', [], 422);
            }
        } else {
            return $this->sendError('User does not exist', [], 422);
        }
    }

    public function user()
    {
        return new UserResource(Auth::user());
    }

    public function logout()
    {
        $token = Auth::user()->token();
        $token->revoke();
        return $this->sendResponse('Success', 'User logout successfully.');
    }
}
