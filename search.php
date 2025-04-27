<?php
session_start();
require 'db.php';

// Récupération des paramètres de recherche
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

try {
    // Construction de la requête de base
    $sql = "
        SELECT a.*, c.nom as categorie_nom, u.pseudo as vendeur, 
               (SELECT url FROM images WHERE annonce_id = a.annonce_id LIMIT 1) as image_url
        FROM annonces a
        JOIN categories c ON a.categorie_id = c.categorie_id
        JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        WHERE a.statut = 'active'
    ";
    $params = [];

    // Ajout des conditions de recherche
    if ($keyword) {
        $sql .= " AND (a.titre LIKE ? OR a.description LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    if ($category) {
        $sql .= " AND a.categorie_id = ?";
        $params[] = $category;
    }

    if ($minPrice !== null) {
        $sql .= " AND a.prix >= ?";
        $params[] = $minPrice;
    }

    if ($maxPrice !== null) {
        $sql .= " AND a.prix <= ?";
        $params[] = $maxPrice;
    }

    // Tri des résultats
    $sql .= match ($sort) {
        'price_asc' => " ORDER BY a.prix ASC",
        'price_desc' => " ORDER BY a.prix DESC",
        'date_asc' => " ORDER BY a.date_creation ASC",
        default => " ORDER BY a.date_creation DESC"
    };

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log("Erreur de recherche: " . $e->getMessage());
    $error = "Une erreur est survenue lors de la recherche.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .search-filters {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .result-card {
            transition: transform 0.2s;
        }
        .result-card:hover {
            transform: translateY(-5px);
        }
        .no-results {
            text-align: center;
            padding: 50px;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <!-- Filtres de recherche -->
        <div class="search-filters">
            <form action="search.php" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="keyword" 
                           value="<?= htmlspecialchars($keyword) ?>" 
                           placeholder="Mot-clé...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">Toutes les catégories</option>
                        <?php
                        $cats = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
                        foreach($cats as $cat):
                        ?>
                        <option value="<?= $cat['categorie_id'] ?>" 
                                <?= $category == $cat['categorie_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="min_price" 
                           value="<?= $minPrice ?>" placeholder="Prix min">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="max_price" 
                           value="<?= $maxPrice ?>" placeholder="Prix max">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats -->
        <div class="row">
            <?php if (empty($results)): ?>
                <div class="col-12 no-results">
                    <i class="bi bi-emoji-frown display-1 text-muted"></i>
                    <h3 class="mt-3">Aucun résultat trouvé</h3>
                    <p class="text-muted">Essayez avec d'autres critères de recherche</p>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 result-card">
                            <img src="uploads/<?= htmlspecialchars($result['image_url'] ?? 'default.jpg') ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($result['titre']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($result['titre']) ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?= htmlspecialchars($result['categorie_nom']) ?>
                                    </small>
                                    <br>
                                    Prix : <?= number_format($result['prix'], 2, ',', ' ') ?> €
                                    <br>
                                    <small class="text-muted">
                                        Vendeur : <?= htmlspecialchars($result['vendeur']) ?>
                                    </small>
                                </p>
                                <a href="voir_annonce.php?id=<?= $result['annonce_id'] ?>" 
                                   class="btn btn-primary">
                                    Voir l'annonce
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation de chargement
        document.querySelector('form').addEventListener('submit', function() {
            this.querySelector('button[type="submit"]').disabled = true;
            this.querySelector('button[type="submit"]').innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Recherche...
            `;
        });
    </script>
</body>
</html>