<?php
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";  // Par défaut dans WAMP/XAMPP
        $password = "";      // Laisse vide si aucun mot de passe défini
        $dbname = "StageLink"; 
        
        // Création de la connexion
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
        // Vérification de la connexion
        if ($this->conn->connect_error) {
            die("Échec de la connexion : " . $this->conn->connect_error);
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Méthode pour exécuter une requête directement
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    // Méthode pour préparer une requête
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    // Méthode pour obtenir l'ID de la dernière insertion
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }
}
?>
