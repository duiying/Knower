<?php

namespace App\View\User\Action;

use Hyperf\View\RenderInterface;

/**
 * 用户（管理员）相关视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\User\Action
 */
class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('user/search');
    }
}