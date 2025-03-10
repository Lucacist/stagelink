<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/OffreModel.php';
require_once ROOT_PATH . '/app/models/EntrepriseModel.php';

class OffreController extends Controller {
    private $offreModel;
    private $entrepriseModel;
    
    public function __construct() {
        $this->offreModel = new OffreModel();
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        // Vérifier les permissions
        $this->checkPageAccess('VOIR_OFFRE');
        
        // Récupérer toutes les offres
        $offres = $this->offreModel->getAllOffres();
        
        // Ajouter les compétences et le statut des likes pour chaque offre
        foreach ($offres as &$offre) {
            $offre['competences'] = $this->offreModel->getOffreCompetences($offre['id']);
            
            // Vérifier si l'utilisateur est connecté
            if (isset($_SESSION['user_id'])) {
                $offre['isLiked'] = $this->offreModel->isOffreLiked($offre['id'], $_SESSION['user_id']);
            } else {
                $offre['isLiked'] = false;
            }
        }
        
        // Afficher la liste des offres
        echo $this->render('offres', [
            'pageTitle' => 'Offres de stage - StageLink',
            'offres' => $offres
        ]);
    }
    
    public function details() {
        // Vérifier les permissions
        $this->checkPageAccess('VOIR_OFFRE');
        
        // Récupérer l'ID de l'offre
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->redirect('offres');
        }
        
        // Récupérer les détails de l'offre
        $offre = $this->offreModel->getOffreById($id);
        
        if (!$offre) {
            $this->redirect('offres');
        }
        
        // Récupérer les compétences requises pour l'offre
        $competences = $this->offreModel->getOffreCompetences($id);
        
        // Vérifier si l'offre est dans la wishlist de l'utilisateur
        $isLiked = false;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $isLiked = $this->offreModel->isOffreLiked($id, $_SESSION['user_id']);
        }
        
        // Afficher les détails de l'offre
        echo $this->render('offre_details', [
            'pageTitle' => $offre['titre'] . ' - StageLink',
            'offre' => $offre,
            'competences' => $competences,
            'isLiked' => $isLiked
        ]);
    }
    
    public function create() {
        // Vérifier les permissions
        $this->checkPageAccess('CREER_OFFRE');
        
        // Récupérer la liste des entreprises pour le formulaire
        $entreprises = $this->entrepriseModel->getAllEntreprises();
        
        // Mode édition si un ID est fourni
        $mode = 'create';
        $offre = null;
        
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $offre = $this->offreModel->getOffreById($id);
            
            if ($offre) {
                $mode = 'edit';
            }
        }
        
        // Afficher le formulaire de création/édition d'offre
        echo $this->render('creer-offre', [
            'pageTitle' => ($mode === 'create' ? 'Créer une offre' : 'Modifier une offre') . ' - StageLink',
            'entreprises' => $entreprises,
            'mode' => $mode,
            'offre' => $offre
        ]);
    }
    
    public function traiter() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        // Vérifier que l'utilisateur est admin ou pilote
        if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'ADMIN' && $_SESSION['user_role'] !== 'PILOTE')) {
            $this->redirect('accueil');
        }
        
        // Déboguer toutes les données POST reçues
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $action = $_POST['action'] ?? '';
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($action === 'create' || $action === 'update') {
                $entrepriseId = isset($_POST['entreprise_id']) ? (int)$_POST['entreprise_id'] : 0;
                $titre = $_POST['titre'] ?? '';
                $description = $_POST['description'] ?? '';
                $baseRemuneration = isset($_POST['base_remuneration']) ? (float)$_POST['base_remuneration'] : 0;
                $dateDebut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d');
                $dateFin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d', strtotime('+3 months'));
                $competences = isset($_POST['competences']) ? $_POST['competences'] : [];
                
                // Déboguer les données reçues
                error_log("Création d'offre - Données reçues: " . json_encode([
                    'action' => $action,
                    'entrepriseId' => $entrepriseId,
                    'titre' => $titre,
                    'competences' => $competences
                ]));
                
                if ($action === 'create') {
                    // Créer une nouvelle offre en passant directement les compétences
                    $offreId = $this->offreModel->createOffre(
                        $entrepriseId, 
                        $titre, 
                        $description, 
                        $baseRemuneration, 
                        $dateDebut, 
                        $dateFin,
                        $competences
                    );
                    
                    if (!$offreId) {
                        // Gérer l'erreur
                        $_SESSION['error_message'] = "Erreur lors de la création de l'offre.";
                    } else {
                        $_SESSION['success_message'] = "L'offre a été créée avec succès.";
                    }
                } else {
                    // Mettre à jour une offre existante
                    $success = $this->offreModel->updateOffre($id, $entrepriseId, $titre, $description, $baseRemuneration, $dateDebut, $dateFin);
                    
                    // Mettre à jour les compétences associées
                    if ($success) {
                        $this->offreModel->addOffreCompetences($id, $competences);
                    }
                    
                    if (!$success) {
                        $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'offre.";
                    } else {
                        $_SESSION['success_message'] = "L'offre a été mise à jour avec succès.";
                    }
                }
            } elseif ($action === 'delete' && $id > 0) {
                // Supprimer une offre
                $this->offreModel->deleteOffre($id);
            }
        }
        
        // Rediriger vers le dashboard au lieu des offres
        $this->redirect('dashboard');
    }
    
    /**
     * Méthode pour gérer les likes/unlikes d'offres (appelée via AJAX)
     */
    public function like() {
        // Définir l'en-tête pour une réponse JSON
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); // Non autorisé
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter une offre à vos favoris']);
            return;
        }
        
        // Récupérer l'ID de l'offre
        $offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
        
        if ($offreId <= 0) {
            http_response_code(400); // Bad request
            echo json_encode(['success' => false, 'message' => 'ID d\'offre invalide']);
            return;
        }
        
        // Ajouter/supprimer l'offre des favoris
        $result = $this->offreModel->toggleLike($offreId, $_SESSION['user_id']);
        
        // Vérifier si l'offre est maintenant likée
        $isLiked = $this->offreModel->isOffreLiked($offreId, $_SESSION['user_id']);
        
        // Envoyer la réponse
        echo json_encode([
            'success' => $result,
            'liked' => $isLiked
        ]);
        exit; // Arrêter l'exécution pour éviter tout autre sortie
    }
}
