<?php

namespace App\View\Frontend;

use Hyperf\View\RenderInterface;

/**
 * 首页相关视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\Frontend
 */
class IndexAction
{
    public function index(RenderInterface $render)
    {
        return $render->render('frontend/index/index');
    }
}