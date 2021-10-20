<?php

declare(strict_types=1);

use HyperfPlus\Route\Route;
use Hyperf\HttpServer\Router\Router;
use HyperfPlus\Middleware\CorsMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use App\Middleware\PassportMiddleware;
use HyperfPlus\Middleware\PjaxMiddleware;
use Hyperf\Tracer\Middleware\TraceMiddleware;

/**
 * 首页
 */
Router::addRoute(['GET'], '/', 'App\View\Index\Action\IndexAction@index', ['middleware' => [PassportMiddleware::class, PjaxMiddleware::class]]);

Router::get('/favicon.ico', function () {
    return '';
});

/**
 * 视图渲染相关路由（统一由 /view 开头）
 */
Router::addGroup('/view/',function () {
    Router::get('user/login','App\View\User\Action\LoginAction@handle');                    // 登录页

    Router::get('user/search','App\View\User\Action\SearchAction@handle');                  // 管理员列表
    Router::get('user/create','App\View\User\Action\CreateAction@handle');                  // 管理员创建
    Router::get('user/update','App\View\User\Action\UpdateAction@handle');                  // 管理员更新

    Router::get('menu/search','App\View\Menu\Action\SearchAction@handle');                  // 菜单列表
    Router::get('menu/create', 'App\View\Menu\Action\CreateAction@handle');                 // 菜单创建
    Router::get('menu/update','App\View\Menu\Action\UpdateAction@handle');                  // 菜单更新

    Router::get('permission/search','App\View\Permission\Action\SearchAction@handle');       // 权限列表
    Router::get('permission/create', 'App\View\Permission\Action\CreateAction@handle');      // 权限创建
    Router::get('permission/update','App\View\Permission\Action\UpdateAction@handle');       // 权限更新

    Router::get('role/search','App\View\Role\Action\SearchAction@handle');                   // 角色列表
    Router::get('role/create', 'App\View\Role\Action\CreateAction@handle');                  // 角色创建
    Router::get('role/update','App\View\Role\Action\UpdateAction@handle');                   // 角色更新

    Router::get('article/search','App\View\Article\Action\SearchAction@handle');             // 文章列表
    Router::get('article/create', 'App\View\Article\Action\CreateAction@handle');            // 文章创建
    Router::get('article/update','App\View\Article\Action\UpdateAction@handle');             // 文章更新
}, ['middleware' => [PassportMiddleware::class, PjaxMiddleware::class]]);

/**
 * 接口相关路由
 */
Router::addGroup('/v1/',function () {
    Router::get('test', 'HyperfPlus\Controller\IndexController@handle');

    Router::get('user/search', Route::decoration('User\Action\SearchAction'));                          // 管理员列表
    Router::get('user/find', Route::decoration('User\Action\FindAction'));                              // 管理员详情
    Router::post('user/create', Route::decoration('User\Action\CreateAction'));                         // 管理员创建
    Router::post('user/update', Route::decoration('User\Action\UpdateAction'));                         // 管理员更新
    Router::post('user/login', Route::decoration('User\Action\LoginAction'));                           // 管理员登录
    Router::post('user/update_field', Route::decoration('User\Action\UpdateFieldAction'));              // 管理员更新字段
    Router::get('user/menu', Route::decoration('User\Action\MenuAction'));                              // 管理员菜单
    Router::get('user/get_info', Route::decoration('User\Action\GetUserInfoAction'));                   // 管理员基础信息

    Router::get('menu/search', Route::decoration('Menu\Action\SearchAction'));                          // 菜单列表
    Router::get('menu/select', Route::decoration('Menu\Action\SearchAction'));                          // 菜单选择（创建、更新角色时用）
    Router::post('menu/create', Route::decoration('Menu\Action\CreateAction'));                         // 菜单创建
    Router::post('menu/update', Route::decoration('Menu\Action\UpdateAction'));                         // 菜单更新
    Router::get('menu/find', Route::decoration('Menu\Action\FindAction'));                              // 菜单详情
    Router::post('menu/update_field', Route::decoration('Menu\Action\UpdateFieldAction'));              // 菜单更新字段

    Router::get('permission/search', Route::decoration('Permission\Action\SearchAction'));              // 权限列表
    Router::get('permission/select', Route::decoration('Permission\Action\SearchAction'));              // 权限选择（创建、更新角色时用）
    Router::post('permission/create', Route::decoration('Permission\Action\CreateAction'));             // 权限创建
    Router::post('permission/update', Route::decoration('Permission\Action\UpdateAction'));             // 权限更新
    Router::get('permission/find', Route::decoration('Permission\Action\FindAction'));                  // 权限详情
    Router::post('permission/update_field', Route::decoration('Permission\Action\UpdateFieldAction'));  // 权限更新字段

    Router::get('role/search', Route::decoration('Role\Action\SearchAction'));                          // 角色列表
    Router::get('role/select', Route::decoration('Role\Action\SearchAction'));                          // 角色选择（创建、更新用户时用）
    Router::post('role/create', Route::decoration('Role\Action\CreateAction'));                         // 角色创建
    Router::post('role/update', Route::decoration('Role\Action\UpdateAction'));                         // 角色更新
    Router::get('role/find', Route::decoration('Role\Action\FindAction'));                              // 角色详情
    Router::post('role/update_field', Route::decoration('Role\Action\UpdateFieldAction'));              // 角色更新字段

    Router::get('article/search', Route::decoration('Article\Action\SearchAction'));                    // 文章列表
    Router::post('article/create', Route::decoration('Article\Action\CreateAction'));                   // 文章创建
    Router::post('article/update', Route::decoration('Article\Action\UpdateAction'));                   // 文章更新
    Router::get('article/find', Route::decoration('Article\Action\FindAction'));                        // 文章详情
    Router::post('article/update_field', Route::decoration('Article\Action\UpdateFieldAction'));        // 文章更新字段
}, ['middleware' => [TraceMiddleware::class, CorsMiddleware::class, PassportMiddleware::class, ValidationMiddleware::class]]);

Router::addRoute(['POST'], '/v1/user/logout', Route::decoration('User\Action\LogoutAction'), ['middleware' => [TraceMiddleware::class, CorsMiddleware::class, ValidationMiddleware::class]]);
