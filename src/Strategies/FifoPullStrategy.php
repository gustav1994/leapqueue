<?php

namespace LeapQueue\Strategies;

use LeapQueue\Job;
use LeapQueue\Queue;

class FifoPullStrategy extends BasePullStrategy
{
    /**
     * In Fifo we take the oldest first
     * 
     * @returns string
     */
    public function getOrderBySql(): string
    {
        return 'job.available_at ASC';
    }

    /**
     * We need a special EXISTS condition that ensures that no other
     * job from the same group is taken out of the queue (and not processed).
     * 
     * @param Queue $queue
     * @returns array
     */
    public function selectionCriteria(Queue $queue) : array
    {
        $fifoCondition = '
            NOT EXISTS (
                SELECT 1
                FROM '. Job::getTableName() .' AS job2
                WHERE                    
                    job.group_id = job2.group_id
                    AND job2.available_at < job.available_at    
            )
        ';

        return [
            'where' => [$fifoCondition],
            'bindings' => []
        ];
    }
}