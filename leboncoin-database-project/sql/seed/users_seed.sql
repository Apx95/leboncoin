INSERT INTO users (email, password_hash, phone, pseudo, created_at, last_login, location) VALUES
('john.doe@example.com', 'hashed_password_1', '1234567890', 'johnny', NOW(), NOW(), 'New York'),
('jane.smith@example.com', 'hashed_password_2', '0987654321', 'janey', NOW(), NOW(), 'Los Angeles'),
('alice.johnson@example.com', 'hashed_password_3', '5555555555', 'alice', NOW(), NOW(), 'Chicago'),
('bob.brown@example.com', 'hashed_password_4', '4444444444', 'bobby', NOW(), NOW(), 'Houston'),
('charlie.davis@example.com', 'hashed_password_5', '3333333333', 'charlie', NOW(), NOW(), 'Phoenix');