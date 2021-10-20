<?php

namespace App\Util;

class Route
{
    /**
     * 路由装饰
     *
     * @param string $route
     * @return string
     */
    public static function decoration($route = '')
    {
        return sprintf('App\Module\%s@handle', $route);
    }
}