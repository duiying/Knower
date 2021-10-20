<?php

namespace App\View\Article\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('article/create');
    }
}