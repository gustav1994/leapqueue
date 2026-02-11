-- Queues Tabel (First version)
CREATE TABLE IF NOT EXISTS {{prefix}}queues (
    
    -- Column Definitions
    id SMALLINT UNSIGNED AUTO_INCREMENT,
    code VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
    created_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6),    
    
    -- Indexes
    PRIMARY KEY (id),
    UNIQUE INDEX idx_unique_code (code)

) ENGINE={{engine}} COMMENT='Table created and managed by LeapQueue'