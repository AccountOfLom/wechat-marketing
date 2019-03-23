<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 韦育章 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/18 17:52
// +----------------------------------------------------------------------

use Illuminate\Support\Facades\Route;


//获取左侧菜单列表
Route::get('getMenuToLeft', 'MenuController@getDataToLeft');

//获取菜单列表内容页
Route::get('getMenuList', 'MenuController@getMenuList');

//编辑菜单
Route::get('editMenu/menu_id={menu_id}', 'MenuController@edit');

//编辑菜单
Route::post('saveMenu', 'MenuController@save');

//删除菜单
Route::post('deleteMenu', 'MenuController@delete');

//获取管理员列表
Route::get('getUserList', 'UserController@getData');

//编辑管理员
Route::get('editUser/user_id={user_id}', 'UserController@edit');

//编辑管理员
Route::post('saveUser', 'UserController@save');

//删除管理员
Route::post('deleteUser', 'UserController@delete');

//获取粉丝列表
Route::get('getWxUserList', 'WxUserController@getData');

//编辑粉丝
Route::get('editWxUser/user_id={user_id}', 'WxUserController@edit');

//编辑粉丝
Route::post('saveWxUser', 'WxUserController@save');

//删除粉丝
Route::post('deleteWxUser', 'WxUserController@delete');











