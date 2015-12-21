CREATE TABLE teststatus (
testId VARCHAR(128) NOT NULL,
testUrl VARCHAR(128),
status INT(12),
primary KEY (testId),
UNIQUE KEY (testId))
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
