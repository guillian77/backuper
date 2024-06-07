CREATE TABLE IF NOT EXISTS backup_history(
    id INTEGER PRIMARY KEY,
    started_at DATETIME NOT NULL,
    finished_at DATETIME,
    run_type TEXT NOT NULL,
    backup_number TEXT,
    purged_number TEXT,
    target_number INT,
    status TEXT NOT NULL
);
