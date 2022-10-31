<?php

Namespace Controller;

Class Log extends Database {
    public function __construct()
    {
        parent::construct();
    }

    public function addLog($userid, $event, $category, $status, $message) {
        
        $sql = "INSERT INTO `log` (`user_id`, `event`, `category`, `status`, `message`)
        VALUES (?),(?),(?),(?),(?)";
        parent::bufferParams($userid, $event, $category, $status, $message);

        parent::query($sql);
        return parent::getQueryOk();
}
    }
}