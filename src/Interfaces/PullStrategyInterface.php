<?php

namespace LeapQueue\Interfaces;

use LeapQueue\Queue;

interface PullStrategyInterface
{
    /**
     * Return the maximum number of jobs to pull in one batch/transaction.
     * 
     * @return int
     */
    public function getMaxJobs() : int;

    /**
     * Queue is passed if we need to fetch some stratistics or 
     * other information about the queue to decide the selection criteria.
     * 
     * @param Queue $queue
     * @return array Example: ['where' => 'group_id = :group_id', 'params' => [':group_id' => 123]]
     */
    public function getSelectionCriteria(Queue $queue) : array;

}