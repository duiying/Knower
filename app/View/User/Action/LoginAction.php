<?php

namespace App\View\User\Action;

use Hyperf\View\RenderInterface;

class LoginAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('user/login');
    }
}