<?php

namespace App\View\Index\Action;

use Hyperf\View\RenderInterface;

/**
 * 首页相关视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\Index\Action
 */
class IndexAction
{
    public function index(RenderInterface $render)
    {
        return $render->render('index/index', ['name' => 'Hyperf']);
    }
}