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