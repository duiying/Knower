<?php

namespace App\View\Backend\ActionLog\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('action_log/search');
    }
}