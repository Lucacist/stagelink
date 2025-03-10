<?php
require_once ROOT_PATH . '/app/controllers/Controller.php';
require_once ROOT_PATH . '/app/models/OffreModel.php';

class WishlistController extends Controller {
    private $offreModel;
    
    public function __construct() {
        $this->offreModel = new OffreModel();
    }
    
    public function toggleLike() {
        // Vérifier que l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer l'ID de l'offre
            $offreId = isset($_POST['offre_id']) ? (int)$_POST['offre_id'] : 0;
            
            if ($offreId > 0) {
                // Ajouter/supprimer l'offre de la wishlist
                $this->offreModel->toggleLike($offreId, $_SESSION['user_id']);
            }
            
            // Rediriger vers la page précédente ou la liste des offres
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?route=offres';
            header('Location: ' . $referer);
            exit();
        } else {
            // Rediriger vers la liste des offres si ce n'est pas une requête POST
            $this->redirect('offres');
        }
    }
    
    public function index() {
        // Vérifier que l'utilisateur est connecté
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        
        // Récupérer toutes les offres aimées par l'utilisateur
        $sql = "SELECT o.*, e.nom as entreprise_nom 
                FROM Offres o
                JOIN Entreprises e ON o.entreprise_id = e.id
                JOIN WishList w ON o.id = w.offre_id
                WHERE w.utilisateur_id = ?
                ORDER BY o.date_debut DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $offres = [];
        while ($row = $result->fetch_assoc()) {
            $offres[] = $row;
        }
        
        // Afficher la liste des offres favorites
        echo $this->render('like', [
            'pageTitle' => 'Mes offres favorites - StageLink',
            'offres' => $offres
        ]);
    }
}
?>
