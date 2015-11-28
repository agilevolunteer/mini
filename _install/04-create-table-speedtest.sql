CREATE TABLE mini.speedtest (
testId VARCHAR(32) NOT NULL,
testUrl VARCHAR(128),
fromBrowser VARCHAR(64),
completed DATETIME,
type VARCHAR(16),
loadTime VARCHAR(255),
TTFB VARCHAR(255),
requests VARCHAR(255),
speedIndex VARCHAR(255),
render VARCHAR(255),
visualComplete VARCHAR(255),
lastVisualChange VARCHAR(255),
firstPaint VARCHAR(255),
primary KEY (testId),
UNIQUE KEY (testId))
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
