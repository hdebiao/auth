<?php

namespace Hdb\Auth;

class Auth
{
    public $db;
    public $dbConfig;

    public function __construct($config)
    {
        self::connectDatabase($config);
    }

    public function connectDatabase($config)
    {

        if ($this->db === null) {
            $this->db =  '';
        }
        
        return $this->db;
    }


    public function add()
    {

    }



    public function check()
    {

    }

    public function delete()
    {

    }


}