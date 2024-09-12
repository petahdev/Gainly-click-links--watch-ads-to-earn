CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    mobilenumber VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE );


CREATE TABLE funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    balance DECIMAL(10, 2) DEFAULT '0.00',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    image_data LONGBLOB NOT NULL
);


CREATE TABLE link_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    link_url VARCHAR(255),
    click_count INT DEFAULT 0,
    last_click_time DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

