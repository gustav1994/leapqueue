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
     * We need to know if the strategy want the jobs to be sorted
     * in a special way.
     * 
     * @return string
     */
    public function getOrderBySql() : string;

    /**
     * Queue is passed if we need to fetch some stratistics or 
     * other information about the queue to decide the selection criteria.
     * 
     * @param Queue $queue
     * @return array [
     *      'where' => [
     *          ['Condition 1'],
     *          ['Condition 2'],
     *      ],
     *      'bindings' => [':param' => 'value']
     * ]
     */
    public function selectionCriteria(Queue $queue) : array;

    /**
     * After the jobs are selected but before they are locked we can do some filtering 
     * if they should not be processed anyways and therefore not locked. 
     * 
     * @param array $jobs
     * @return array
     */
    public function filterJobs( array $jobs ) : array;

}