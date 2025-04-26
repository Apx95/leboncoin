<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération des brouillons
try {
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
    $error = "Erreur lors de la récupération des brouillons.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes brouillons - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .brouillon-card {
            transition: transform 0.2s;
            position: relative;
        }
        .brouillon-card:hover {
            transform: translateY(-5px);
        }
        .date-creation {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .draft-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 15px;
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php include 'navbarCo.php'; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mes brouillons</h2>
            <a href="mes_annonces.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
        
        <?php if (isset($_GET['draft']) && $_GET['draft'] === 'saved'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Votre annonce a été sauvegardée en brouillon.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (empty($brouillons)): ?>
            <div class="alert alert-info">
                Vous n'avez aucun brouillon pour le moment.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($brouillons as $brouillon): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card brouillon-card h-100">
                            <div class="draft-badge">
                                <i class="bi bi-pencil-square"></i> Brouillon
                            </div>
                            <?php 
                            if (!empty($brouillon['images'])) {
                                $images = json_decode($brouillon['images'], true);
                                if (!empty($images)) {
                                    echo '<img src="uploads/brouillons/' . htmlspecialchars($images[0]) . '" 
                                              class="card-img-top" 
                                              alt="Image principale"
                                              style="height: 200px; object-fit: cover;">';
                                }
                            }
                            ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?= htmlspecialchars($brouillon['titre'] ?? 'Sans titre') ?>
                                </h5>
                                <?php if (!empty($brouillon['prix'])): ?>
                                    <p class="card-text">
                                        Prix : <?= number_format($brouillon['prix'], 2, ',', ' ') ?> €
                                        <?php if ($brouillon['prix_negociable']): ?>
                                            <span class="badge bg-info">Négociable</span>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                <p class="card-text text-truncate">
                                    <?= htmlspecialchars($brouillon['description'] ?? 'Aucune description') ?>
                                </p>
                                <div class="card-text small">
                                    <?php if (!empty($brouillon['categorie_nom'])): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-tag"></i> 
                                            <?= htmlspecialchars($brouillon['categorie_nom']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($brouillon['marque_nom'])): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-box"></i> 
                                            <?= htmlspecialchars($brouillon['marque_nom']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($brouillon['etat'])): ?>
                                        <p class="mb-1">
                                            <i class="bi bi-info-circle"></i> 
                                            État : <?= htmlspecialchars($brouillon['etat']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <p class="date-creation mt-2">
                                    <i class="bi bi-clock"></i>
                                    Modifié le <?= date('d/m/Y à H:i', strtotime($brouillon['date_creation'])) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <a href="edit_brouillon.php?id=<?= $brouillon['brouillon_id'] ?>" 
                                       class="btn btn-primary btn-sm">Continuer</a>
                                    <button class="btn btn-danger btn-sm delete-brouillon" 
                                            data-id="<?= $brouillon['brouillon_id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.delete-brouillon').forEach(button => {
            button.addEventListener('click', async function() {
                if (confirm('Voulez-vous vraiment supprimer ce brouillon ?')) {
                    try {
                        const response = await fetch('supp_brouillon.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                brouillon_id: this.dataset.id
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.closest('.col-md-6').remove();
                            if (document.querySelectorAll('.brouillon-card').length === 0) {
                                location.reload();
                            }
                        } else {
                            throw new Error(data.error || 'Erreur lors de la suppression');
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la suppression du brouillon');
                    }
                }
            });
        });
    </script>
</body>
</html>