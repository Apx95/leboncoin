<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Non autorisé']));
}

// Get and validate JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['annonce_id']) || !is_numeric($data['annonce_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'ID de l\'annonce invalide']));
}

try {
    $db->beginTransaction();

    // Check if favorite already exists
    $stmt = $db->prepare("
        SELECT favori_id 
        FROM favoris 
        WHERE utilisateur_id = ? 
        AND annonce_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $data['annonce_id']]);
    $favori = $stmt->fetch();

    if ($favori) {
        // Remove favorite
        $stmt = $db->prepare("
            DELETE FROM favoris 
            WHERE utilisateur_id = ? 
            AND annonce_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $data['annonce_id']]);
        $is_favori = false;
    } else {
        // Check if listing exists
        $stmt = $db->prepare("SELECT annonce_id FROM annonces WHERE annonce_id = ?");
        $stmt->execute([$data['annonce_id']]);
        if (!$stmt->fetch()) {
            throw new Exception('Annonce introuvable');
        }

        // Add favorite
        $stmt = $db->prepare("
            INSERT INTO favoris (utilisateur_id, annonce_id, date_ajout) 
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$_SESSION['user_id'], $data['annonce_id']]);
        $is_favori = true;
    }

    $db->commit();
    echo json_encode([
        'success' => true,
        'is_favori' => $is_favori,
        'message' => $is_favori ? 'Ajouté aux favoris' : 'Retiré des favoris'
    ]);

} catch (Exception $e) {
    $db->rollBack();
    error_log('Erreur toggle_favori.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la modification des favoris',
        'details' => $e->getMessage()
    ]);
}