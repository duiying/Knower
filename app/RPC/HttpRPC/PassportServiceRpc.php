<?php

namespace App\RPC\HttpRPC;

use Hyperf\Di\Annotation\Inject;
use HyperfPlus\Http\Client;
use HyperfPlus\RPC\HttpRPC;
use HyperfPlus\Constant\Constant;

class PassportServiceRpc extends HttpRPC
{
    public $service;

    /**
     * @Inject()
     * @var Client
     */
    public $client;

    public function __construct()
    {
        $this->service = env('PASSPORT_SERVICE_URI');
    }

    public function checkUserPermission($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/check_permission',
        ]);
    }

    public function searchUser($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/search',
        ]);
    }

    public function createUser($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/create',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateUser($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/update',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateUserField($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/update_field',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function findUser($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/find',
        ]);
    }

    public function getUserMenuList($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/menu',
        ]);
    }

    public function login($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/login',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function logout($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/user/logout',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function searchMenu($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/menu/search',
        ]);
    }

    public function createMenu($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/menu/create',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateMenu($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/menu/update',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateMenuField($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/menu/update_field',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function findMenu($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/menu/find',
        ]);
    }

    public function searchPermission($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/permission/search',
        ]);
    }

    public function createPermission($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/permission/create',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updatePermission($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/permission/update',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updatePermissionField($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/permission/update_field',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function findPermission($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/permission/find',
        ]);
    }

    public function searchRole($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/role/search',
        ]);
    }

    public function createRole($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/role/create',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateRole($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/role/update',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function updateRoleField($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/role/update_field',
            'method'    => Constant::METHOD_POST
        ]);
    }

    public function findRole($requestData)
    {
        return $this->call($requestData, [
            'timeout'   => 1000,
            'uri'       => 'v1/role/find',
        ]);
    }
}