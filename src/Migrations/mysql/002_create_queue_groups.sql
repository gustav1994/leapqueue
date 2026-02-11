-- Queues Group Table (First version)
--
-- We store groups in a separate table to assign each group a unique integer group_id.
-- This allows us to use group_id as a partition key in the jobs table, which is much more efficient
-- than partitioning on a string (group_code). Integer partition keys provide better performance,
-- lower storage overhead, and enable fast lookups and joins. This design also supports referential
-- integrity and makes it easy to manage group metadata and relationships between queues and groups.
CREATE TABLE IF NOT EXISTS {{prefix}}queue_groups (
    
    -- Column Definitions
    id BIGINT UNSIGNED AUTO_INCREMENT,
    queue_id SMALLINT UNSIGNED NOT NULL,
    code VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),    

    -- Indexes
    PRIMARY KEY (id),
    UNIQUE INDEX idx_unique (queue_id, code),
    
    -- Foreign Key Constraints
    FOREIGN KEY (queue_id) REFERENCES {{prefix}}queues(id) ON DELETE CASCADE

) ENGINE={{engine}} COMMENT='Table created and managed by LeapQueue'