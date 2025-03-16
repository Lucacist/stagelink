<?php
class Controller {
    protected function render($view, $data = []) {
        extract($data);
        
        ob_start();
        
        include ROOT_PATH . '/app/views/' . $view . '.php';
        
        $content = ob_get_clean();
        
        return $content;
    }
    
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
    
    protected function checkPageAccess($permissionCode) {
        if (!$this->hasPermission($permissionCode)) {
            $this->redirect('accueil');
        }
    }
    
    protected function redirect($route, $params = []) {
        $url = 'index.php?route=' . $route;
        
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
        
        header('Location: ' . $url);
        exit();
    }
    
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    /**
 * Obtenir le libellé du statut de candidature
 */
    protected function getStatusLabel($status) {
        $labels = [
            'en_attente' => 'En attente',
            'acceptee' => 'Acceptée',
            'refusee' => 'Refusée',
            'entretien' => 'Entretien planifié'
        ];
    
        return isset($labels[$status]) ? $labels[$status] : $status;
    }

}
?>
