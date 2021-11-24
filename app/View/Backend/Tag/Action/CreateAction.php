<?php

namespace App\View\Backend\Tag\Action;

use Hyperf\View\RenderInterface;

class CreateAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('tag/create');
    }
}