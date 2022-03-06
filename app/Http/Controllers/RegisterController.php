<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::query()->create($input);
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password] )){
            $user = Auth::user();
            $success['token'] =  $user->createToken('boards')->accessToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User logout successfully.');
        //return UserResource::collection($user);
     //   return $user->json;
//        if (Auth::check()) {
//            $user = Auth::user()->token();
//            $user->revoke();
//            $success['name'] = $user->name;
//            return $this->sendResponse($success, 'User logout successfully.');
//        }
//        else{
//            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
//        }
    }
}
