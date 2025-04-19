CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'canceled') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);