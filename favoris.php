<?php
session_start();
require 'db.php';

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Get favorites with all listing information
    $stmt = $db->prepare("
        SELECT a.*, 
               u.pseudo,
               GROUP_CONCAT(DISTINCT i.url) as images,
               c.nom as categorie_nom,
               m.nom as marque_nom,
               f.date_ajout as date_favoris
        FROM favoris f
        JOIN annonces a ON f.annonce_id = a.annonce_id
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        LEFT JOIN images i ON a.annonce_id = i.annonce_id
        LEFT JOIN categories c ON a.categorie_id = c.categorie_id
        LEFT JOIN marques m ON a.marque_id = m.id
        WHERE f.utilisateur_id = ?
        GROUP BY a.annonce_id
        ORDER BY f.date_ajout DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log($e->getMessage());
    $error = "Une erreur est survenue lors de la récupération des favoris";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mes Favoris</h2>
            <a href="accueil.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (empty($favoris)): ?>
            <div class="alert alert-info">
                Vous n'avez pas encore d'annonces en favoris.
                <a href="accueil.php" class="alert-link">Parcourir les annonces</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach($favoris as $favori): 
                    $images = explode(',', $favori['images']);
                    $premiere_image = !empty($images[0]) ? 'uploads/' . $images[0] : 'images/default.jpg';
                ?>
                    <div class="col">
                        <div class="card h-100">
                            <button class="btn btn-danger favorite-btn"
                                    onclick="removeFavorite(<?= $favori['annonce_id'] ?>, this)">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                            <img src="<?= htmlspecialchars($premiere_image) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($favori['titre']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($favori['titre']) ?></h5>
                                <p class="card-text">
                                    Prix : <?= number_format($favori['prix'], 2, ',', ' ') ?> €
                                    <?php if($favori['prix_negociable']): ?>
                                        <span class="badge bg-info">Négociable</span>
                                    <?php endif; ?>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($favori['localisation']) ?><br>
                                        <i class="bi bi-person"></i> <?= htmlspecialchars($favori['pseudo']) ?><br>
                                        Ajouté aux favoris le <?= date('d/m/Y', strtotime($favori['date_favoris'])) ?>
                                    </small>
                                </p>
                                <a href="voir_annonce.php?id=<?= $favori['annonce_id'] ?>" 
                                   class="btn btn-primary">
                                    Voir l'annonce
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeFavorite(annonceId, button) {
            if (confirm('Voulez-vous vraiment retirer cette annonce de vos favoris ?')) {
                fetch('toggle_favori.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ annonce_id: annonceId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('.col').remove();
                        // Reload page if no favorites left
                        if (document.querySelectorAll('.col').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Erreur lors du retrait des favoris');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
            }
        }
    </script>
</body>
</html>