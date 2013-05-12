BEGIN TRANSACTION;

CREATE TABLE session (
  id CHAR(32) PRIMARY KEY,
  data TEXT,
  accessed INT,
  created INT
);

CREATE INDEX session_index ON session (id, created, updated);

CREATE TABLE user (
  id VARCHAR(255) PRIMARY KEY,
  server_id VARCHAR(255),
  name VARCHAR(255),
  color1 INT,
  color2 INT,
  created INT,
  updated INT
);

CREATE INDEX user_index ON user (id, server_id, created, updated);

CREATE TABLE message (
  id VARCHAR(255) PRIMARY KEY,
  user_id VARCHAR(255),
  parent_id VARCHAR(255),
  server_id VARCHAR(255),
  file_id VARCHAR(255),
  content TEXT,
  created INT,
  updated INT
);

CREATE INDEX message_index ON message (id, user_id, parent_id, server_id, created, updated);

CREATE TABLE file (
  id VARCHAR(255) PRIMARY KEY,
  user_id VARCHAR(255),
  message_id VARCHAR(255),
  server_id VARCHAR(255),
  name VARCHAR(255),
  path VARCHAR(255),
  created INT,
  updated INT
);

CREATE INDEX file_index ON file (id, user_id, message_id, server_id, created, updated);

CREATE TABLE file_sync_upload (
  id VARCHAR(255) PRIMARY KEY,
  created INT
);

CREATE TABLE file_sync_upload_chunk (
  id VARCHAR(255) PRIMARY KEY,
  file_id VARCHAR(255),
  offset INT,
  data TEXT
);

CREATE TABLE meta (
  name VARCHAR(255) PRIMARY KEY,
  value TEXT
);

CREATE TABLE wispr (
  id VARCHAR(255) PRIMARY KEY,
  status VARCHAR(255) DEFAULT 'show-intro',
  created INT
);

COMMIT;
