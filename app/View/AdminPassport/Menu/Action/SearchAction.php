<?php

namespace App\View\AdminPassport\Menu\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('menu/search');
    }
}