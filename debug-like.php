<?php
// Script de débogage pour les likes

// Activer l'affichage complet des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définir l'en-tête pour une réponse JSON
header('Content-Type: application/json');

// Log du début d'exécution
error_log("Début de l'exécution du script de débogage de likes");

// Vérifier si on est dans une session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Informations sur la session
$session_info = [
    'session_id' => session_id(),
    'user_id' => $_SESSION['user_id'] ?? 'non défini',
    'user_role' => $_SESSION['user_role'] ?? 'non défini'
];

// Informations sur la requête
$request_info = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'offre_id' => $_POST['offre_id'] ?? 'non défini',
    'post_data' => $_POST
];

// Envoyer une réponse de test
echo json_encode([
    'success' => true,
    'liked' => true,
    'debug' => [
        'session' => $session_info,
        'request' => $request_info
    ]
]);

// Log de fin d'exécution
error_log("Fin de l'exécution du script de débogage de likes");
?>
