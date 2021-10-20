<?php

namespace App\View\Article\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('article/search');
    }
}