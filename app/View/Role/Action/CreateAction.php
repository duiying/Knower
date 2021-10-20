<?php

namespace App\View\Role\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('role/create');
    }
}