<?php

namespace App\View\AdminPassport\Role\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('role/search');
    }
}