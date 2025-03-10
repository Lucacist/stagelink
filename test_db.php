<?php
// Définir la racine de l'application
define('ROOT_PATH', __DIR__);

// Inclure la classe Database
require_once ROOT_PATH . '/app/models/Database.php';

// Tester la connexion
$db = Database::getInstance()->getConnection();

if ($db) {
    echo "Connexion à la base de données réussie !";
    
    // Tester une requête simple
    $result = $db->query("SELECT 'Test de requête' AS test");
    $row = $result->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "Échec de la connexion à la base de données.";
}
?>
