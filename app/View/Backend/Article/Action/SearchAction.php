<?php

namespace App\View\Backend\Article\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('article/search');
    }
}