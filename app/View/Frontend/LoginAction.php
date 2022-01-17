<?php

namespace App\View\Frontend;

use Hyperf\View\RenderInterface;

/**
 * 登录视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\Frontend
 */
class LoginAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('frontend/login/login');
    }
}