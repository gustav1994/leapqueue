-- Jobs  Table (First version)
--
-- DENORMALIZATION:
-- 'queue_id' is intentionally duplicated from the 'groups' table. 
-- This avoids costly JOINs in the fetch-loop and enables single-table Index Seeks.
--
-- PARTITIONING:
-- Table is split into 31 partitions using HASH(group_id). 
-- Prime Number (31): Ensures a mathematically superior distribution of data. Parallelism: Allows
-- multiple workers to hit different physical storage segments
-- simultaneously, eliminating buffer pool and I/O contention at scale.
--
-- VIRTUAL LANES: Non-FIFO jobs should be assigned a random group_id (e.g., 1-1024) 
-- to leverage all 31 partitions and maximize throughput.
CREATE TABLE IF NOT EXISTS {{prefix}}jobs (
    
    -- Column Definitions
    id BIGINT UNSIGNED AUTO_INCREMENT,
    queue_id SMALLINT UNSIGNED NOT NULL,
    group_id BIGINT UNSIGNED NOT NULL,    
    job MEDIUMBLOB NOT NULL,
    available_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),

    -- Indexes
    PRIMARY KEY (id, group_id),
    INDEX idx_fifo_lookup (queue_id, group_id, available_at, created_at),    
    INDEX idx_velocity_lookup (queue_id, available_at, created_at)

    -- Foreign Key Constraints not available when using partitioning

) ENGINE={{engine}} COMMENT='Table created and managed by LeapQueue' PARTITION BY HASH(group_id) PARTITIONS 31