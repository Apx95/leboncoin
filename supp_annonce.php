<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Non autorisé']));
}

// Récupération et validation des données
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['annonce_id']) || !is_numeric($data['annonce_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'ID de l\'annonce invalide ou manquant']));
}

try {
    $db->beginTransaction();

    // Vérifier que l'annonce appartient bien à l'utilisateur
    $stmt = $db->prepare("
        SELECT annonce_id 
        FROM annonces 
        WHERE annonce_id = ? 
        AND utilisateur_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$data['annonce_id'], $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        $db->rollBack();
        http_response_code(404);
        die(json_encode(['error' => 'Annonce non trouvée ou non autorisée']));
    }

    // Supprimer les images associées
    $stmt = $db->prepare("SELECT url FROM images WHERE annonce_id = ?");
    $stmt->execute([$data['annonce_id']]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($images as $image) {
        $filepath = __DIR__ . '/uploads/' . $image;
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                error_log('Impossible de supprimer le fichier: ' . $filepath);
            }
        }
    }

    // Supprimer les entrées dans les tables associées
    $tables = ['images', 'favoris', 'annonces'];
    foreach ($tables as $table) {
        $stmt = $db->prepare("DELETE FROM {$table} WHERE annonce_id = ?");
        $stmt->execute([$data['annonce_id']]);
    }

    $db->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Annonce supprimée avec succès'
    ]);

} catch (PDOException $e) {
    $db->rollBack();
    error_log('Erreur SQL lors de la suppression de l\'annonce: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la suppression',
        'message' => 'Une erreur est survenue lors de la suppression de l\'annonce'
    ]);
} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur lors de la suppression de l\'annonce: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la suppression',
        'message' => $e->getMessage()
    ]);
}