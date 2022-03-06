<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    // is_premium:
    // 0 - To ALL
    // 1 - Premium

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Отобразить все группы
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //НОВУЮ ГРУППУ ПОКА НЕ ЗАПИСЫВАЕМ
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //ОТОБРАЖЕНИЕ ВСЕХ КАТЕГОРИЙ ВЫБРАНОЙ ГРУППЫ
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        //НЕ ОБНОВЛЯЕМ ИНФОРМАЦИЮ О ГРУППЕ
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //НЕ УДАЛЯЕМ ГРУППУ
    }
}
