<?php
require "db.php";

header('Content-Type: application/json');

if (!isset($_GET['categorie_id'])) {
    echo json_encode(['error' => 'Catégorie non spécifiée']);
    exit;
}

$categorie_id = filter_input(INPUT_GET, 'categorie_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Récupérer les marques de la catégorie sélectionnée
    $stmt = $pdo->prepare("
        SELECT id, nom 
        FROM marques 
        WHERE categorie_id = ? 
        ORDER BY nom ASC
    ");
    $stmt->execute([$categorie_id]);
    $marques = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($marques);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur lors de la récupération des marques']);
}
?>