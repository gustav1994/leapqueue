<?php

namespace LeapQueue;

use PDO;
use LeapQueue\Migrations\Migrator;

class Manager
{   
    public function __construct( protected PDO $db, protected array $config = [] )    
    {   
        $databaseConfig = $config['database'] ?? [];        

        $migrator = new Migrator($this->db, $databaseConfig);

        // Will run the SQL migration regardless of the current state of the database,
        // so it should be idempotent and use "CREATE TABLE IF NOT EXISTS" and similar statements.
        //
        // @todo find a way to not run migrations on every instantiation of the Manager class, but only when necessary    
        $migrator->run();
    }

    public function push()
    {

    }

    public function pull()
    {

    }    
   
}
