<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/EntrepriseModel.php';

class EntrepriseController extends Controller {
    private $entrepriseModel;
    
    public function __construct() {
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        // Vérifier les permissions
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        // Récupérer toutes les entreprises avec leurs notes
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprises = $this->entrepriseModel->getAllEntreprisesWithRatings($userId);
        
        // Afficher la liste des entreprises
        echo $this->render('Entreprises', [
            'pageTitle' => 'Entreprises - StageLink',
            'entreprises' => $entreprises
        ]);
    }
    
    public function details() {
        // Vérifier les permissions
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        // Récupérer l'ID de l'entreprise
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->redirect('entreprises');
        }
        
        // Récupérer les détails de l'entreprise avec les notes
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprise = $this->entrepriseModel->getEntrepriseWithRatingsById($id, $userId);
        
        if (!$entreprise) {
            $this->redirect('entreprises');
        }
        
        // Récupérer les évaluations de l'entreprise
        $evaluations = $this->entrepriseModel->getEvaluations($id);
        
        // Récupérer les offres de l'entreprise
        $offres = $this->entrepriseModel->getOffresEntreprise($id);
        
        // Récupérer le nombre total de candidatures de l'entreprise
        $totalCandidatures = $this->entrepriseModel->getTotalCandidaturesEntreprise($id);
        
        // Afficher les détails de l'entreprise
        echo $this->render('entreprise_details', [
            'pageTitle' => $entreprise['nom'] . ' - StageLink',
            'entreprise' => $entreprise,
            'evaluations' => $evaluations,
            'offres' => $offres,
            'totalCandidatures' => $totalCandidatures
        ]);
    }
    
    public function traiter() {
        // Vérifier les permissions
        $this->checkPageAccess('GERER_ENTREPRISES');
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        
        // Validation des données
        $errors = [];
        
        if (empty($nom)) {
            $errors[] = "Le nom de l'entreprise est requis.";
        }
        
        if (empty($description)) {
            $errors[] = "La description de l'entreprise est requise.";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Une adresse email valide est requise.";
        }
        
        // Traiter selon l'action demandée
        if (empty($errors)) {
            if ($action === 'create') {
                // Créer une nouvelle entreprise
                $success = $this->entrepriseModel->createEntreprise($nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été créée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la création de l'entreprise.";
                }
            } elseif ($action === 'update' && $id > 0) {
                // Mettre à jour une entreprise existante
                $success = $this->entrepriseModel->updateEntreprise($id, $nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été mise à jour avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour de l'entreprise.";
                }
            } elseif ($action === 'delete' && $id > 0) {
                // Supprimer une entreprise
                $success = $this->entrepriseModel->deleteEntreprise($id);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été supprimée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'entreprise.";
                }
            }
        } else {
            // S'il y a des erreurs, les stocker dans la session
            $_SESSION['error_message'] = implode('<br>', $errors);
        }
        
        // Rediriger vers la page précédente
        $this->redirect('dashboard');
    }
    
    public function rate() {
        // Vérifier les permissions
        $this->checkPageAccess('NOTER_ENTREPRISE');
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('entreprises');
            return;
        }
        
        // Récupérer les données du formulaire
        $entrepriseId = isset($_POST['entreprise_id']) ? (int)$_POST['entreprise_id'] : 0;
        $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
        $commentaire = trim($_POST['commentaire'] ?? '');
        $userId = $_SESSION['user_id'];
        
        // Validation des données
        if ($entrepriseId <= 0 || $note < 1 || $note > 5) {
            $_SESSION['error_message'] = "Données invalides pour l'évaluation.";
            $this->redirect('entreprise_details&id=' . $entrepriseId);
            return;
        }
        
        // Enregistrer l'évaluation
        $success = $this->entrepriseModel->rateEntreprise($entrepriseId, $userId, $note, $commentaire);
        
        if ($success) {
            $_SESSION['success_message'] = "Votre évaluation a été enregistrée avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'enregistrement de votre évaluation.";
        }
        
        // Rediriger vers la page de détails de l'entreprise
        $this->redirect('entreprise_details&id=' . $entrepriseId);
    }
}
?>
