<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/UtilisateurModel.php';

class AuthController extends Controller {
    private $utilisateurModel;
    
    public function __construct() {
        $this->utilisateurModel = new UtilisateurModel();
    }
    
    public function login() {
        // Si l'utilisateur est déjà connecté, rediriger vers l'accueil
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $this->redirect('accueil');
        }
        
        // Traitement du formulaire de connexion
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['mot_de_passe'] ?? '';
            
            $user = $this->utilisateurModel->authenticate($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                
                // Récupérer le rôle et les permissions
                $role = $this->utilisateurModel->getUserRole($user['id']);
                $_SESSION['user_role'] = $role['role_code'];
                
                // Récupérer et enregistrer les permissions de l'utilisateur
                $_SESSION['permissions'] = $this->utilisateurModel->getUserPermissions($user['id']);
                
                $this->redirect('accueil');
            } else {
                $error = 'Email ou mot de passe incorrect';
            }
        }
        
        // Affichage du formulaire de connexion
        echo $this->render('login', [
            'error' => $error,
            'pageTitle' => 'Connexion - StageLink'
        ]);
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Détruire toutes les données de session
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page de connexion
        $this->redirect('login');
    }
}
?>
