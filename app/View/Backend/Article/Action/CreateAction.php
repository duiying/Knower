<?php

namespace App\View\Backend\Article\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('article/create');
    }
}