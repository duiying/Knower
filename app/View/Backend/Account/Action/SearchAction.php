<?php

namespace App\View\Backend\Account\Action;

use Hyperf\View\RenderInterface;

class SearchAction
{
    public function handle(RenderInterface $render)
    {
        return $render->render('account/search');
    }
}