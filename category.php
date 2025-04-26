<?php
session_start();
require "db.php";

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    // Récupération de la catégorie
    $stmt = $db->prepare("
        SELECT c1.*, c2.nom as parent_nom 
        FROM categories c1 
        LEFT JOIN categories c2 ON c1.categorie_parent_id = c2.categorie_id 
        WHERE c1.categorie_id = ?
    ");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des sous-catégories
    $stmt = $db->prepare("SELECT * FROM categories WHERE categorie_parent_id = ?");
    $stmt->execute([$category_id]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des annonces
    $stmt = $db->prepare("
        SELECT a.*, 
               u.pseudo, 
               GROUP_CONCAT(DISTINCT i.url) as images,
               c.nom as categorie_nom,
               m.nom as marque_nom
        FROM annonces a
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        LEFT JOIN images i ON a.annonce_id = i.annonce_id
        LEFT JOIN categories c ON a.categorie_id = c.categorie_id
        LEFT JOIN marques m ON a.marque_id = m.id
        WHERE (a.categorie_id = ? 
           OR a.categorie_id IN (SELECT categorie_id FROM categories WHERE categorie_parent_id = ?))
        AND a.statut = 'active'
        GROUP BY a.annonce_id
        ORDER BY a.date_creation DESC
    ");
    $stmt->execute([$category_id, $category_id]);
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['nom']) ?> - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="accueil.php">Accueil</a></li>
                <?php if ($category['parent_nom']): ?>
                    <li class="breadcrumb-item"><a href="category.php?id=<?= $category['categorie_parent_id'] ?>"><?= htmlspecialchars($category['parent_nom']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars($category['nom']) ?></li>
            </ol>
        </nav>

        <h1 class="mb-4"><?= htmlspecialchars($category['nom']) ?></h1>

        <?php if (!empty($subcategories)): ?>
            <div class="row mb-4">
                <?php foreach($subcategories as $sub): ?>
                    <div class="col-md-3 mb-3">
                        <a href="category.php?id=<?= $sub['categorie_id'] ?>" class="text-decoration-none">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($sub['nom']) ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php foreach($annonces as $annonce): 
                $images = explode(',', $annonce['images']);
                $premiere_image = !empty($images[0]) ? 'uploads/' . $images[0] : 'images/default.jpg';
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($premiere_image) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($annonce['titre']) ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($annonce['titre']) ?></h5>
                            <p class="card-text">
                                Prix : <?= number_format($annonce['prix'], 2, ',', ' ') ?> €
                                <?php if($annonce['prix_negociable']): ?>
                                    <span class="badge bg-info">Négociable</span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Vendeur : <?= htmlspecialchars($annonce['pseudo']) ?><br>
                                    <?php if ($annonce['marque_nom']): ?>
                                        Marque : <?= htmlspecialchars($annonce['marque_nom']) ?><br>
                                    <?php endif; ?>
                                    Catégorie : <?= htmlspecialchars($annonce['categorie_nom']) ?>
                                </small>
                            </p>
                            <a href="voir_annonce.php?id=<?= $annonce['annonce_id'] ?>" 
                               class="btn btn-primary">Voir l'annonce</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>