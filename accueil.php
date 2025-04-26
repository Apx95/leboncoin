<!-- filepath: c:\xampp\htdocs\leboncoin\acceuil.php -->
<?php
require "db.php";

// Ajouter la fonction checkFavori
function checkFavori($db, $user_id, $annonce_id) {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ? AND annonce_id = ?");
        $stmt->execute([$user_id, $annonce_id]);
        return $stmt->fetchColumn() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

// Vérifier si l'utilisateur est connecté
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Et dans le bloc try-catch des annonces
try {
    // ...code existant...
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$stmt = $db->query("
    SELECT a.*, 
           u.pseudo, 
           u.telephone,
           c.nom as categorie_nom,
           m.nom as marque_nom,
           GROUP_CONCAT(DISTINCT i.url) as images,
           (SELECT COUNT(*) FROM favoris f WHERE f.annonce_id = a.annonce_id) as nb_favoris
    FROM annonces a
    LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
    LEFT JOIN categories c ON a.categorie_id = c.categorie_id
    LEFT JOIN marques m ON a.marque_id = m.id  /* Correction ici : m.id au lieu de m.marque_id */
    LEFT JOIN images i ON a.annonce_id = i.annonce_id
    WHERE a.statut = 'active'
    GROUP BY a.annonce_id, a.titre, a.prix, a.description, 
             a.date_creation, u.pseudo, u.telephone, 
             c.nom, m.nom
    ORDER BY a.date_creation DESC
    LIMIT 12
");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroBazar - Accueil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Fichier CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
</head>
<body>
    <!-- Inclusion de la navbar -->
    <?php include 'navbarCo.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar des catégories -->
            <aside class="col-lg-3 col-md-4 sidebar">
                <h3 class="sidebar-title">Catégories</h3>
                <ul class="list-group">
                    <?php
                    try {
                        // Récupérer les catégories principales
                        $stmt = $db->query("SELECT categorie_id, nom, icone FROM categories WHERE categorie_parent_id IS NULL ORDER BY nom");
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                        foreach ($categories as $categorie) {
                            $icon = $categorie['icone'] ?? 'grid';
                            echo "<li class='list-group-item'>";
                            echo "<i class='bi bi-{$icon} me-2'></i>";
                            echo "<a href='category.php?id={$categorie['categorie_id']}'>" . htmlspecialchars($categorie['nom']) . "</a>";
                            echo "</li>";
                        }
                    } catch(PDOException $e) {
                        echo "<li class='list-group-item'>Erreur de chargement des catégories</li>";
                    }
                    ?>
                </ul>
            </aside>

            <!-- Contenu principal -->
            <main class="col-lg-9 col-md-8">
                <!-- Bannière centrale -->
                <section class="banner text-center">
                    <h2>Bienvenue sur ElectroBazar</h2>
                    <p>Trouvez votre prochain appareil électronique au meilleur prix !</p>
                    <a href="Depot_annonce.php" class="btn btn-banner">Publier une annonce</a>
                </section>

                <!-- Section Tendance -->
                <section class="trending">
                    <h2 class="section-title">Annonces récentes</h2>
                    <div class="row g-4">
                        <?php
                        try {
                            // Récupération des annonces avec leurs informations
                            $stmt = $db->query("
                                SELECT a.*, 
                                       u.pseudo, 
                                       u.telephone,
                                       c.nom as categorie_nom,
                                       m.nom as marque_nom,
                                       GROUP_CONCAT(DISTINCT i.url) as images,
                                       (SELECT COUNT(*) FROM favoris f WHERE f.annonce_id = a.annonce_id) as nb_favoris
                                FROM annonces a
                                LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
                                LEFT JOIN categories c ON a.categorie_id = c.categorie_id
                                LEFT JOIN marques m ON a.marque_id = m.id
                                LEFT JOIN images i ON a.annonce_id = i.annonce_id
                                WHERE a.statut = 'active'
                                GROUP BY a.annonce_id
                                ORDER BY a.date_creation DESC
                                LIMIT 12
                            ");
                            $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                            foreach($annonces as $annonce) {
                                $images = explode(',', $annonce['images']);
                                $premiere_image = !empty($images[0]) ? 'uploads/' . $images[0] : 'images/default.jpg';
                                $est_favori = isset($_SESSION['user_id']) ? checkFavori($db, $_SESSION['user_id'], $annonce['annonce_id']) : false;
                        ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card h-100 product-card">
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($premiere_image) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($annonce['titre']) ?>"
                                                 style="height: 200px; object-fit: cover;">
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <button class="btn-favori position-absolute top-0 end-0 m-2 <?= $est_favori ? 'active' : '' ?>"
                                                        data-annonce-id="<?= $annonce['annonce_id'] ?>">
                                                    <i class="bi <?= $est_favori ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($annonce['titre']) ?></h5>
                                            <p class="card-text">
                                                <strong><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</strong>
                                                <?php if($annonce['prix_negociable']): ?>
                                                    <span class="badge bg-info ms-2">Négociable</span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="bi bi-tag"></i> <?= htmlspecialchars($annonce['categorie_nom']) ?><br>
                                                    <?php if ($annonce['marque_nom']): ?>
                                                        <i class="bi bi-bookmark"></i> <?= htmlspecialchars($annonce['marque_nom']) ?><br>
                                                    <?php endif; ?>
                                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($annonce['localisation']) ?><br>
                                                    <i class="bi bi-clock"></i> <?= date('d/m/Y', strtotime($annonce['date_creation'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="voir_annonce.php?id=<?= $annonce['annonce_id'] ?>" 
                                                   class="btn btn-primary btn-sm">Voir l'annonce</a>
                                                <button type="button" 
                                                        class="btn btn-outline-secondary btn-sm"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#contactModal<?= $annonce['annonce_id'] ?>">
                                                    <i class="bi bi-chat-dots"></i> Contact
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                                    
                                <!-- Modal de contact pour chaque annonce -->
                                <div class="modal fade" id="contactModal<?= $annonce['annonce_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Contacter le vendeur</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Vendeur :</strong> <?= htmlspecialchars($annonce['pseudo']) ?></p>
                                                <?php if (!$annonce['masquer_telephone'] && $annonce['telephone']): ?>
                                                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($annonce['telephone']) ?></p>
                                                <?php endif; ?>
                                                <form action="envoyer_message.php" method="POST" class="message-form">
                                                    <input type="hidden" name="annonce_id" value="<?= $annonce['annonce_id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Votre message</label>
                                                        <textarea class="form-control" name="message" required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Envoyer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } catch(PDOException $e) {
                            echo "<div class='alert alert-danger'>Erreur lors du chargement des annonces</div>";
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
    <script>
    // Gestion des favoris
    document.querySelectorAll('.btn-favori').forEach(btn => {
        btn.addEventListener('click', async function() {
            const annonceId = this.dataset.annonceId;
            try {
                const response = await fetch('gerer_favori.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ annonce_id: annonceId })
                });

                if (!response.ok) throw new Error('Erreur réseau');

                const data = await response.json();
                if (data.success) {
                    const icon = this.querySelector('i');
                    this.classList.toggle('active');
                    icon.classList.toggle('bi-heart');
                    icon.classList.toggle('bi-heart-fill');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de la gestion des favoris');
            }
        });
    });
    </script>
</body>
</html>