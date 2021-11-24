<?php

namespace App\View\Frontend;

use Hyperf\View\RenderInterface;

/**
 * 首页相关视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\Index\Action
 */
class FrontendAction
{
    public function index(RenderInterface $render)
    {
        return $render->render('frontend/layouts/app');
    }
}