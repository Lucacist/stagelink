<?php
session_start();
require_once 'config.php';
require_once 'utils/permissions.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Non connecté']));
}

// Vérifier si l'offre_id est fourni
if (!isset($_POST['offre_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'ID de l\'offre manquant']));
}

$utilisateur_id = $_SESSION['user_id'];
$offre_id = (int)$_POST['offre_id'];

try {
    // Commencer une transaction
    $conn->begin_transaction();

    // Vérifier si le like existe déjà
    $stmt = $conn->prepare("SELECT 1 FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
    $stmt->bind_param("ii", $utilisateur_id, $offre_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;

    if ($exists) {
        // Supprimer le like s'il existe
        $stmt = $conn->prepare("DELETE FROM WishList WHERE utilisateur_id = ? AND offre_id = ?");
        $stmt->bind_param("ii", $utilisateur_id, $offre_id);
        $stmt->execute();
    } else {
        // Ajouter le like s'il n'existe pas
        $stmt = $conn->prepare("INSERT INTO WishList (utilisateur_id, offre_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $utilisateur_id, $offre_id);
        $stmt->execute();
    }

    // Valider la transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'action' => $exists ? 'removed' : 'added'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour des favoris'
    ]);
}

$conn->close();