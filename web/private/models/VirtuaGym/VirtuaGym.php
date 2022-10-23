<?php

use Model\Users;

class VirtuaGym {
    private $user;

    public function __construct(){
        $this->user = new Users;
    }

    public function testConnection() {

    }
}