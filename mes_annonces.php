<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Récupérer les annonces publiées avec toutes les informations nécessaires
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
        WHERE a.utilisateur_id = ?
        GROUP BY a.annonce_id
        ORDER BY a.date_creation DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les brouillons avec leurs informations complètes
    $stmt = $db->prepare("
        SELECT b.*, 
               c.nom as categorie_nom,
               m.nom as marque_nom
        FROM brouillons b
        LEFT JOIN categories c ON b.categorie_id = c.categorie_id
        LEFT JOIN marques m ON b.marque_id = m.id
        WHERE b.utilisateur_id = ? 
        ORDER BY b.date_creation DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $brouillons = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    error_log($e->getMessage());
    $error = "Une erreur est survenue";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Annonces - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .draft-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 15px;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mes Annonces</h2>
            <a href="accueil.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Onglets -->
        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#publiees">
                    Annonces publiées (<?= count($annonces) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#brouillons">
                    Brouillons (<?= count($brouillons) ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Annonces publiées -->
            <div class="tab-pane fade show active" id="publiees">
                <div class="row g-4">
                    <?php if (empty($annonces)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                Vous n'avez pas encore publié d'annonces.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($annonces as $annonce): 
                            $images = explode(',', $annonce['images']);
                            $premiere_image = !empty($images[0]) ? 'uploads/' . $images[0] : 'images/default.jpg';
                        ?>
                            <div class="col-md-4">
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="voir_annonce.php?id=<?= $annonce['annonce_id'] ?>" 
                                               class="btn btn-primary btn-sm">Voir</a>
                                            <div>
                                                <a href="modifier_annonce.php?id=<?= $annonce['annonce_id'] ?>" 
                                                   class="btn btn-secondary btn-sm me-1">Modifier</a>
                                                <button class="btn btn-danger btn-sm" 
                                                        onclick="supprimerAnnonce(<?= $annonce['annonce_id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Brouillons -->
            <div class="tab-pane fade" id="brouillons">
                <div class="row g-4">
                    <?php if (empty($brouillons)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                Vous n'avez aucun brouillon pour le moment.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach($brouillons as $brouillon): ?>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="draft-badge">
                                        <i class="bi bi-pencil-square"></i> Brouillon
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars($brouillon['titre'] ?? 'Sans titre') ?>
                                        </h5>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Dernière modification : 
                                                <?= date('d/m/Y H:i', strtotime($brouillon['date_creation'])) ?>
                                            </small>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="edit_brouillon.php?id=<?= $brouillon['brouillon_id'] ?>" 
                                               class="btn btn-primary btn-sm">Continuer</a>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="supprimerBrouillon(<?= $brouillon['brouillon_id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour supprimer une annonce
        function supprimerAnnonce(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.')) {
                fetch('supp_annonce.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ annonce_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression de l\'annonce');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
            }
        }

        // Fonction pour supprimer un brouillon
        function supprimerBrouillon(id) {
            if (confirm('Voulez-vous vraiment supprimer ce brouillon ?')) {
                fetch('supp_brouillon.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ brouillon_id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression du brouillon');
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