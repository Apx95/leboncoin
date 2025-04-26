<!-- filepath: c:\xampp\htdocs\leboncoin\index.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroBazar - Accueil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Fichier CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <style>
        /* Styles pour le bouton "Déposer une annonce" */
        .btn-banner {
            background-color: #007BFF;
            color: #FFFFFF;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1.2rem;
            font-weight: bold;
            border: none;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-banner:hover {
            background-color: #0056b3;
            text-decoration: none;
        }

        /* Styles pour le titre "Tendance en ce moment" */
        .section-title {
            font-size: 2rem;
            font-weight: bold;
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background-color: #007BFF;
            margin: 10px auto 0;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar des catégories -->
            <aside class="col-lg-3 col-md-4 sidebar">
                <h3 class="sidebar-title">Catégories</h3>
                <ul class="list-group">
                    <?php
                    try {
                        require_once 'db.php';
                        // Récupérer les catégories principales
                        $stmt = $db->query("
                            SELECT c.categorie_id, c.nom, c.icone,
                                   COUNT(a.annonce_id) as nb_annonces
                            FROM categories c
                            LEFT JOIN annonces a ON a.categorie_id = c.categorie_id
                            WHERE c.categorie_parent_id IS NULL
                            GROUP BY c.categorie_id
                            ORDER BY c.nom
                        ");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        foreach ($categories as $categorie) {
                            $icon = $categorie['icone'] ?? 'grid';
                            $nbAnnonces = $categorie['nb_annonces'] > 0 ? 
                                "<span class='badge bg-primary float-end'>{$categorie['nb_annonces']}</span>" : '';
                            
                            echo "<li class='list-group-item'>";
                            echo "<a href='category.php?id={$categorie['categorie_id']}' 
                                     class='d-flex justify-content-between align-items-center text-decoration-none'>";
                            echo "<span><i class='bi bi-{$icon} me-2'></i>" . 
                                 htmlspecialchars($categorie['nom']) . "</span>";
                            echo $nbAnnonces;
                            echo "</a></li>";
                        }
                    } catch(PDOException $e) {
                        echo "<li class='list-group-item text-danger'>Erreur de chargement des catégories</li>";
                    }
                    ?>
                </ul>
            </aside>

            <!-- Contenu principal -->
            <main class="col-lg-9 col-md-8">
                <!-- Bannière centrale -->
                <section class="banner text-center">
                    <h2>Trouvez votre prochain appareil électronique au meilleur prix</h2>
                    <a href="Depot_annonce.php" class="btn-banner"><i class="bi bi-plus-circle me-2"></i>Déposer une annonce</a>
                </section>

                <!-- Section Tendance -->
                <section class="trending">
                    <h2 class="section-title">Tendance en ce moment</h2>
                    <div class="row g-4">
                        <?php
                        // Exemple de produits dynamiques
                        $products = [
                            ["image" => "images/product1.jpg", "title" => "Smartphone Galaxy S21", "price" => "750 €", "badge" => "Nouveau"],
                            ["image" => "images/product2.jpg", "title" => "Ordinateur Portable HP", "price" => "1200 €", "badge" => "Promo"],
                            ["image" => "images/product3.jpg", "title" => "Tablette iPad Pro", "price" => "950 €"],
                            ["image" => "images/product4.jpg", "title" => "Casque Audio Bose", "price" => "300 €"]
                        ];

                        foreach ($products as $product) {
                            echo "
                            <div class='col-lg-4 col-md-6'>
                                <div class='card product-card'>
                                    <img src='{$product['image']}' class='card-img-top' alt='{$product['title']}'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>{$product['title']}</h5>
                                        <p class='card-text'>Prix : {$product['price']}</p>
                                        <i class='bi bi-lightning-charge-fill text-primary'></i>
                                        " . (isset($product['badge']) ? "<span class='badge bg-success'>{$product['badge']}</span>" : "") . "
                                    </div>
                                </div>
                            </div>
                            ";
                        }
                        ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center">
        <p>&copy; 2025 ElectroBazar. Tous droits réservés.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>