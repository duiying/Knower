<?php

namespace App\View\AdminPassport\Permission\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('permission/create');
    }
}