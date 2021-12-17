<?php

namespace App\View\Backend\Comment\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('comment/search');
    }
}