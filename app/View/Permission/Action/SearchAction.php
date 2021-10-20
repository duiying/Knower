<?php

namespace App\View\Permission\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('permission/search');
    }
}