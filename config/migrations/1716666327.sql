CREATE DATABASE IF NOT EXISTS jeeves;

USE jeeves;

CREATE TABLE IF NOT EXISTS events (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description LONGTEXT,
    created_at VARCHAR(100) NOT NULL,
    user_id INT,
    INDEX(user_id)
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    user_id INT,
    start_at VARCHAR(100),
    end_at VARCHAR(100),
    INDEX(user_id, event_id),
    FOREIGN KEY(event_id)
    REFERENCES events(id)
);

/*
---- TESTING ----
INSERT INTo events VALUES(NULL,'test-21','this is a test','1720960422',1),(NULL,'test-22','this is a test','1720960422',1),
(NULL,'test-23','this is a test','1720960422',1),(NULL,'test-24','this is a test','1720960422',1),(NULL,'test-25','this is a test','1720960422',1),
(NULL,'test-33','this is a test','1720960422',1),(NULL,'test-34','this is a test','1720960422',1),(NULL,'test-35','this is a test','1720960422',1),
(NULL,'test-43','this is a test','1720960422',1),(NULL,'test-44','this is a test','1720960422',1),(NULL,'test-45','this is a test','1720960422',1),
(NULL,'test-26','this is a test','1720960422',1),(NULL,'test-27','this is a test','1720960422',1),(NULL,'test-28','this is a test','1720960422',1);
*/