<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: accueil.php');
    exit();
}

try {
    $stmt = $db->prepare("
        SELECT a.*, u.pseudo, c.nom as categorie, m.nom as marque, 
               GROUP_CONCAT(i.url) as images
        FROM annonces a
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.utilisateur_id
        LEFT JOIN categories c ON a.categorie_id = c.categorie_id
        LEFT JOIN marques m ON a.marque_id = m.id
        LEFT JOIN images i ON a.annonce_id = i.annonce_id
        WHERE a.annonce_id = ?
        GROUP BY a.annonce_id
    ");
    
    $stmt->execute([$_GET['id']]);
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$annonce) {
        header('Location: accueil.php');
        exit();
    }
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($annonce['titre']) ?> - ElectroBazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ... autres liens CSS ... -->
</head>
<body>
    <!-- Affichez les détails de l'annonce ici -->
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h1><?= htmlspecialchars($annonce['titre']) ?></h1>
                <p class="text-success">Annonce publiée avec succès !</p>
                <!-- ... autres détails de l'annonce ... -->
                <a href="accueil.php" class="btn btn-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
</body>
</html>