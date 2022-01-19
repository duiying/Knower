<?php

declare(strict_types=1);

use App\Util\Route;
use Hyperf\HttpServer\Router\Router;
use App\Middleware\CorsMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;
use App\Middleware\PassportMiddleware;
use App\Middleware\PjaxMiddleware;
use App\Middleware\FrontendTokenMiddleware;
use App\Middleware\PreventRepeatMiddleware;
use App\Middleware\FrontendPjaxMiddleware;
use App\Middleware\FrontendMiddleware;

Router::get('/favicon.ico', function () {
    return '';
});

/********************************************************* 后台路由 begin ***********************************************/

// 后台首页
Router::addRoute(['GET'], '/admin', 'App\View\Backend\Index\Action\IndexAction@index', ['middleware' => [PassportMiddleware::class, PjaxMiddleware::class]]);

// 后台视图渲染相关路由（统一由 /view 开头）
Router::addGroup('/view/',function () {
    Router::get('user/login','App\View\AdminPassport\User\Action\LoginAction@handle');                     // 登录页

    Router::get('user/search','App\View\AdminPassport\User\Action\SearchAction@handle');                   // 管理员列表
    Router::get('user/create','App\View\AdminPassport\User\Action\CreateAction@handle');                   // 管理员创建
    Router::get('user/update','App\View\AdminPassport\User\Action\UpdateAction@handle');                   // 管理员更新

    Router::get('menu/search','App\View\AdminPassport\Menu\Action\SearchAction@handle');                   // 菜单列表
    Router::get('menu/create', 'App\View\AdminPassport\Menu\Action\CreateAction@handle');                  // 菜单创建
    Router::get('menu/update','App\View\AdminPassport\Menu\Action\UpdateAction@handle');                   // 菜单更新

    Router::get('permission/search','App\View\AdminPassport\Permission\Action\SearchAction@handle');       // 权限列表
    Router::get('permission/create', 'App\View\AdminPassport\Permission\Action\CreateAction@handle');      // 权限创建
    Router::get('permission/update','App\View\AdminPassport\Permission\Action\UpdateAction@handle');       // 权限更新

    Router::get('role/search','App\View\AdminPassport\Role\Action\SearchAction@handle');                   // 角色列表
    Router::get('role/create', 'App\View\AdminPassport\Role\Action\CreateAction@handle');                  // 角色创建
    Router::get('role/update','App\View\AdminPassport\Role\Action\UpdateAction@handle');                   // 角色更新

    // 文章相关
    Router::get('article/search','App\View\Backend\Article\Action\SearchAction@handle');
    Router::get('article/create', 'App\View\Backend\Article\Action\CreateAction@handle');
    Router::get('article/update','App\View\Backend\Article\Action\UpdateAction@handle');

    // 标签相关
    Router::get('tag/search','App\View\Backend\Tag\Action\SearchAction@handle');
    Router::get('tag/create', 'App\View\Backend\Tag\Action\CreateAction@handle');
    Router::get('tag/update','App\View\Backend\Tag\Action\UpdateAction@handle');

    // 评论相关
    Router::get('comment/search','App\View\Backend\Comment\Action\SearchAction@handle');

    // 用户相关
    Router::get('account/search','App\View\Backend\Account\Action\SearchAction@handle');

    // 行为日志
    Router::get('action_log/search', 'App\View\Backend\ActionLog\Action\SearchAction@handle');
}, ['middleware' => [PassportMiddleware::class, PjaxMiddleware::class]]);

// 后台接口相关路由（统一由 /v1 开头）
Router::addGroup('/v1/',function () {
    // 管理员相关
    Router::get('user/search', Route::decoration('AdminPassport\User\Action\SearchAction'));
    Router::get('user/find', Route::decoration('AdminPassport\User\Action\FindAction'));
    Router::post('user/create', Route::decoration('AdminPassport\User\Action\CreateAction'));
    Router::post('user/update', Route::decoration('AdminPassport\User\Action\UpdateAction'));
    Router::post('user/login', Route::decoration('AdminPassport\User\Action\LoginAction'));
    Router::post('user/delete', Route::decoration('AdminPassport\User\Action\UpdateFieldAction'));
    Router::get('user/menu', Route::decoration('AdminPassport\User\Action\MenuAction'));
    Router::get('user/get_info', Route::decoration('AdminPassport\User\Action\GetUserInfoAction'));

    // 菜单相关
    Router::get('menu/search', Route::decoration('AdminPassport\Menu\Action\SearchAction'));
    Router::get('menu/select', Route::decoration('AdminPassport\Menu\Action\SearchAction'));
    Router::post('menu/create', Route::decoration('AdminPassport\Menu\Action\CreateAction'));
    Router::post('menu/update', Route::decoration('AdminPassport\Menu\Action\UpdateAction'));
    Router::get('menu/find', Route::decoration('AdminPassport\Menu\Action\FindAction'));
    Router::post('menu/delete', Route::decoration('AdminPassport\Menu\Action\UpdateFieldAction'));

    // 权限相关
    Router::get('permission/search', Route::decoration('AdminPassport\Permission\Action\SearchAction'));
    Router::get('permission/select', Route::decoration('AdminPassport\Permission\Action\SelectAction'));
    Router::post('permission/create', Route::decoration('AdminPassport\Permission\Action\CreateAction'));
    Router::post('permission/update', Route::decoration('AdminPassport\Permission\Action\UpdateAction'));
    Router::get('permission/find', Route::decoration('AdminPassport\Permission\Action\FindAction'));
    Router::post('permission/delete', Route::decoration('AdminPassport\Permission\Action\UpdateFieldAction'));

    // 角色相关
    Router::get('role/search', Route::decoration('AdminPassport\Role\Action\SearchAction'));
    Router::get('role/select', Route::decoration('AdminPassport\Role\Action\SearchAction'));
    Router::post('role/create', Route::decoration('AdminPassport\Role\Action\CreateAction'));
    Router::post('role/update', Route::decoration('AdminPassport\Role\Action\UpdateAction'));
    Router::get('role/find', Route::decoration('AdminPassport\Role\Action\FindAction'));
    Router::post('role/delete', Route::decoration('AdminPassport\Role\Action\UpdateFieldAction'));

    // 文章相关
    Router::get('article/search', Route::decoration('Article\Action\SearchAction'));
    Router::post('article/create', Route::decoration('Article\Action\CreateAction'));
    Router::post('article/update', Route::decoration('Article\Action\UpdateAction'));
    Router::get('article/find', Route::decoration('Article\Action\FindAction'));
    Router::post('article/delete', Route::decoration('Article\Action\UpdateFieldAction'));
    Router::get('article/async_es', Route::decoration('Article\Action\AsyncEsAction'));

    // 标签相关
    Router::get('tag/search', Route::decoration('Tag\Action\SearchAction'));
    Router::post('tag/create', Route::decoration('Tag\Action\CreateAction'));
    Router::post('tag/update', Route::decoration('Tag\Action\UpdateAction'));
    Router::get('tag/find', Route::decoration('Tag\Action\FindAction'));
    Router::post('tag/delete', Route::decoration('Tag\Action\UpdateFieldAction'));
    Router::get('tag/select', Route::decoration('Tag\Action\SelectAction'));

    // 评论相关
    Router::get('comment/search', Route::decoration('Comment\Action\SearchAction'));
    Router::post('comment/update_status', Route::decoration('Comment\Action\UpdateFieldAction'));

    // 用户相关
    Router::get('account/search', Route::decoration('Account\Action\SearchAction'));
    Router::post('account/update_status', Route::decoration('Account\Action\UpdateFieldAction'));
    Router::post('account/update_mark_field', Route::decoration('Account\Action\UpdateMarkFieldAction'));

    // 行为日志
    Router::get('action_log/search', Route::decoration('ActionLog\Action\SearchAction'));

    // 图片上传
    Router::post('img/upload', Route::decoration('Img\Action\UploadAction'));

    // 数据统计
    Router::get('data/stat', Route::decoration('Stat\Action\StatAction'));
}, ['middleware' => [CorsMiddleware::class, PassportMiddleware::class, ValidationMiddleware::class]]);

// 退出登录
Router::addRoute(['POST'], '/v1/user/logout', Route::decoration('AdminPassport\User\Action\LogoutAction'), [
    'middleware' => [CorsMiddleware::class, ValidationMiddleware::class]
]);

/********************************************************* 后台路由 end *************************************************/

/********************************************************* 前台路由 begin ***********************************************/

// 前台视图路由
Router::addGroup('',function () {
    // 首页
    Router::get('/','App\View\Frontend\IndexAction@index');
    // 文章详情页
    Router::get('/article/detail','App\View\Frontend\ArticleDetailAction@handle');
    // 登录页
    Router::get('/login','App\View\Frontend\LoginAction@handle');
}, ['middleware' => [FrontendMiddleware::class, FrontendPjaxMiddleware::class]]);

// 文章详情接口
Router::get('/article/info', Route::decoration('Article\Action\InfoAction'), ['middleware' => [FrontendMiddleware::class]]);
// 标签列表接口
Router::get('/tags', Route::decoration('Tag\Action\ListAction'));
// 首页文章列表接口
Router::get('/articles', Route::decoration('Article\Action\ListAction'));
// 登录相关接口
Router::get('/oauth/github', 'App\Module\Account\Action\OAuthAction@github');
Router::get('/oauth/github/callback', 'App\Module\Account\Action\OAuthAction@githubCallback');
Router::get('/oauth/qq', 'App\Module\Account\Action\OAuthAction@qq');
Router::get('/oauth/qq/callback', 'App\Module\Account\Action\OAuthAction@qqCallback');
// 根据前台 token 获取用户登录信息接口
Router::get('/account/get_info_by_token', Route::decoration('Account\Action\GetAccountInfoByTokenAction'));
// 退出登录
Router::get('/account/logout', Route::decoration('Account\Action\LogoutAction'));
// 创建评论接口
Router::post('/comment/create', Route::decoration('Comment\Action\CreateAction'),  ['middleware' => [PreventRepeatMiddleware::class, FrontendTokenMiddleware::class]]);
// 评论列表接口
Router::get('/comments', Route::decoration('Comment\Action\CommentsAction'), ['middleware' => [FrontendMiddleware::class]]);
// 删除评论接口
Router::post('/comment/delete', Route::decoration('Comment\Action\DeleteCommentAction'), ['middleware' => [PreventRepeatMiddleware::class, FrontendTokenMiddleware::class]]);
/********************************************************* 前台路由 end *************************************************/