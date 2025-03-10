<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/OffreModel.php';

class CandidatureController extends Controller {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/app/models/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index() {
        // Vérifier les permissions
        $this->checkPageAccess('VOIR_CANDIDATURES');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Déterminer si l'utilisateur voit ses propres candidatures ou toutes les candidatures (selon son rôle)
        $isAdmin = $this->hasPermission('GERER_CANDIDATURES');
        
        // Requête SQL pour récupérer les candidatures
        if ($isAdmin) {
            // Administrateur ou pilote: voir toutes les candidatures
            $sql = "SELECT c.*, o.titre as offre_titre, e.nom as entreprise_nom, u.nom as etudiant_nom, u.prenom as etudiant_prenom
                    FROM Candidatures c
                    JOIN Offres o ON c.offre_id = o.id
                    JOIN Entreprises e ON o.entreprise_id = e.id
                    JOIN Utilisateurs u ON c.utilisateur_id = u.id
                    ORDER BY c.date_candidature DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        } else {
            // Étudiant: voir uniquement ses propres candidatures
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
        
        // Afficher la liste des candidatures
        echo $this->render('traiter_candidature', [
            'pageTitle' => 'Candidatures - StageLink',
            'candidatures' => $candidatures,
            'isAdmin' => $isAdmin
        ]);
    }
    
    public function postuler() {
        // Vérifier que l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
            $lettre = $_POST['lettre_motivation'] ?? '';
            
            // Gestion du téléchargement du CV
            $cvPath = '';
            if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['cv']['tmp_name'];
                $fileName = basename($_FILES['cv']['name']);
                $uploadDir = ROOT_PATH . '/public/uploads/cv/';
                
                // Créer le répertoire s'il n'existe pas
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Générer un nom unique pour éviter les conflits
                $cvPath = 'cv_' . $_SESSION['user_id'] . '_' . time() . '_' . $fileName;
                
                // Déplacer le fichier
                move_uploaded_file($tmpName, $uploadDir . $cvPath);
            }
            
            if ($offreId > 0 && !empty($cvPath)) {
                // Insérer la candidature
                $sql = "INSERT INTO Candidatures (utilisateur_id, offre_id, date_candidature, cv, lettre_motivation) 
                        VALUES (?, ?, NOW(), ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("iiss", $_SESSION['user_id'], $offreId, $cvPath, $lettre);
                $stmt->execute();
                
                // Rediriger vers les détails de l'offre
                $this->redirect('offre_details&id=' . $offreId . '&message=candidature_success');
            } else {
                // Rediriger vers les détails de l'offre avec un message d'erreur
                $this->redirect('offre_details&id=' . $offreId . '&message=candidature_error');
            }
        } else {
            // Rediriger vers la liste des offres si ce n'est pas une requête POST
            $this->redirect('offres');
        }
    }
    
    public function traiter() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
            return;
        }
        
        $utilisateur_id = $_SESSION['user_id'];
        
        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('offres');
            return;
        }
        
        // Récupérer les données du formulaire
        $offre_id = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
        $lettre_motivation = trim($_POST['lettre_motivation'] ?? '');
        $cv_file = isset($_FILES['cv']) ? $_FILES['cv'] : null;
        
        // Validation des données
        $errors = [];
        
        if ($offre_id <= 0) {
            $errors[] = "L'ID de l'offre est invalide.";
        }
        
        if (empty($lettre_motivation)) {
            $errors[] = "La lettre de motivation est requise.";
        }
        
        // Validation du fichier CV
        $cv_path = '';
        if ($cv_file && $cv_file['error'] == 0) {
            // Vérifier le type de fichier (PDF uniquement)
            $allowed_types = ['application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $cv_file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                $errors[] = "Le CV doit être au format PDF.";
            } else {
                // Créer le dossier uploads/cv s'il n'existe pas
                $upload_dir = ROOT_PATH . '/uploads/cv/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Générer un nom de fichier unique
                $cv_filename = uniqid('cv_') . '.pdf';
                $cv_path = '/uploads/cv/' . $cv_filename;
                $target_file = ROOT_PATH . $cv_path;
                
                // Déplacer le fichier téléchargé
                if (!move_uploaded_file($cv_file['tmp_name'], $target_file)) {
                    $errors[] = "Une erreur s'est produite lors du téléchargement du CV.";
                    $cv_path = '';
                }
            }
        } else {
            $errors[] = "Le CV est requis.";
        }
        
        // Vérifier si l'utilisateur a déjà candidaté pour cette offre
        $stmt = $this->db->prepare("SELECT id FROM Candidatures WHERE utilisateur_id = ? AND offre_id = ?");
        $stmt->bind_param("ii", $utilisateur_id, $offre_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Vous avez déjà candidaté pour cette offre.";
        }
        
        // S'il n'y a pas d'erreurs, enregistrer la candidature
        if (empty($errors)) {
            $date_candidature = date('Y-m-d H:i:s');
            $statut = 'en attente';
            
            $stmt = $this->db->prepare("INSERT INTO Candidatures (utilisateur_id, offre_id, date_candidature, lettre_motivation, cv_path, statut) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $utilisateur_id, $offre_id, $date_candidature, $lettre_motivation, $cv_path, $statut);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Votre candidature a été enregistrée avec succès.";
            } else {
                $_SESSION['error_message'] = "Une erreur s'est produite lors de l'enregistrement de votre candidature.";
                // Supprimer le fichier CV si l'insertion en base a échoué
                if (!empty($cv_path)) {
                    @unlink(ROOT_PATH . $cv_path);
                }
            }
        } else {
            $_SESSION['error_message'] = implode('<br>', $errors);
            // Supprimer le fichier CV si des erreurs ont été rencontrées
            if (!empty($cv_path)) {
                @unlink(ROOT_PATH . $cv_path);
            }
        }
        
        // Rediriger vers la page de détails de l'offre
        $this->redirect('offre_details&id=' . $offre_id);
    }
}
?>
