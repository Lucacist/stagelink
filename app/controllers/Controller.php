<?php
/**
 * Contrôleur de base dont héritent tous les autres contrôleurs
 */
class Controller {
    /**
     * Rend une vue avec des données
     */
    protected function render($view, $data = []) {
        // Extraction des données pour les rendre disponibles dans la vue
        extract($data);
        
        // Démarrer la mise en mémoire tampon de sortie
        ob_start();
        
        // Inclure la vue
        include ROOT_PATH . '/app/views/' . $view . '.php';
        
        // Récupérer le contenu du tampon et l'effacer
        $content = ob_get_clean();
        
        // Retourner le contenu
        return $content;
    }
    
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    protected function hasPermission($permissionCode) {
        require_once ROOT_PATH . '/app/models/UtilisateurModel.php';
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $userModel = new UtilisateurModel();
        return $userModel->hasPermission($_SESSION['user_id'], $permissionCode);
    }
    
    /**
     * Vérifie si l'accès à une page est autorisé
     */
    protected function checkPageAccess($requiredPermission) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit();
        }
        
        if (!$this->hasPermission($requiredPermission)) {
            header('Location: index.php?route=accueil');
            exit();
        }
    }
    
    /**
     * Redirige vers une route
     */
    protected function redirect($route) {
        header('Location: index.php?route=' . $route);
        exit();
    }
}
?>
