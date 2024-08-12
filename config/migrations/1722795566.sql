CREATE TABLE IF NOT EXISTS event_details(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    loaction VARCHAR(255) NOT NULL,
    slots INT NOT NULL,
    price FLOAT NOT NULL,
    event_id INT NOT NULL,
    pre_payment BOOLEAN NOT NULL DEFAULT false,
    created_at VARCHAR(100) NOT NULL,
    FOREIGN KEY(event_id)
    REFERENCES events(id)
);