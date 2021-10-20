<?php

namespace App\View\User\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('user/create');
    }
}