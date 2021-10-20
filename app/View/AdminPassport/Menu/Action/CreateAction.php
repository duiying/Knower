<?php

namespace App\View\AdminPassport\Menu\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('menu/create');
    }
}