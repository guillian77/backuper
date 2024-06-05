DROP TABLE IF EXISTS directory;
CREATE TABLE directory(
    id INTEGER PRIMARY KEY,
    path TEXT NOT NULL,
    type TEXT NOT NULL
);

DROP TABLE IF EXISTS configuration;
CREATE TABLE configuration(
    id INTEGER NOT NULL,
    backup_enabled INTEGER NOT NULL DEFAULT true,
    purge_enabled INTEGER NOT NULL DEFAULT true,
    encrypt_enabled INTEGER NOT NULL DEFAULT false,
    encryption_key TEXT,
    retention_days INTEGER NOT NULL DEFAULT 7,
    schedule_type TEXT NOT NULL DEFAULT 'daily',
    constraint PK_T1 PRIMARY KEY (id),
    constraint CK_T1_Locked CHECK (id='1')
);

INSERT INTO configuration (backup_enabled) VALUES (true);
