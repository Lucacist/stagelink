<?php
// Définir le chemin racine de l'application uniquement s'il n'est pas déjà défini
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/..');
}

// Inclure la nouvelle classe Auth au lieu de la configuration directe
require_once ROOT_PATH . '/app/utils/Auth.php';

/**
 * Vérifie si l'utilisateur a une permission spécifique
 * Fonction de compatibilité redirigeant vers la classe Auth
 */
function hasPermission($userId, $permissionCode) {
    return Auth::hasPermission($userId, $permissionCode);
}

/**
 * Récupère toutes les permissions d'un utilisateur
 * Fonction de compatibilité redirigeant vers la classe Auth
 */
function getUserPermissions($userId) {
    return Auth::getUserPermissions($userId);
}

/**
 * Récupère le rôle d'un utilisateur
 * Fonction de compatibilité redirigeant vers la classe Auth
 */
function getUserRole($userId) {
    return Auth::getUserRole($userId);
}

/**
 * Vérifie si l'accès à une page est autorisé
 * Fonction de compatibilité redirigeant vers la classe Auth
 */
function checkPageAccess($requiredPermission) {
    return Auth::checkPageAccess($requiredPermission);
}
