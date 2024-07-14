CREATE TABLE IF NOT EXISTS booking_details(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(150) NOT NULL,
    last_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    cell_number VARCHAR(150) NOT NULL,
    booking_id VARCHAR(150) NOT NULL,
    created_at VARCHAR(100) NOT NULL,
    INDEX(booking_id)
);