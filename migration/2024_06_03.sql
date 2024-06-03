DROP TABLE IF EXISTS directory;
CREATE TABLE directory(
    id INTEGER PRIMARY KEY ASC,
    path TEXT NOT NULL,
    type TEXT NOT NULL
);

DROP TABLE IF EXISTS configuration;
CREATE TABLE configuration(
    key TEXT NOT NULL PRIMARY KEY UNIQUE,
    value TEXT NOT NULL
);

INSERT INTO configuration (key, value) VALUES ('backup_enabled', false);
INSERT INTO configuration (key, value) VALUES ('purge_enabled', true);
INSERT INTO configuration (key, value) VALUES ('retention_days', 3);
INSERT INTO configuration (key, value) VALUES ('schedule', 'custom');
