-- Création de la base de données
DROP DATABASE IF EXISTS electrobazar;
CREATE DATABASE electrobazar;
USE electrobazar;

-- Création de la table des utilisateurs
CREATE TABLE utilisateurs (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(15),
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    role ENUM('admin', 'utilisateur') DEFAULT 'utilisateur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    localisation VARCHAR(255)
);

-- Création de la table des catégories
CREATE TABLE categories (
    categorie_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    categorie_parent_id INT DEFAULT NULL,
    FOREIGN KEY (categorie_parent_id) REFERENCES categories(categorie_id)
);

-- Création de la table des marques
CREATE TABLE marques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    categorie_id INT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(categorie_id)
);

-- Création de la table des annonces
CREATE TABLE annonces (
    annonce_id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    categorie_id INT NOT NULL,
    marque_id INT,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    etat VARCHAR(50) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    prix_negociable TINYINT(1) DEFAULT 0,
    mode_remise VARCHAR(50),
    localisation VARCHAR(255),
    masquer_telephone TINYINT(1) DEFAULT 0,
    statut ENUM('en_attente', 'active', 'inactive', 'vendue') DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (categorie_id) REFERENCES categories(categorie_id),
    FOREIGN KEY (marque_id) REFERENCES marques(id)
);

-- Création de la table des images
CREATE TABLE images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    annonce_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    ordre INT NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (annonce_id) REFERENCES annonces(annonce_id) ON DELETE CASCADE
);

-- Création de la table des messages
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    annonce_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(utilisateur_id),
    FOREIGN KEY (annonce_id) REFERENCES annonces(annonce_id)
);

-- Insertion des catégories de base
INSERT INTO categories (nom) VALUES 
('Smartphones'),
('Ordinateurs'),
('Tablettes'),
('Téléviseurs'),
('Audio'),
('Accessoires');

-- Insertion des marques de base
INSERT INTO marques (nom, categorie_id) VALUES
-- Smartphones
('Apple', 1),
('Samsung', 1),
('Xiaomi', 1),
('Huawei', 1),
('OnePlus', 1),
-- Ordinateurs
('HP', 2),
('Dell', 2),
('Lenovo', 2),
('Asus', 2),
('Acer', 2),
-- Tablettes
('iPad', 3),
('Galaxy Tab', 3),
('Surface', 3),
-- Téléviseurs
('LG', 4),
('Sony', 4),
('Philips', 4),
('Samsung TV', 4),
-- Audio
('Bose', 5),
('JBL', 5),
('Sony Audio', 5),
('Sennheiser', 5),
-- Accessoires
('Logitech', 6),
('Belkin', 6),
('Razer', 6);

-- Création des vues
CREATE VIEW recherche_avancee AS
SELECT 
    a.annonce_id,
    a.titre,
    a.description,
    a.prix,
    a.prix_negociable,
    a.etat,
    a.mode_remise,
    a.statut,
    a.localisation,
    a.date_creation,
    u.utilisateur_id,
    u.pseudo,
    u.email,
    c.nom AS categorie_nom,
    m.nom AS marque_nom,
    GROUP_CONCAT(i.url) AS images
FROM 
    annonces a
JOIN 
    utilisateurs u ON a.utilisateur_id = u.utilisateur_id
JOIN 
    categories c ON a.categorie_id = c.categorie_id
LEFT JOIN 
    marques m ON a.marque_id = m.id
LEFT JOIN 
    images i ON a.annonce_id = i.annonce_id
WHERE 
    a.statut = 'active'
GROUP BY 
    a.annonce_id;

-- Index pour améliorer les performances
CREATE INDEX idx_annonces_statut ON annonces(statut);
CREATE INDEX idx_images_annonce ON images(annonce_id);
CREATE INDEX idx_marques_categorie ON marques(categorie_id);