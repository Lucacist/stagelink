<?php
// Point d'entrée principal de l'application
session_start();

// Définir la racine de l'application
define('ROOT_PATH', __DIR__);

// Inclure la configuration
require_once ROOT_PATH . '/config/config.php';
require_once 'app/controllers/CandidatureController.php';

// Si l'utilisateur n'est pas connecté et essaie d'accéder à une route qui nécessite une authentification,
// il est redirigé vers la page de connexion
$public_routes = ['login', 'logout', 'accueil', 'offres', 'offre_details'];
$route = isset($_GET['route']) ? $_GET['route'] : 'accueil';

// Rediriger vers login si l'utilisateur n'est pas connecté et tente d'accéder à une route protégée
if (!isset($_SESSION['user_id']) && !in_array($route, $public_routes)) {
    header('Location: index.php?route=login');
    exit();
}

// Routage des requêtes vers les contrôleurs appropriés
switch ($route) {
    case 'accueil':
        require_once ROOT_PATH . '/app/controllers/AccueilController.php';
        $controller = new AccueilController();
        $controller->index();
        break;
    
    case 'login':
        require_once ROOT_PATH . '/app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
    
    case 'entreprises':
        require_once ROOT_PATH . '/app/controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->index();
        break;
    
    case 'entreprise_details':
        require_once ROOT_PATH . '/app/controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->details();
        break;
    
    case 'offres':
        require_once ROOT_PATH . '/app/controllers/OffreController.php';
        $controller = new OffreController();
        $controller->index();
        break;
    
    case 'offre_details':
        require_once ROOT_PATH . '/app/controllers/OffreController.php';
        $controller = new OffreController();
        $controller->details();
        break;
    
    case 'creer-offre':
        require_once ROOT_PATH . '/app/controllers/OffreController.php';
        $controller = new OffreController();
        $controller->create();
        break;
    
    case 'dashboard':
        require_once ROOT_PATH . '/app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
    
    case 'like':
        require_once ROOT_PATH . '/app/controllers/OffreController.php';
        $controller = new OffreController();
        $controller->like();
        break;
    
    case 'rate_entreprise':
        require_once ROOT_PATH . '/app/controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->rate();
        break;
    
    case 'traiter_candidature':
        require_once ROOT_PATH . '/app/controllers/CandidatureController.php';
        $controller = new CandidatureController();
        $controller->traiter();
        break;
    
    case 'traiter_entreprise':
        require_once ROOT_PATH . '/app/controllers/EntrepriseController.php';
        $controller = new EntrepriseController();
        $controller->traiter();
        break;
    
    case 'traiter_offre':
        require_once ROOT_PATH . '/app/controllers/OffreController.php';
        $controller = new OffreController();
        $controller->traiter();
        break;
    
    case 'profil':
        require_once ROOT_PATH . '/app/controllers/ProfilController.php';
        $controller = new ProfilController();
        $controller->index();
        break;
        
    case 'logout':
        require_once ROOT_PATH . '/app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case 'candidature_postuler':
        $controller = new CandidatureController();
        $controller->postuler();
        break;
    
    case 'confirmation_candidature':
        $controller = new CandidatureController();
            // Remplacer l'appel direct à render() par:
        $controller->afficherConfirmation();
        break;
        


    
    case 'mes_candidatures':
        $controller = new CandidatureController();
        $controller->mesCandidatures();
        break;
        
    default:
        // Page 404 ou redirection vers la page d'accueil
        header('Location: index.php?route=accueil');
        exit();
}
?>
