<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/OffreModel.php';
require_once ROOT_PATH . '/app/models/CandidatureModel.php';

class CandidatureController extends Controller {
    private $db;
    private $candidatureModel;
    
    public function __construct() {
       // parent::__construct();
        require_once ROOT_PATH . '/app/models/Database.php';
        $this->db = Database::getInstance()->getConnection();
        $this->candidatureModel = new CandidatureModel();
    }

    /**
     * Définit un message flash pour affichage ultérieur
     */
    public function setFlashMessage($type, $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Affiche toutes les candidatures (admin) ou celles de l'utilisateur courant
     */
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }
        
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
        
        if ($isAdmin) {
            $sql = "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom, u.nom as etudiant_nom, u.prenom as etudiant_prenom
                    FROM Candidatures c
                    JOIN Offres o ON c.offre_id = o.id
                    JOIN Entreprises e ON o.entreprise_id = e.id
                    JOIN Utilisateurs u ON c.utilisateur_id = u.id
                    ORDER BY c.date_candidature DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            $sql = "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom
                    FROM Candidatures c
                    JOIN Offres o ON c.offre_id = o.id
                    JOIN Entreprises e ON o.entreprise_id = e.id
                    WHERE c.utilisateur_id = ?
                    ORDER BY c.date_candidature DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
        }
        
        $result = $stmt->get_result();
        
        $candidatures = [];
        while ($row = $result->fetch_assoc()) {
            $candidatures[] = $row;
        }
        
        echo $this->render('traiter_candidature', [
            'pageTitle' => 'Candidatures - StageLink',
            'candidatures' => $candidatures,
            'isAdmin' => $isAdmin
        ]);
    }
    
    /**
     * Traite la soumission d'une candidature à partir du formulaire
     */
    public function postuler() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage('error', 'Vous devez être connecté pour postuler');
            header('Location: index.php?route=login');
            exit;
        }
        
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée');
            header('Location: index.php?route=offres');
            exit;
        }
        
        // Récupérer et valider les données
        $offre_id = filter_input(INPUT_POST, 'offre_id', FILTER_VALIDATE_INT);
        $lettre_motivation = htmlspecialchars($_POST['lettre_motivation'] ?? '');
        
        if (!$offre_id || empty($lettre_motivation)) {
            $this->setFlashMessage('error', 'Tous les champs sont obligatoires');
            header('Location: index.php?route=offre_details&id=' . $offre_id);
            exit;
        }
        
        // Gérer l'upload du CV
        $cv = '';
        $upload_success = false;
        
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/cv/';
            
            // Créer le répertoire s'il n'existe pas
            if (!file_exists(ROOT_PATH . '/' . $upload_dir)) {
                mkdir(ROOT_PATH . '/' . $upload_dir, 0755, true);
            }
            
            // Vérifier le type de fichier
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($_FILES['cv']['tmp_name']);
            
            $allowed_types = ['application/pdf'];
            
            if (!in_array($mime_type, $allowed_types)) {
                $this->setFlashMessage('error', 'Format de fichier non autorisé. Seuls les PDF sont acceptés.');
                header('Location: index.php?route=offre_details&id=' . $offre_id);
                exit;
            }
            
            // Vérifier la taille (max 2 Mo)
            if ($_FILES['cv']['size'] > 2 * 1024 * 1024) {
                $this->setFlashMessage('error', 'Le fichier est trop volumineux (max 2 Mo)');
                header('Location: index.php?route=offre_details&id=' . $offre_id);
                exit;
            }
            
            // Générer un nom de fichier unique
            $filename = uniqid('cv_') . '_' . $_SESSION['user_id'] . '_' . time() . '.pdf';
            $file_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['cv']['tmp_name'], ROOT_PATH . '/' . $file_path)) {
                $cv = $file_path;
                $upload_success = true;
            } else {
                $this->setFlashMessage('error', 'Erreur lors de l\'upload du fichier');
                header('Location: index.php?route=offre_details&id=' . $offre_id);
                exit;
            }
        } else {
            $this->setFlashMessage('error', 'Le CV est obligatoire');
            header('Location: index.php?route=offre_details&id=' . $offre_id);
            exit;
        }
        
        // Enregistrer la candidature
        if ($upload_success) {
            $result = $this->candidatureModel->creerCandidature(
                $_SESSION['user_id'],
                $offre_id,
                $lettre_motivation,
                $cv
            );
            
            if ($result['success']) {
                $this->setFlashMessage('success', 'Votre candidature a été enregistrée avec succès');
                header('Location: index.php?route=confirmation_candidature&id=' . $result['id']);
                exit;
            } else {
                // En cas d'erreur, supprimer le fichier uploadé
                if (file_exists(ROOT_PATH . '/' . $cv)) {
                    unlink(ROOT_PATH . '/' . $cv);
                }
                
                $this->setFlashMessage('error', $result['message']);
                header('Location: index.php?route=offre_details&id=' . $offre_id);
                exit;
            }
        }
    }
    
    /**
     * Méthode pour générer un token CSRF
     */
    public function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Affiche les candidatures de l'utilisateur courant
     */
    public function mesCandidatures() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?route=login');
            exit;
        }
        
        $utilisateur_id = $_SESSION['user_id'];
        $candidatures = $this->candidatureModel->getCandidaturesByUtilisateur($utilisateur_id);
        
        echo $this->render('mes_candidatures', [
            'pageTitle' => 'Mes candidatures - StageLink',
            'candidatures' => $candidatures
        ]);
    }

    public function afficherConfirmation() {
        echo $this->render('confirmation_candidature', [
            'pageTitle' => 'Confirmation de candidature - StageLink'
        ]);
    }
    
    
    /**
     * Traite une candidature (acceptation, refus, etc.)
     */
    public function traiter() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier que l'utilisateur est connecté et a les droits
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
            $this->setFlashMessage('error', 'Accès refusé');
            header('Location: index.php?route=accueil');
            exit;
        }
        
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée');
            header('Location: index.php?route=candidatures');
            exit;
        }
        
        $candidature_id = filter_input(INPUT_POST, 'candidature_id', FILTER_VALIDATE_INT);
        $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);
        
        if (!$candidature_id || !in_array($statut, ['en_attente', 'vue', 'retenue', 'refusee'])) {
            $this->setFlashMessage('error', 'Paramètres invalides');
            header('Location: index.php?route=candidatures');
            exit;
        }
        
        $success = $this->candidatureModel->updateStatut($candidature_id, $statut);
        
        if ($success) {
            $this->setFlashMessage('success', 'Le statut de la candidature a été mis à jour');
        } else {
            $this->setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour');
        }
        
        header('Location: index.php?route=candidatures');
        exit;
    }
}
?>
