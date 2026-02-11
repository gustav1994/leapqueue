<?php

namespace LeapQueue;

use PDO;
use LeapQueue\Migrations\Migrator;

class Manager
{   
    public function __construct( protected PDO $db )
    {                
        $migrator = new Migrator($this->db);

        $migrator->run(['prefix' => 'leap_']);
    }

    public function push()
    {

    }

    public function pull()
    {

    }    
   
}
