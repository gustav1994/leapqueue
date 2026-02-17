<?php

namespace LeapQueue\Strategies;

use Exception;
use LeapQueue\Queue;
use LeapQueue\Interfaces\PullStrategyInterface;

class BasePullStrategy implements PullStrategyInterface
{
    /**
     * Property assignment
     * 
     * @param int $maxJobs
     */
    public function __construct(protected int $maxJobs = 1)
    {

    }

    /**
     * Create order by statement that is SQL compatible
     * 
     * @return string
     */
    public function getOrderBySql() : string
    {
        return '';
    }

    /**
     * Return the maximum number of jobs to pull from the queue (and lock) in one batch
     * 
     * @returns int
     */
    public function getMaxJobs() : int
    {
        return $this->maxJobs;
    }

    /**
     * Default is to return no conditions, so 
     * 
     * @param Queue $queue
     * @returns array
     */
    public function selectionCriteria(Queue $queue) : array
    {
        return [
            'where' => [],
            'bindings' => []
        ];
    }

    /**
     * The sub-class strategies can override this method, but the default
     * is to not filter anything and just return input
     * 
     * @param array $jobs
     * @return array
     */
    public function filterJobs( array $jobs ) : array
    {
        return $jobs;
    }

}