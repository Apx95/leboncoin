

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `annonce_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `marque_id` int(11) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `etat` varchar(50) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `prix_negociable` tinyint(1) DEFAULT 0,
  `mode_remise` varchar(50) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `masquer_telephone` tinyint(1) DEFAULT 0,
  `statut` enum('en_attente','active','inactive','vendue') DEFAULT 'en_attente',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`annonce_id`, `utilisateur_id`, `categorie_id`, `marque_id`, `titre`, `description`, `etat`, `prix`, `prix_negociable`, `mode_remise`, `localisation`, `masquer_telephone`, `statut`, `date_creation`) VALUES
(1, 1, 18, 197, 'iphone16', '6,7″ ou 6,1″\r\n\r\nÉcran Super Retina XDR note de bas de page ¹\r\n\r\n— Pas de technologie ProMotion\r\n\r\n— Pas d’écran toujours activé\r\n\r\n\r\nAluminium avec dos en verre teinté dans la masse\r\n\r\nBouton Action\r\n\r\n\r\nConçu pour Apple Intelligence note de bas de page ⁸\r\n\r\n\r\nPuce A18\r\navec GPU 5 cœurs\r\n\r\n\r\nCommande de l’appareil photo\r\n\r\nUn accès plus rapide aux outils photo et vidéo\r\n\r\n\r\nSystème avancé à deux caméras\r\n\r\nFusion 48 Mpx | Ultra grand‑angle 12 Mpx\r\n\r\nPhotos super haute résolution\r\n(24 Mpx et 48 Mpx)\r\n\r\nPortraits nouvelle génération avec Mise au point et Contrôle de la profondeur\r\n\r\nPhoto macro\r\n\r\nDolby Vision jusqu’à 4K à 60 i/s\r\n\r\nPhotos et vidéos spatiales\r\n\r\nStyles photographiques dernière génération\r\n\r\nIntelligence visuelle, pour en savoir plus sur ce qui vous entoure\r\n\r\n\r\nOptions de zoom optique\r\n\r\n\r\nDynamic Island\r\n\r\nUne expérience magique sur iPhone\r\n\r\n\r\nAppel d’urgence\r\n\r\nSOS d’urgence par satellite note de bas de page ⁴\r\n\r\nDétection des accidents note de bas de page ⁵\r\n\r\n\r\nJusqu’à 27 heures de lecture vidéo note de bas de page ³\r\n\r\n\r\nUSB‑C\r\n\r\nPrise en charge d’USB 2\r\n\r\n\r\nFace ID', 'tres-bon', 969.00, 0, 'les-deux', 'paris', 0, 'active', '2025-04-26 13:18:35'),
(2, 1, 18, 197, 'iphone16', '6,7″ ou 6,1″\r\n\r\nÉcran Super Retina XDR note de bas de page ¹\r\n\r\n— Pas de technologie ProMotion\r\n\r\n— Pas d’écran toujours activé\r\n\r\n\r\nAluminium avec dos en verre teinté dans la masse\r\n\r\nBouton Action\r\n\r\n\r\nConçu pour Apple Intelligence note de bas de page ⁸\r\n\r\n\r\nPuce A18\r\navec GPU 5 cœurs\r\n\r\n\r\nCommande de l’appareil photo\r\n\r\nUn accès plus rapide aux outils photo et vidéo\r\n\r\n\r\nSystème avancé à deux caméras\r\n\r\nFusion 48 Mpx | Ultra grand‑angle 12 Mpx\r\n\r\nPhotos super haute résolution\r\n(24 Mpx et 48 Mpx)\r\n\r\nPortraits nouvelle génération avec Mise au point et Contrôle de la profondeur\r\n\r\nPhoto macro\r\n\r\nDolby Vision jusqu’à 4K à 60 i/s\r\n\r\nPhotos et vidéos spatiales\r\n\r\nStyles photographiques dernière génération\r\n\r\nIntelligence visuelle, pour en savoir plus sur ce qui vous entoure\r\n\r\n\r\nOptions de zoom optique\r\n\r\n\r\nDynamic Island\r\n\r\nUne expérience magique sur iPhone\r\n\r\n\r\nAppel d’urgence\r\n\r\nSOS d’urgence par satellite note de bas de page ⁴\r\n\r\nDétection des accidents note de bas de page ⁵\r\n\r\n\r\nJusqu’à 27 heures de lecture vidéo note de bas de page ³\r\n\r\n\r\nUSB‑C\r\n\r\nPrise en charge d’USB 2\r\n\r\n\r\nFace ID', 'tres-bon', 969.00, 0, 'les-deux', 'paris', 0, 'active', '2025-04-26 13:18:35'),
(10, 1, 13, 66, 'MacBook Pro 16 pouces - Noir sidéral', 'Écran Liquid Retina XDR 16 pouces²\r\nÉcran standard\r\nPuce Apple M4 Max avec CPU 16 cœurs, GPU 40 cœurs et Neural Engine 16 cœurs\r\n48 Go de mémoire unifiée\r\nSSD de 1 To\r\nAdaptateur secteur USB-C 140 W\r\nTrois ports Thunderbolt 5, port HDMI, fente pour carte SDXC, prise casque, port MagSafe 3\r\nMagic Keyboard rétroéclairé avec Touch ID - Français\r\n\r\nApple Intelligence Footnote ∆', 'neuf', 4424.00, 1, 'les-deux', 'paris', 0, 'active', '2025-04-26 23:37:52');

-- --------------------------------------------------------

--
-- Structure de la table `brouillons`
--

CREATE TABLE `brouillons` (
  `brouillon_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `marque_id` int(11) DEFAULT NULL,
  `etat` varchar(50) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `mode_remise` varchar(50) DEFAULT NULL,
  `prix_negociable` tinyint(1) DEFAULT 0,
  `masquer_telephone` tinyint(1) DEFAULT 0,
  `images` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_modification` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `categorie_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie_parent_id` int(11) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`categorie_id`, `nom`, `categorie_parent_id`, `icone`) VALUES
(1, 'Ordinateurs & Informatique', NULL, 'laptop'),
(2, 'Smartphones & Tablettes', NULL, 'phone'),
(3, 'TV & Home Cinéma', NULL, 'tv'),
(4, 'Audio & Son', NULL, 'music-note-list'),
(5, 'Gaming & Consoles', NULL, 'controller'),
(6, 'Photo & Vidéo', NULL, 'camera'),
(7, 'Gros Électroménager', NULL, 'houses'),
(8, 'Petit Électroménager', NULL, 'cup-hot'),
(9, 'Objets Connectés', NULL, 'smartwatch'),
(10, 'Autres Appareils', NULL, 'tools'),
(11, 'Moniteurs', 1, NULL),
(12, 'PC de Bureau', 1, NULL),
(13, 'PC Portables', 1, NULL),
(14, 'Périphériques', 1, NULL),
(15, 'Stockage', 1, NULL),
(16, 'Accessoires Mobiles', 2, NULL),
(17, 'Tablettes', 2, NULL),
(18, 'Smartphones', 2, NULL),
(19, 'Accessoires TV', 3, NULL),
(20, 'Home Cinéma', 3, NULL),
(21, 'TV ', 3, NULL),
(22, 'Vidéoprojecteurs', 3, NULL),
(23, 'Barres de Son', 4, NULL),
(24, 'Enceintes ', 4, NULL),
(25, 'Microphones', 4, NULL),
(26, 'Tables de Mixage', 4, NULL),
(27, 'Nintendo', 5, NULL),
(28, 'PlayStation', 5, NULL),
(29, 'Rétro Gaming', 5, NULL),
(30, 'Xbox', 5, NULL),
(31, 'Caméscope', 6, NULL),
(32, 'Caméras pro', 6, NULL),
(33, 'Éclairages', 6, NULL),
(34, 'Stabilisateurs', 6, NULL),
(35, 'Cuisinières', 7, NULL),
(36, 'Congélateurs', 7, NULL),
(37, 'Fours', 7, NULL),
(38, 'Lave-linge', 7, NULL),
(39, 'Lave-vaisselle', 7, NULL),
(40, 'Réfrigérateurs', 7, NULL),
(41, 'Aspirateurs', 8, NULL),
(42, 'Machines à Café', 8, NULL),
(43, 'Micro-ondes', 8, NULL),
(44, 'Petit déjeuner', 8, NULL),
(45, 'Robots Cuisine', 8, NULL),
(46, 'Caméras', 9, NULL),
(47, 'Domotique', 9, NULL),
(48, 'Montres Connectées', 9, NULL),
(49, 'Pièces Détachées', 10, NULL),
(50, 'autre materiel', 10, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `conversation_id` int(11) NOT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`conversation_id`, `annonce_id`, `date_creation`) VALUES
(1, 1, '2025-04-26 22:40:44'),
(2, 2, '2025-04-26 22:52:53');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `favori_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ordre` int(11) NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`image_id`, `annonce_id`, `url`, `ordre`, `date_ajout`) VALUES
(1, 1, '680cdd2b0a827_1745673515.webp', 0, '2025-04-26 13:18:35'),
(2, 1, '680cdd2b0ae24_1745673515.webp', 1, '2025-04-26 13:18:35'),
(3, 2, '680cdd2bcdd32_1745673515.webp', 0, '2025-04-26 13:18:35'),
(4, 2, '680cdd2bce782_1745673515.webp', 1, '2025-04-26 13:18:35'),
(19, 10, 'img_680d6e5003ed7_1745710672.jpg', 0, '2025-04-26 23:37:52'),
(20, 10, 'img_680d6e5004e2c_1745710672.jpg', 1, '2025-04-26 23:37:52'),
(21, 10, 'img_680d6e5005049_1745710672.jpg', 2, '2025-04-26 23:37:52');

-- --------------------------------------------------------

--
-- Structure de la table `marques`
--

CREATE TABLE `marques` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `marques`
--

INSERT INTO `marques` (`id`, `nom`, `categorie_id`, `date_creation`) VALUES
(1, 'Dell', 13, '2025-04-26 11:20:20'),
(2, 'HP', 13, '2025-04-26 11:20:20'),
(3, 'Lenovo', 13, '2025-04-26 11:20:20'),
(4, 'Asus', 13, '2025-04-26 11:20:20'),
(5, 'Acer', 13, '2025-04-26 11:20:20'),
(6, 'Apple', 13, '2025-04-26 11:20:20'),
(7, 'MSI', 13, '2025-04-26 11:20:20'),
(8, 'Razer', 13, '2025-04-26 11:20:20'),
(9, 'Microsoft', 13, '2025-04-26 11:20:20'),
(10, 'Huawei', 13, '2025-04-26 11:20:20'),
(16, 'Dell', 12, '2025-04-26 11:20:20'),
(17, 'HP', 12, '2025-04-26 11:20:20'),
(18, 'Lenovo', 12, '2025-04-26 11:20:20'),
(19, 'Asus', 12, '2025-04-26 11:20:20'),
(20, 'Apple', 12, '2025-04-26 11:20:20'),
(21, 'MSI', 12, '2025-04-26 11:20:20'),
(22, 'Corsair', 12, '2025-04-26 11:20:20'),
(23, 'NZXT', 12, '2025-04-26 11:20:20'),
(24, 'Acer', 12, '2025-04-26 11:20:20'),
(25, 'Alienware', 12, '2025-04-26 11:20:20'),
(31, 'Samsung', 11, '2025-04-26 11:20:20'),
(32, 'LG', 11, '2025-04-26 11:20:20'),
(33, 'Dell', 11, '2025-04-26 11:20:20'),
(34, 'Asus', 11, '2025-04-26 11:20:20'),
(35, 'BenQ', 11, '2025-04-26 11:20:20'),
(36, 'AOC', 11, '2025-04-26 11:20:20'),
(37, 'ViewSonic', 11, '2025-04-26 11:20:20'),
(38, 'Philips', 11, '2025-04-26 11:20:20'),
(39, 'MSI', 11, '2025-04-26 11:20:20'),
(40, 'Acer', 11, '2025-04-26 11:20:20'),
(46, 'Logitech', 14, '2025-04-26 11:20:20'),
(47, 'Razer', 14, '2025-04-26 11:20:20'),
(48, 'Corsair', 14, '2025-04-26 11:20:20'),
(49, 'SteelSeries', 14, '2025-04-26 11:20:20'),
(50, 'HyperX', 14, '2025-04-26 11:20:20'),
(51, 'Roccat', 14, '2025-04-26 11:20:20'),
(52, 'Microsoft', 14, '2025-04-26 11:20:20'),
(53, 'Asus ROG', 14, '2025-04-26 11:20:20'),
(54, 'HP', 14, '2025-04-26 11:20:20'),
(55, 'Trust', 14, '2025-04-26 11:20:20'),
(61, 'Dell', 13, '2025-04-26 11:21:17'),
(62, 'HP', 13, '2025-04-26 11:21:17'),
(63, 'Lenovo', 13, '2025-04-26 11:21:17'),
(64, 'Asus', 13, '2025-04-26 11:21:17'),
(65, 'Acer', 13, '2025-04-26 11:21:17'),
(66, 'Apple', 13, '2025-04-26 11:21:17'),
(67, 'MSI', 13, '2025-04-26 11:21:17'),
(68, 'Razer', 13, '2025-04-26 11:21:17'),
(69, 'Microsoft', 13, '2025-04-26 11:21:17'),
(70, 'Huawei', 13, '2025-04-26 11:21:17'),
(76, 'Dell', 12, '2025-04-26 11:21:17'),
(77, 'HP', 12, '2025-04-26 11:21:17'),
(78, 'Lenovo', 12, '2025-04-26 11:21:17'),
(79, 'Asus', 12, '2025-04-26 11:21:17'),
(80, 'Apple', 12, '2025-04-26 11:21:17'),
(81, 'MSI', 12, '2025-04-26 11:21:17'),
(82, 'Corsair', 12, '2025-04-26 11:21:17'),
(83, 'NZXT', 12, '2025-04-26 11:21:17'),
(84, 'Acer', 12, '2025-04-26 11:21:17'),
(85, 'Alienware', 12, '2025-04-26 11:21:17'),
(91, 'Samsung', 11, '2025-04-26 11:21:17'),
(92, 'LG', 11, '2025-04-26 11:21:17'),
(93, 'Dell', 11, '2025-04-26 11:21:17'),
(94, 'Asus', 11, '2025-04-26 11:21:17'),
(95, 'BenQ', 11, '2025-04-26 11:21:17'),
(96, 'AOC', 11, '2025-04-26 11:21:17'),
(97, 'ViewSonic', 11, '2025-04-26 11:21:17'),
(98, 'Philips', 11, '2025-04-26 11:21:17'),
(99, 'MSI', 11, '2025-04-26 11:21:17'),
(100, 'Acer', 11, '2025-04-26 11:21:17'),
(106, 'Logitech', 14, '2025-04-26 11:21:17'),
(107, 'Razer', 14, '2025-04-26 11:21:17'),
(108, 'Corsair', 14, '2025-04-26 11:21:17'),
(109, 'SteelSeries', 14, '2025-04-26 11:21:17'),
(110, 'HyperX', 14, '2025-04-26 11:21:17'),
(111, 'Roccat', 14, '2025-04-26 11:21:17'),
(112, 'Microsoft', 14, '2025-04-26 11:21:17'),
(113, 'Asus ROG', 14, '2025-04-26 11:21:17'),
(114, 'HP', 14, '2025-04-26 11:21:17'),
(115, 'Trust', 14, '2025-04-26 11:21:17'),
(121, 'Dell', 13, '2025-04-26 11:23:53'),
(122, 'HP', 13, '2025-04-26 11:23:53'),
(123, 'Lenovo', 13, '2025-04-26 11:23:53'),
(124, 'Asus', 13, '2025-04-26 11:23:53'),
(125, 'Acer', 13, '2025-04-26 11:23:53'),
(126, 'Apple', 13, '2025-04-26 11:23:53'),
(127, 'MSI', 13, '2025-04-26 11:23:53'),
(128, 'Razer', 13, '2025-04-26 11:23:53'),
(129, 'Microsoft', 13, '2025-04-26 11:23:53'),
(130, 'Huawei', 13, '2025-04-26 11:23:53'),
(136, 'Dell', 12, '2025-04-26 11:23:53'),
(137, 'HP', 12, '2025-04-26 11:23:53'),
(138, 'Lenovo', 12, '2025-04-26 11:23:53'),
(139, 'Asus', 12, '2025-04-26 11:23:53'),
(140, 'Apple', 12, '2025-04-26 11:23:53'),
(141, 'MSI', 12, '2025-04-26 11:23:53'),
(142, 'Corsair', 12, '2025-04-26 11:23:53'),
(143, 'NZXT', 12, '2025-04-26 11:23:53'),
(144, 'Acer', 12, '2025-04-26 11:23:53'),
(145, 'Alienware', 12, '2025-04-26 11:23:53'),
(151, 'Samsung', 11, '2025-04-26 11:23:53'),
(152, 'LG', 11, '2025-04-26 11:23:53'),
(153, 'Dell', 11, '2025-04-26 11:23:53'),
(154, 'Asus', 11, '2025-04-26 11:23:53'),
(155, 'BenQ', 11, '2025-04-26 11:23:53'),
(156, 'AOC', 11, '2025-04-26 11:23:53'),
(157, 'ViewSonic', 11, '2025-04-26 11:23:53'),
(158, 'Philips', 11, '2025-04-26 11:23:53'),
(159, 'MSI', 11, '2025-04-26 11:23:53'),
(160, 'Acer', 11, '2025-04-26 11:23:53'),
(166, 'Logitech', 14, '2025-04-26 11:23:53'),
(167, 'Razer', 14, '2025-04-26 11:23:53'),
(168, 'Corsair', 14, '2025-04-26 11:23:53'),
(169, 'SteelSeries', 14, '2025-04-26 11:23:53'),
(170, 'HyperX', 14, '2025-04-26 11:23:53'),
(171, 'Roccat', 14, '2025-04-26 11:23:53'),
(172, 'Microsoft', 14, '2025-04-26 11:23:53'),
(173, 'Asus ROG', 14, '2025-04-26 11:23:53'),
(174, 'HP', 14, '2025-04-26 11:23:53'),
(175, 'Trust', 14, '2025-04-26 11:23:53'),
(181, 'Samsung', 15, '2025-04-26 11:23:53'),
(182, 'Western Digital', 15, '2025-04-26 11:23:53'),
(183, 'Seagate', 15, '2025-04-26 11:23:53'),
(184, 'Crucial', 15, '2025-04-26 11:23:53'),
(185, 'Kingston', 15, '2025-04-26 11:23:53'),
(186, 'SanDisk', 15, '2025-04-26 11:23:53'),
(187, 'Corsair', 15, '2025-04-26 11:23:53'),
(188, 'Toshiba', 15, '2025-04-26 11:23:53'),
(189, 'LaCie', 15, '2025-04-26 11:23:53'),
(190, 'ADATA', 15, '2025-04-26 11:23:53'),
(196, 'Samsung', 18, '2025-04-26 11:25:00'),
(197, 'Apple ', 18, '2025-04-26 11:25:00'),
(198, 'Google Pixel', 18, '2025-04-26 11:25:00'),
(199, 'Xiaomi', 18, '2025-04-26 11:25:00'),
(200, 'OnePlus', 18, '2025-04-26 11:25:00'),
(201, 'OPPO', 18, '2025-04-26 11:25:00'),
(202, 'Vivo', 18, '2025-04-26 11:25:00'),
(203, 'Realme', 18, '2025-04-26 11:25:00'),
(204, 'Motorola', 18, '2025-04-26 11:25:00'),
(205, 'Sony', 18, '2025-04-26 11:25:00'),
(206, 'Nothing Phone', 18, '2025-04-26 11:25:00'),
(211, 'Samsung ', 17, '2025-04-26 11:25:00'),
(212, 'Apple ', 17, '2025-04-26 11:25:00'),
(213, 'Xiaomi ', 17, '2025-04-26 11:25:00'),
(214, 'Lenovo ', 17, '2025-04-26 11:25:00'),
(215, 'Huawei ', 17, '2025-04-26 11:25:00'),
(216, 'OPPO ', 17, '2025-04-26 11:25:00'),
(217, 'OnePlus ', 17, '2025-04-26 11:25:00'),
(218, 'Google Pixel ', 17, '2025-04-26 11:25:00'),
(219, 'Amazon ', 17, '2025-04-26 11:25:00'),
(226, 'Belkin', 16, '2025-04-26 11:25:00'),
(227, 'Spigen', 16, '2025-04-26 11:25:00'),
(228, 'Otterbox', 16, '2025-04-26 11:25:00'),
(229, 'Anker', 16, '2025-04-26 11:25:00'),
(230, 'Samsung', 16, '2025-04-26 11:25:00'),
(231, 'Apple', 16, '2025-04-26 11:25:00'),
(232, 'JBL', 16, '2025-04-26 11:25:00'),
(233, 'Sony', 16, '2025-04-26 11:25:00'),
(234, 'Jabra', 16, '2025-04-26 11:25:00'),
(235, 'ZAGG', 16, '2025-04-26 11:25:00'),
(241, 'Samsung', 21, '2025-04-26 11:25:42'),
(242, 'LG', 21, '2025-04-26 11:25:42'),
(243, 'Sony', 21, '2025-04-26 11:25:42'),
(244, 'Philips', 21, '2025-04-26 11:25:42'),
(245, 'TCL', 21, '2025-04-26 11:25:42'),
(246, 'Hisense', 21, '2025-04-26 11:25:42'),
(247, 'Panasonic', 21, '2025-04-26 11:25:42'),
(248, 'Sharp', 21, '2025-04-26 11:25:42'),
(249, 'Xiaomi', 21, '2025-04-26 11:25:42'),
(250, 'Thomson', 21, '2025-04-26 11:25:42'),
(256, 'Epson', 22, '2025-04-26 11:25:42'),
(257, 'BenQ', 22, '2025-04-26 11:25:42'),
(258, 'Optoma', 22, '2025-04-26 11:25:42'),
(259, 'ViewSonic', 22, '2025-04-26 11:25:42'),
(260, 'Sony', 22, '2025-04-26 11:25:42'),
(261, 'LG', 22, '2025-04-26 11:25:42'),
(262, 'Samsung', 22, '2025-04-26 11:25:42'),
(263, 'Acer', 22, '2025-04-26 11:25:42'),
(264, 'XGIMI', 22, '2025-04-26 11:25:42'),
(265, 'Philips', 22, '2025-04-26 11:25:42'),
(271, 'Sony', 20, '2025-04-26 11:25:42'),
(272, 'Samsung', 20, '2025-04-26 11:25:42'),
(273, 'LG', 20, '2025-04-26 11:25:42'),
(274, 'Bose', 20, '2025-04-26 11:25:42'),
(275, 'Yamaha', 20, '2025-04-26 11:25:42'),
(276, 'Denon', 20, '2025-04-26 11:25:42'),
(277, 'Onkyo', 20, '2025-04-26 11:25:42'),
(278, 'Pioneer', 20, '2025-04-26 11:25:42'),
(279, 'Marantz', 20, '2025-04-26 11:25:42'),
(280, 'Harman Kardon', 20, '2025-04-26 11:25:42'),
(286, 'One For All', 19, '2025-04-26 11:25:42'),
(287, 'Meliconi', 19, '2025-04-26 11:25:42'),
(288, 'Vogel\'s', 19, '2025-04-26 11:25:42'),
(289, 'Belkin', 19, '2025-04-26 11:25:42'),
(290, 'Monster Cable', 19, '2025-04-26 11:25:42'),
(291, 'AudioQuest', 19, '2025-04-26 11:25:42'),
(292, 'Roku', 19, '2025-04-26 11:25:42'),
(293, 'Amazon Fire TV', 19, '2025-04-26 11:25:42'),
(294, 'Google Chromecast', 19, '2025-04-26 11:25:42'),
(295, 'Apple TV', 19, '2025-04-26 11:25:42'),
(301, 'Sonos', 23, '2025-04-26 11:26:25'),
(302, 'Samsung', 23, '2025-04-26 11:26:25'),
(303, 'Bose', 23, '2025-04-26 11:26:25'),
(304, 'LG', 23, '2025-04-26 11:26:25'),
(305, 'Sony', 23, '2025-04-26 11:26:25'),
(306, 'JBL', 23, '2025-04-26 11:26:25'),
(307, 'Yamaha', 23, '2025-04-26 11:26:25'),
(308, 'Denon', 23, '2025-04-26 11:26:25'),
(309, 'Bang & Olufsen', 23, '2025-04-26 11:26:25'),
(310, 'Sennheiser', 23, '2025-04-26 11:26:25'),
(316, 'Bose', 24, '2025-04-26 11:26:25'),
(317, 'JBL', 24, '2025-04-26 11:26:25'),
(318, 'Sonos', 24, '2025-04-26 11:26:25'),
(319, 'Marshall', 24, '2025-04-26 11:26:25'),
(320, 'Bang & Olufsen', 24, '2025-04-26 11:26:25'),
(321, 'Klipsch', 24, '2025-04-26 11:26:25'),
(322, 'KEF', 24, '2025-04-26 11:26:25'),
(323, 'Focal', 24, '2025-04-26 11:26:25'),
(324, 'Harman Kardon', 24, '2025-04-26 11:26:25'),
(325, 'Ultimate Ears', 24, '2025-04-26 11:26:25'),
(331, 'Shure', 25, '2025-04-26 11:26:25'),
(332, 'Audio-Technica', 25, '2025-04-26 11:26:25'),
(333, 'Rode', 25, '2025-04-26 11:26:25'),
(334, 'Blue Microphones', 25, '2025-04-26 11:26:25'),
(335, 'Sennheiser', 25, '2025-04-26 11:26:25'),
(336, 'AKG', 25, '2025-04-26 11:26:25'),
(337, 'Neumann', 25, '2025-04-26 11:26:25'),
(338, 'Behringer', 25, '2025-04-26 11:26:25'),
(339, 'Lewitt', 25, '2025-04-26 11:26:25'),
(340, 'Warm Audio', 25, '2025-04-26 11:26:25'),
(346, 'Pioneer DJ', 26, '2025-04-26 11:26:25'),
(347, 'Allen & Heath', 26, '2025-04-26 11:26:25'),
(348, 'Native Instruments', 26, '2025-04-26 11:26:25'),
(349, 'Roland', 26, '2025-04-26 11:26:25'),
(350, 'Behringer', 26, '2025-04-26 11:26:25'),
(351, 'Denon DJ', 26, '2025-04-26 11:26:25'),
(352, 'Yamaha', 26, '2025-04-26 11:26:25'),
(353, 'Numark', 26, '2025-04-26 11:26:25'),
(354, 'Mackie', 26, '2025-04-26 11:26:25'),
(355, 'Soundcraft', 26, '2025-04-26 11:26:25'),
(361, 'Sony PS5', 28, '2025-04-26 11:26:56'),
(362, 'Sony PS4', 28, '2025-04-26 11:26:56'),
(363, 'Sony PS4 Pro', 28, '2025-04-26 11:26:56'),
(364, 'Sony PS VR', 28, '2025-04-26 11:26:56'),
(365, 'Sony PS VR2', 28, '2025-04-26 11:26:56'),
(366, 'Razer', 28, '2025-04-26 11:26:56'),
(367, 'Thrustmaster', 28, '2025-04-26 11:26:56'),
(368, 'Nacon', 28, '2025-04-26 11:26:56'),
(369, 'PowerA', 28, '2025-04-26 11:26:56'),
(370, 'Hori', 28, '2025-04-26 11:26:56'),
(376, 'Microsoft Xbox Series X', 30, '2025-04-26 11:26:56'),
(377, 'Microsoft Xbox Series S', 30, '2025-04-26 11:26:56'),
(378, 'Microsoft Xbox One X', 30, '2025-04-26 11:26:56'),
(379, 'Microsoft Xbox One S', 30, '2025-04-26 11:26:56'),
(380, 'Elite Controller', 30, '2025-04-26 11:26:56'),
(381, 'Razer', 30, '2025-04-26 11:26:56'),
(382, 'PowerA', 30, '2025-04-26 11:26:56'),
(383, 'PDP', 30, '2025-04-26 11:26:56'),
(384, 'Turtle Beach', 30, '2025-04-26 11:26:56'),
(385, 'Seagate', 30, '2025-04-26 11:26:56'),
(391, 'Nintendo Switch OLED', 27, '2025-04-26 11:26:56'),
(392, 'Nintendo Switch', 27, '2025-04-26 11:26:56'),
(393, 'Nintendo Switch Lite', 27, '2025-04-26 11:26:56'),
(394, 'Nintendo Pro Controller', 27, '2025-04-26 11:26:56'),
(395, 'PowerA', 27, '2025-04-26 11:26:56'),
(396, 'Hori', 27, '2025-04-26 11:26:56'),
(397, '8BitDo', 27, '2025-04-26 11:26:56'),
(398, 'SanDisk', 27, '2025-04-26 11:26:56'),
(399, 'PDP', 27, '2025-04-26 11:26:56'),
(400, 'Nyko', 27, '2025-04-26 11:26:56'),
(406, 'Nintendo NES', 29, '2025-04-26 11:26:56'),
(407, 'Super Nintendo', 29, '2025-04-26 11:26:56'),
(408, 'Nintendo 64', 29, '2025-04-26 11:26:56'),
(409, 'Nintendo GameCube', 29, '2025-04-26 11:26:56'),
(410, 'Sony PS1', 29, '2025-04-26 11:26:56'),
(411, 'Sony PS2', 29, '2025-04-26 11:26:56'),
(412, 'Sega MegaDrive', 29, '2025-04-26 11:26:56'),
(413, 'Sega Saturn', 29, '2025-04-26 11:26:56'),
(414, 'Atari', 29, '2025-04-26 11:26:56'),
(415, 'Neo Geo', 29, '2025-04-26 11:26:56'),
(421, 'Sony', 31, '2025-04-26 11:27:34'),
(422, 'Panasonic', 31, '2025-04-26 11:27:34'),
(423, 'Canon', 31, '2025-04-26 11:27:34'),
(424, 'JVC', 31, '2025-04-26 11:27:34'),
(425, 'Blackmagic Design', 31, '2025-04-26 11:27:34'),
(426, 'DJI', 31, '2025-04-26 11:27:34'),
(427, 'GoPro', 31, '2025-04-26 11:27:34'),
(428, 'RED', 31, '2025-04-26 11:27:34'),
(429, 'Z-CAM', 31, '2025-04-26 11:27:34'),
(430, 'ARRI', 31, '2025-04-26 11:27:34'),
(436, 'Sony Professional', 32, '2025-04-26 11:27:34'),
(437, 'Canon Cinema EOS', 32, '2025-04-26 11:27:34'),
(438, 'Blackmagic Design', 32, '2025-04-26 11:27:34'),
(439, 'RED Digital Cinema', 32, '2025-04-26 11:27:34'),
(440, 'ARRI', 32, '2025-04-26 11:27:34'),
(441, 'Panasonic Professional', 32, '2025-04-26 11:27:34'),
(442, 'Z CAM Professional', 32, '2025-04-26 11:27:34'),
(443, 'Kinefinity', 32, '2025-04-26 11:27:34'),
(444, 'Sharp 8K', 32, '2025-04-26 11:27:34'),
(445, 'AJA CION', 32, '2025-04-26 11:27:34'),
(451, 'Aputure', 33, '2025-04-26 11:27:34'),
(452, 'Godox', 33, '2025-04-26 11:27:34'),
(453, 'ARRI Lighting', 33, '2025-04-26 11:27:34'),
(454, 'Nanlite', 33, '2025-04-26 11:27:34'),
(455, 'Profoto', 33, '2025-04-26 11:27:34'),
(456, 'Broncolor', 33, '2025-04-26 11:27:34'),
(457, 'Rotolight', 33, '2025-04-26 11:27:34'),
(458, 'Westcott', 33, '2025-04-26 11:27:34'),
(459, 'Litepanels', 33, '2025-04-26 11:27:34'),
(460, 'Neewer', 33, '2025-04-26 11:27:34'),
(466, 'DJI', 34, '2025-04-26 11:27:34'),
(467, 'Zhiyun', 34, '2025-04-26 11:27:34'),
(468, 'FeiyuTech', 34, '2025-04-26 11:27:34'),
(469, 'Moza', 34, '2025-04-26 11:27:34'),
(470, 'Manfrotto', 34, '2025-04-26 11:27:34'),
(471, 'Glidecam', 34, '2025-04-26 11:27:34'),
(472, 'Tilta', 34, '2025-04-26 11:27:34'),
(473, 'SmallRig', 34, '2025-04-26 11:27:34'),
(474, 'Steadicam', 34, '2025-04-26 11:27:34'),
(475, 'Ikan', 34, '2025-04-26 11:27:34'),
(481, 'De Dietrich', 35, '2025-04-26 11:28:14'),
(482, 'Bosch', 35, '2025-04-26 11:28:14'),
(483, 'Siemens', 35, '2025-04-26 11:28:14'),
(484, 'Beko', 35, '2025-04-26 11:28:14'),
(485, 'Electrolux', 35, '2025-04-26 11:28:14'),
(486, 'Whirlpool', 35, '2025-04-26 11:28:14'),
(487, 'Samsung', 35, '2025-04-26 11:28:14'),
(488, 'LG', 35, '2025-04-26 11:28:14'),
(489, 'Candy', 35, '2025-04-26 11:28:14'),
(490, 'Faure', 35, '2025-04-26 11:28:14'),
(496, 'Liebherr', 36, '2025-04-26 11:28:14'),
(497, 'Bosch', 36, '2025-04-26 11:28:14'),
(498, 'Siemens', 36, '2025-04-26 11:28:14'),
(499, 'Beko', 36, '2025-04-26 11:28:14'),
(500, 'Whirlpool', 36, '2025-04-26 11:28:14'),
(501, 'Electrolux', 36, '2025-04-26 11:28:14'),
(502, 'Samsung', 36, '2025-04-26 11:28:14'),
(503, 'Haier', 36, '2025-04-26 11:28:14'),
(504, 'Candy', 36, '2025-04-26 11:28:14'),
(505, 'Hotpoint', 36, '2025-04-26 11:28:14'),
(511, 'Bosch', 37, '2025-04-26 11:28:14'),
(512, 'Siemens', 37, '2025-04-26 11:28:14'),
(513, 'Neff', 37, '2025-04-26 11:28:14'),
(514, 'Miele', 37, '2025-04-26 11:28:14'),
(515, 'Samsung', 37, '2025-04-26 11:28:14'),
(516, 'LG', 37, '2025-04-26 11:28:14'),
(517, 'Whirlpool', 37, '2025-04-26 11:28:14'),
(518, 'De Dietrich', 37, '2025-04-26 11:28:14'),
(519, 'Electrolux', 37, '2025-04-26 11:28:14'),
(520, 'AEG', 37, '2025-04-26 11:28:14'),
(526, 'Miele', 38, '2025-04-26 11:28:14'),
(527, 'Bosch', 38, '2025-04-26 11:28:14'),
(528, 'Siemens', 38, '2025-04-26 11:28:14'),
(529, 'Samsung', 38, '2025-04-26 11:28:14'),
(530, 'LG', 38, '2025-04-26 11:28:14'),
(531, 'Whirlpool', 38, '2025-04-26 11:28:14'),
(532, 'Electrolux', 38, '2025-04-26 11:28:14'),
(533, 'Beko', 38, '2025-04-26 11:28:14'),
(534, 'Candy', 38, '2025-04-26 11:28:14'),
(535, 'Panasonic', 38, '2025-04-26 11:28:14'),
(541, 'Bosch', 39, '2025-04-26 11:28:14'),
(542, 'Siemens', 39, '2025-04-26 11:28:14'),
(543, 'Miele', 39, '2025-04-26 11:28:14'),
(544, 'Whirlpool', 39, '2025-04-26 11:28:14'),
(545, 'Samsung', 39, '2025-04-26 11:28:14'),
(546, 'Beko', 39, '2025-04-26 11:28:14'),
(547, 'AEG', 39, '2025-04-26 11:28:14'),
(548, 'Electrolux', 39, '2025-04-26 11:28:14'),
(549, 'Candy', 39, '2025-04-26 11:28:14'),
(550, 'Hotpoint', 39, '2025-04-26 11:28:14'),
(556, 'Samsung', 40, '2025-04-26 11:28:14'),
(557, 'LG', 40, '2025-04-26 11:28:14'),
(558, 'Bosch', 40, '2025-04-26 11:28:14'),
(559, 'Siemens', 40, '2025-04-26 11:28:14'),
(560, 'Whirlpool', 40, '2025-04-26 11:28:14'),
(561, 'Liebherr', 40, '2025-04-26 11:28:14'),
(562, 'Beko', 40, '2025-04-26 11:28:14'),
(563, 'Electrolux', 40, '2025-04-26 11:28:14'),
(564, 'Haier', 40, '2025-04-26 11:28:14'),
(565, 'Candy', 40, '2025-04-26 11:28:14'),
(571, 'Dyson', 41, '2025-04-26 11:28:54'),
(572, 'Rowenta', 41, '2025-04-26 11:28:54'),
(573, 'Xiaomi', 41, '2025-04-26 11:28:54'),
(574, 'iRobot', 41, '2025-04-26 11:28:54'),
(575, 'Bosch', 41, '2025-04-26 11:28:54'),
(576, 'Miele', 41, '2025-04-26 11:28:54'),
(577, 'Philips', 41, '2025-04-26 11:28:54'),
(578, 'Electrolux', 41, '2025-04-26 11:28:54'),
(579, 'Samsung', 41, '2025-04-26 11:28:54'),
(580, 'Hoover', 41, '2025-04-26 11:28:54'),
(586, 'Nespresso', 42, '2025-04-26 11:28:54'),
(587, 'Delonghi', 42, '2025-04-26 11:28:54'),
(588, 'Philips', 42, '2025-04-26 11:28:54'),
(589, 'Krups', 42, '2025-04-26 11:28:54'),
(590, 'Jura', 42, '2025-04-26 11:28:54'),
(591, 'Saeco', 42, '2025-04-26 11:28:54'),
(592, 'Siemens', 42, '2025-04-26 11:28:54'),
(593, 'Melitta', 42, '2025-04-26 11:28:54'),
(594, 'Magimix', 42, '2025-04-26 11:28:54'),
(595, 'Sage', 42, '2025-04-26 11:28:54'),
(601, 'Samsung', 43, '2025-04-26 11:28:54'),
(602, 'LG', 43, '2025-04-26 11:28:54'),
(603, 'Panasonic', 43, '2025-04-26 11:28:54'),
(604, 'Whirlpool', 43, '2025-04-26 11:28:54'),
(605, 'Sharp', 43, '2025-04-26 11:28:54'),
(606, 'Bosch', 43, '2025-04-26 11:28:54'),
(607, 'Siemens', 43, '2025-04-26 11:28:54'),
(608, 'Beko', 43, '2025-04-26 11:28:54'),
(609, 'Candy', 43, '2025-04-26 11:28:54'),
(610, 'Electrolux', 43, '2025-04-26 11:28:54'),
(616, 'Philips', 44, '2025-04-26 11:28:54'),
(617, 'Moulinex', 44, '2025-04-26 11:28:54'),
(618, 'Tefal', 44, '2025-04-26 11:28:54'),
(619, 'Russell Hobbs', 44, '2025-04-26 11:28:54'),
(620, 'Krups', 44, '2025-04-26 11:28:54'),
(621, 'Braun', 44, '2025-04-26 11:28:54'),
(622, 'Delonghi', 44, '2025-04-26 11:28:54'),
(623, 'Bosch', 44, '2025-04-26 11:28:54'),
(624, 'Siemens', 44, '2025-04-26 11:28:54'),
(625, 'Severin', 44, '2025-04-26 11:28:54'),
(631, 'Thermomix', 45, '2025-04-26 11:28:54'),
(632, 'KitchenAid', 45, '2025-04-26 11:28:54'),
(633, 'Moulinex', 45, '2025-04-26 11:28:54'),
(634, 'Kenwood', 45, '2025-04-26 11:28:54'),
(635, 'Magimix', 45, '2025-04-26 11:28:54'),
(636, 'Bosch', 45, '2025-04-26 11:28:54'),
(637, 'Philips', 45, '2025-04-26 11:28:54'),
(638, 'Ninja', 45, '2025-04-26 11:28:54'),
(639, 'Cookeo', 45, '2025-04-26 11:28:54'),
(640, 'Companion', 45, '2025-04-26 11:28:54'),
(646, 'Ring', 46, '2025-04-26 11:29:09'),
(647, 'Arlo', 46, '2025-04-26 11:29:09'),
(648, 'Nest', 46, '2025-04-26 11:29:09'),
(649, 'Netatmo', 46, '2025-04-26 11:29:09'),
(650, 'Eufy', 46, '2025-04-26 11:29:09'),
(651, 'TP-Link', 46, '2025-04-26 11:29:09'),
(652, 'Xiaomi', 46, '2025-04-26 11:29:09'),
(653, 'Blink', 46, '2025-04-26 11:29:09'),
(654, 'Reolink', 46, '2025-04-26 11:29:09'),
(655, 'Foscam', 46, '2025-04-26 11:29:09'),
(661, 'Philips Hue', 47, '2025-04-26 11:29:09'),
(662, 'Google Nest', 47, '2025-04-26 11:29:09'),
(663, 'Amazon Alexa', 47, '2025-04-26 11:29:09'),
(664, 'Xiaomi', 47, '2025-04-26 11:29:09'),
(665, 'Legrand', 47, '2025-04-26 11:29:09'),
(666, 'Somfy', 47, '2025-04-26 11:29:09'),
(667, 'Tado', 47, '2025-04-26 11:29:09'),
(668, 'Fibaro', 47, '2025-04-26 11:29:09'),
(669, 'Shelly', 47, '2025-04-26 11:29:09'),
(670, 'TP-Link', 47, '2025-04-26 11:29:09'),
(676, 'Apple', 48, '2025-04-26 11:29:09'),
(677, 'Samsung', 48, '2025-04-26 11:29:09'),
(678, 'Garmin', 48, '2025-04-26 11:29:09'),
(679, 'Fitbit', 48, '2025-04-26 11:29:09'),
(680, 'Huawei', 48, '2025-04-26 11:29:09'),
(681, 'Xiaomi', 48, '2025-04-26 11:29:09'),
(682, 'Withings', 48, '2025-04-26 11:29:09'),
(683, 'Amazfit', 48, '2025-04-26 11:29:09'),
(684, 'Oppo', 48, '2025-04-26 11:29:09'),
(685, 'Honor', 48, '2025-04-26 11:29:09'),
(691, 'Samsung', 49, '2025-04-26 11:29:09'),
(692, 'Apple', 49, '2025-04-26 11:29:09'),
(693, 'LG', 49, '2025-04-26 11:29:09'),
(694, 'Sony', 49, '2025-04-26 11:29:09'),
(695, 'HP', 49, '2025-04-26 11:29:09'),
(696, 'Dell', 49, '2025-04-26 11:29:09'),
(697, 'Lenovo', 49, '2025-04-26 11:29:09'),
(698, 'Asus', 49, '2025-04-26 11:29:09'),
(699, 'Xiaomi', 49, '2025-04-26 11:29:09'),
(700, 'Huawei', 49, '2025-04-26 11:29:09');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `expediteur_id` int(11) DEFAULT NULL,
  `destinataire_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_envoi` datetime DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`message_id`, `conversation_id`, `expediteur_id`, `destinataire_id`, `message`, `date_envoi`, `lu`) VALUES
(2, 1, 1, 2, 'oui tu veux quoi', '2025-04-26 22:41:52', 1),
(3, 1, 1, 2, 'la marchandise tu l\'as ?', '2025-04-26 22:45:38', 1),
(5, 1, 2, 1, 'oui je l\'a', '2025-04-26 22:53:22', 1),
(6, 1, 2, 1, 'grand, la pomme c\'est combien', '2025-04-26 23:10:45', 1);

-- --------------------------------------------------------

--
-- Structure de la table `message_deletions`
--

CREATE TABLE `message_deletions` (
  `deletion_id` int(11) NOT NULL,
  `message_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `conversation_id` int(11) DEFAULT NULL,
  `deletion_date` datetime DEFAULT NULL,
  `message_content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message_deletions`
--

INSERT INTO `message_deletions` (`deletion_id`, `message_id`, `user_id`, `conversation_id`, `deletion_date`, `message_content`) VALUES
(1, 1, 2, 1, '2025-04-26 23:10:19', 'ounzfouoizjoairff'),
(2, 7, 2, 1, '2025-04-26 23:14:29', 'oui je l\'a'),
(3, 8, 2, 1, '2025-04-26 23:14:44', 'oui tu veux quoi'),
(4, 9, 2, 1, '2025-04-26 23:26:30', 'grand, la pomme c\'est combien');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `utilisateur_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `pseudo` varchar(50) NOT NULL,
  `role` enum('admin','utilisateur') DEFAULT 'utilisateur',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `email`, `mot_de_passe`, `telephone`, `pseudo`, `role`, `date_creation`, `derniere_connexion`, `localisation`) VALUES
(1, 'tralalerotralala@gmail.com', '$2y$10$bLDXdVgNBNv8Hl.p1JC/QOdi9dZiZf1alhJze4ce0Zzt.XI/Cbc5q', '0712345678', 'tralalerotralala', 'utilisateur', '2025-04-26 08:46:57', '2025-04-26 23:16:32', 'FRANCE'),
(2, 'lirililarila@gmail.com', '$2y$10$.SMpGZIaiFnDfNxKHgv2cOPcV.5z1/dRMSRoWMUoTCUdFPPkajklu', '0612345678', 'lirili larila', 'utilisateur', '2025-04-26 16:12:36', '2025-04-26 20:49:00', 'suisse');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`annonce_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `marque_id` (`marque_id`),
  ADD KEY `idx_annonces_statut` (`statut`);

--
-- Index pour la table `brouillons`
--
ALTER TABLE `brouillons`
  ADD PRIMARY KEY (`brouillon_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `marque_id` (`marque_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categorie_id`),
  ADD KEY `categorie_parent_id` (`categorie_parent_id`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `annonce_id` (`annonce_id`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`favori_id`),
  ADD UNIQUE KEY `unique_favori` (`utilisateur_id`,`annonce_id`),
  ADD KEY `annonce_id` (`annonce_id`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `idx_images_annonce` (`annonce_id`);

--
-- Index pour la table `marques`
--
ALTER TABLE `marques`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_marques_categorie` (`categorie_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`);

--
-- Index pour la table `message_deletions`
--
ALTER TABLE `message_deletions`
  ADD PRIMARY KEY (`deletion_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `annonce_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `brouillons`
--
ALTER TABLE `brouillons`
  MODIFY `brouillon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `categorie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `favori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `marques`
--
ALTER TABLE `marques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=707;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `message_deletions`
--
ALTER TABLE `message_deletions`
  MODIFY `deletion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `annonces_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`),
  ADD CONSTRAINT `annonces_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`categorie_id`),
  ADD CONSTRAINT `annonces_ibfk_3` FOREIGN KEY (`marque_id`) REFERENCES `marques` (`id`);

--
-- Contraintes pour la table `brouillons`
--
ALTER TABLE `brouillons`
  ADD CONSTRAINT `brouillons_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`),
  ADD CONSTRAINT `brouillons_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`categorie_id`),
  ADD CONSTRAINT `brouillons_ibfk_3` FOREIGN KEY (`marque_id`) REFERENCES `marques` (`id`);

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`categorie_parent_id`) REFERENCES `categories` (`categorie_id`);

--
-- Contraintes pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`annonce_id`);

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`),
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`annonce_id`);

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`annonce_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `marques`
--
ALTER TABLE `marques`
  ADD CONSTRAINT `marques_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`categorie_id`);

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`conversation_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`expediteur_id`) REFERENCES `utilisateurs` (`utilisateur_id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`destinataire_id`) REFERENCES `utilisateurs` (`utilisateur_id`);

--
-- Contraintes pour la table `message_deletions`
--
ALTER TABLE `message_deletions`
  ADD CONSTRAINT `message_deletions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`utilisateur_id`);
COMMIT;