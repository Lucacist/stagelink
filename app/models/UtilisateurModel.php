<?php
class UtilisateurModel {
    private $db;
    
    public function __construct() {
        require_once ROOT_PATH . '/app/models/Database.php';
        $this->db = Database::getInstance();
    }
    
    public function getUserById($userId) {
        $sql = "SELECT * FROM Utilisateurs WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM Utilisateurs WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['mot_de_passe']) || $password === $row['mot_de_passe']) {
                return $row;
            }
        }
        
        return null;
    }
    
    public function getUserRole($userId) {
        $sql = "SELECT r.code as role_code, r.nom as role_nom
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                WHERE u.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        
        return null;
    }
    
    public function getUserPermissions($userId) {
        $sql = "SELECT DISTINCT p.code
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                JOIN Role_Permissions rp ON r.id = rp.role_id
                JOIN Permissions p ON rp.permission_id = p.id
                WHERE u.id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['code'];
        }
        
        return $permissions;
    }
    
    public function hasPermission($userId, $permissionCode) {
        $sql = "SELECT COUNT(*) as count 
                FROM Utilisateurs u
                JOIN Roles r ON u.role_id = r.id
                JOIN Role_Permissions rp ON r.id = rp.role_id
                JOIN Permissions p ON rp.permission_id = p.id
                WHERE u.id = ? AND p.code = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $userId, $permissionCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
}
?>
