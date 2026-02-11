CREATE TABLE IF NOT EXISTS {{prefix}}jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT,
    queue_id SMALLINT UNSIGNED NOT NULL,
    group_code VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
    job MEDIUMBLOB NOT NULL,
    available_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),

    -- Indexes
    PRIMARY KEY (id),
    INDEX idx_grouped_fifo (queue_id, group_code, available_at, created_at),
    INDEX idx_velocity (queue_id, available_at, created_at),

    -- Foreign key constraint
    FOREIGN KEY (queue_id) REFERENCES {{prefix}}queues(id) ON DELETE CASCADE
)