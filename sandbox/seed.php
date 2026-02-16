<?php

    use LeapQueue\Job;
    use LeapQueue\Queue;
    use LeapQueue\Strategies\VelocityPullStrategy;
    
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
    $sandboxQueue = Queue::find(['code' => 'sandbox']);    

    if( !$sandboxQueue ) {

        $sandboxQueue = new Queue();
        $sandboxQueue->code = 'sandbox';
        $sandboxQueue->save();

    }

    // Now we have a queue and we can start pushing jobs into it
    for( $i = 0; $i < 10; $i++ ) {
        $job = new Job();
        $job->job = "this is my random job data {$i}";

        var_dump($sandboxQueue->push($job));
    }

    // Let's see if we can delete the last job
    echo "Deleting the last job (#{$job->id}) in group {$job->group_id}";
    var_dump($job->remove());

    // Let's pull out jobs from the queue with the most simple strategy: velocotiy
    $velocityStrategy = new VelocityPullStrategy();

    $jobs = $sandboxQueue->pull($velocityStrategy);

    echo 'Pulled out ' . count($jobs) . ' jobs ('. implode(',', array_map(fn($job) => $job->id, $jobs)) .') from the queue using velocity strategy' . PHP_EOL;