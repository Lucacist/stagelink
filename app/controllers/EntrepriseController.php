<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/EntrepriseModel.php';

class EntrepriseController extends Controller {
    private $entrepriseModel;
    
    public function __construct() {
        $this->entrepriseModel = new EntrepriseModel();
    }
    
    public function index() {
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprises = $this->entrepriseModel->getAllEntreprisesWithRatings($userId);
        
        echo $this->render('Entreprises', [
            'pageTitle' => 'Entreprises - StageLink',
            'entreprises' => $entreprises
        ]);
    }
    
    public function details() {
        $this->checkPageAccess('VOIR_ENTREPRISE');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $this->redirect('entreprises');
        }
        
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $entreprise = $this->entrepriseModel->getEntrepriseWithRatingsById($id, $userId);
        
        if (!$entreprise) {
            $this->redirect('entreprises');
        }
        
        $evaluations = $this->entrepriseModel->getEvaluations($id);
        
        $offres = $this->entrepriseModel->getOffresEntreprise($id);
        
        $totalCandidatures = $this->entrepriseModel->getTotalCandidaturesEntreprise($id);
        
        echo $this->render('entreprise_details', [
            'pageTitle' => $entreprise['nom'] . ' - StageLink',
            'entreprise' => $entreprise,
            'evaluations' => $evaluations,
            'offres' => $offres,
            'totalCandidatures' => $totalCandidatures
        ]);
    }
    
    public function traiter() {
        $this->checkPageAccess('GERER_ENTREPRISES');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
            return;
        }
        
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        
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
        
        if (empty($errors)) {
            if ($action === 'create') {
                $success = $this->entrepriseModel->createEntreprise($nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été créée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la création de l'entreprise.";
                }
            } elseif ($action === 'update' && $id > 0) {
                $success = $this->entrepriseModel->updateEntreprise($id, $nom, $description, $email, $telephone);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été mise à jour avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la mise à jour de l'entreprise.";
                }
            } elseif ($action === 'delete' && $id > 0) {
                $success = $this->entrepriseModel->deleteEntreprise($id);
                if ($success) {
                    $_SESSION['success_message'] = "L'entreprise a été supprimée avec succès.";
                } else {
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de l'entreprise.";
                }
            }
        } else {
            $_SESSION['error_message'] = implode('<br>', $errors);
        }
        
        $this->redirect('dashboard');
    }
    
    public function rate() {
        $this->checkPageAccess('EVALUER_ENTREPRISE');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('entreprises');
            return;
        }
        
        // Débogage - Enregistrer les données reçues
        $debug = "Données reçues: \n";
        $debug .= "POST: " . print_r($_POST, true) . "\n";
        $debug .= "SESSION: " . print_r($_SESSION, true) . "\n";
        file_put_contents(ROOT_PATH . '/debug_rating.log', $debug, FILE_APPEND);
        
        $entrepriseId = isset($_POST['entreprise_id']) ? (int)$_POST['entreprise_id'] : 0;
        $note = isset($_POST['note']) ? (int)$_POST['note'] : 0;
        $userId = $_SESSION['user_id'];
        
        if ($entrepriseId <= 0 || $note < 1 || $note > 5) {
            $_SESSION['error_message'] = "Données invalides pour l'évaluation.";
            $this->redirect('entreprise_details&id=' . $entrepriseId);
            return;
        }
        
        $success = $this->entrepriseModel->rateEntreprise($entrepriseId, $userId, $note);
        
        // Débogage - Enregistrer le résultat
        $debug = "Résultat de rateEntreprise: \n";
        $debug .= "Success: " . ($success ? 'true' : 'false') . "\n";
        $debug .= "entrepriseId: $entrepriseId, userId: $userId, note: $note\n";
        file_put_contents(ROOT_PATH . '/debug_rating.log', $debug, FILE_APPEND);
        
        if ($success) {
            $_SESSION['success_message'] = "Votre évaluation a été enregistrée avec succès.";
        } else {
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'enregistrement de votre évaluation.";
        }
        
        $this->redirect('entreprise_details&id=' . $entrepriseId);
    }
}
