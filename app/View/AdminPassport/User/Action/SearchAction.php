<?php

namespace App\View\AdminPassport\User\Action;

use Hyperf\View\RenderInterface;

/**
 * 用户（管理员）相关视图渲染
 *
 * @author duiying <wangyaxiandev@gmail.com>
 * @package App\View\AdminPassport\User\Action
 */
class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('user/search');
    }
}