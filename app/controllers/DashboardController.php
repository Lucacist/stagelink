<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/UtilisateurModel.php';
require_once ROOT_PATH . '/app/models/OffreModel.php';
require_once ROOT_PATH . '/app/models/EntrepriseModel.php';

class DashboardController extends Controller {
    private $utilisateurModel;
    private $offreModel;
    private $entrepriseModel;
    
    public function __construct() {
        $this->utilisateurModel = new UtilisateurModel();
        $this->offreModel = new OffreModel();
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        // Vérifier que l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        // Vérifier que l'utilisateur est admin ou pilote
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'PILOTE')) {
            $this->redirect('accueil');
        }
        
        // Récupérer les données pour le tableau de bord selon le rôle de l'utilisateur
        $userData = $this->utilisateurModel->getUserById($_SESSION['user_id']);
        $userRole = $this->utilisateurModel->getUserRole($_SESSION['user_id']);
        
        // Récupérer les permissions de l'utilisateur
        $userPermissions = [
            'GERER_ENTREPRISES' => $this->hasPermission('GERER_ENTREPRISES'),
            'GERER_OFFRES' => $this->hasPermission('GERER_OFFRES'),
            'GERER_UTILISATEURS' => $this->hasPermission('GERER_UTILISATEURS'),
            'CREER_OFFRE' => $this->hasPermission('CREER_OFFRE')
        ];
        
        // Récupérer les entreprises pour le formulaire de création d'offres
        $entreprises = $this->entrepriseModel->getAllEntreprises();
        
        // Récupérer les compétences pour le formulaire de création d'offres
        $competences = $this->offreModel->getAllCompetences();
        
        // Données générales
        $totalOffres = count($this->offreModel->getAllOffres());
        $totalEntreprises = count($this->entrepriseModel->getAllEntreprises());
        
        // Afficher le tableau de bord
        echo $this->render('dashboard', [
            'pageTitle' => 'Tableau de bord - StageLink',
            'userData' => $userData,
            'userRole' => $userRole,
            'totalOffres' => $totalOffres,
            'totalEntreprises' => $totalEntreprises,
            'entreprises' => $entreprises,
            'competences' => $competences,
            'userPermissions' => $userPermissions
        ]);
    }
}
?>
