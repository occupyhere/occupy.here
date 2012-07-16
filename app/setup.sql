BEGIN TRANSACTION;

CREATE TABLE user (
  id VARCHAR(255) PRIMARY KEY,
  server_id VARCHAR(255),
  name VARCHAR(255),
  bio TEXT,
  created INT,
  updated INT
);

CREATE TABLE message (
  id VARCHAR(255) PRIMARY KEY,
  user_id VARCHAR(255),
  parent_id VARCHAR(255),
  server_id VARCHAR(255),
  content TEXT,
  location VARCHAR(255),
  latlng VARCHAR(255),
  created INT,
  updated INT
);

CREATE TABLE file (
  id VARCHAR(255) PRIMARY KEY,
  user_id VARCHAR(255),
  server_id VARCHAR(255),
  name VARCHAR(255),
  type VARCHAR(255),
  path VARCHAR(255),
  original VARCHAR(255),
  location VARCHAR(255),
  latlng VARCHAR(255),
  created INT,
  updated INT
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
