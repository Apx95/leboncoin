-- Création de la base de données
CREATE DATABASE leboncoin;
USE leboncoin;

-- Création de la table des utilisateurs
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    location VARCHAR(255)
);

-- Création de la table des catégories
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_category_id INT DEFAULT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id)
);

-- Création de la table des annonces
CREATE TABLE ads (
    ad_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('active', 'inactive', 'sold') DEFAULT 'active',
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- Création de la table des images
CREATE TABLE images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    `order` INT NOT NULL,
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id) ON DELETE CASCADE
);

-- Création de la table des messages
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    ad_id INT NOT NULL,
    content TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id),
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id)
);

-- Création de la table des signalements
CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    user_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Création de la table des évaluations
CREATE TABLE ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    rating_value INT CHECK (rating_value BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (ad_id) REFERENCES ads(ad_id)
);

-- Création de la table des transactions
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

-- Création de la vue pour la recherche avancée
CREATE VIEW advanced_search AS
SELECT 
    a.ad_id,
    a.title,
    a.description,
    a.price,
    a.status,
    a.location,
    a.created_at,
    u.user_id,
    u.pseudo,
    u.email,
    c.name AS category_name
FROM 
    ads a
JOIN 
    users u ON a.user_id = u.user_id
JOIN 
    categories c ON a.category_id = c.category_id
WHERE 
    a.status = 'active';

-- Création de la vue pour l'activité des utilisateurs
CREATE VIEW user_activity_view AS
SELECT 
    u.user_id,
    u.pseudo,
    COUNT(DISTINCT a.ad_id) AS total_ads,
    COUNT(DISTINCT m.message_id) AS total_messages,
    MAX(a.created_at) AS last_ad_date,
    MAX(m.timestamp) AS last_message_date
FROM 
    users u
LEFT JOIN 
    ads a ON u.user_id = a.user_id
LEFT JOIN 
    messages m ON u.user_id = m.sender_id OR u.user_id = m.receiver_id
GROUP BY 
    u.user_id, u.pseudo;

-- Insertion de données d'exemple dans la table des utilisateurs
INSERT INTO users (email, password_hash, phone, pseudo, created_at, last_login, location) VALUES
('john.doe@example.com', 'hashed_password_1', '1234567890', 'johnny', NOW(), NOW(), 'New York'),
('jane.smith@example.com', 'hashed_password_2', '0987654321', 'janey', NOW(), NOW(), 'Los Angeles'),
('alice.johnson@example.com', 'hashed_password_3', '5555555555', 'alice', NOW(), NOW(), 'Chicago'),
('bob.brown@example.com', 'hashed_password_4', '4444444444', 'bobby', NOW(), NOW(), 'Houston'),
('charlie.davis@example.com', 'hashed_password_5', '3333333333', 'charlie', NOW(), NOW(), 'Phoenix');

-- Insertion de données d'exemple dans la table des annonces
INSERT INTO ads (user_id, category_id, title, description, price, status, location, created_at) VALUES
(1, 1, 'Vintage Bicycle', 'A classic vintage bicycle in great condition.', 150.00, 'active', 'Paris', NOW()),
(2, 2, 'Smartphone', 'Latest model smartphone with all accessories.', 600.00, 'active', 'Lyon', NOW()),
(3, 3, 'Dining Table', 'Solid wood dining table, seats 6.', 300.00, 'active', 'Marseille', NOW()),
(4, 1, 'Guitar', 'Acoustic guitar in excellent condition.', 200.00, 'active', 'Nice', NOW()),
(5, 2, 'Laptop', 'High-performance laptop for gaming and work.', 1200.00, 'active', 'Toulouse', NOW());

-- Insertion de données d'exemple dans la table des messages
INSERT INTO messages (sender_id, receiver_id, ad_id, content, timestamp) VALUES
(1, 2, 1, 'Is this item still available?', '2023-10-01 10:00:00'),
(2, 1, 1, 'Yes, it is! Would you like to come see it?', '2023-10-01 10:05:00'),
(3, 1, 2, 'Can you lower the price?', '2023-10-02 11:00:00'),
(1, 3, 2, 'Sorry, the price is firm.', '2023-10-02 11:10:00');