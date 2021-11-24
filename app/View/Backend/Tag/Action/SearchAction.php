<?php

namespace App\View\Backend\Tag\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('tag/search');
    }
}