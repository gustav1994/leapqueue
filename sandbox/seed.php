<?php

    use LeapQueue\Queue;
    
    require __DIR__ . '/../vendor/autoload.php';

    // Create PDO connection 
    $db = new PDO('mysql:host=mysql;dbname=leapqueue', 'root', 'root');

    // Boot the Queue class by providing configration and database connection
    Queue::boot([
        'database' => [
            'table_prefix' => 'leapqueue_'     
        ]
    ], $db);

    // In sandbox environment, we just migrate on every request
    Queue::migrateAll();

    // Check if the sandbox queue already exists, if not then we create it
    $sandboxQueue = Queue::find('code', 'sandbox');

    if( !$sandboxQueue ) {

        $sandboxQueue = new Queue();
        $sandboxQueue->code = 'sandbox';
        $sandboxQueue->save();

    }

    // Now we have a queue and we can start pushing jobs into it
    $jobs = [
        'job1',
        'job2',
        'job3',
    ];
    //$sandboxQueue->push($jobs, 'group1');
