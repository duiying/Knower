<?php

namespace App\View\AdminPassport\Permission\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('permission/search');
    }
}