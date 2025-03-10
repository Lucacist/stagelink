<?php
// Fichier de test pour le formulaire de création d'offre
session_start();
require_once 'config/config.php';
require_once 'app/models/Database.php';
require_once 'app/models/CompetenceModel.php';

// Simuler l'authentification si nécessaire
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // ID temporaire pour le test
    $_SESSION['user_role'] = 'ADMIN'; // Rôle temporaire pour le test
}

// Instantier le modèle de compétences
$competenceModel = new CompetenceModel();
$competences = $competenceModel->getAllCompetences();

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Données du formulaire reçues :</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Insérer les données dans la base
    if (isset($_POST['titre']) && isset($_POST['entreprise_id'])) {
        // Connexion à la base de données
        $db = Database::getInstance();
        
        // Insérer l'offre
        $sql = "INSERT INTO Offres (entreprise_id, titre, description, base_remuneration, date_debut, date_fin) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        
        $entrepriseId = (int)$_POST['entreprise_id'];
        $titre = $_POST['titre'];
        $description = $_POST['description'] ?? '';
        $remuneration = floatval($_POST['base_remuneration'] ?? 0);
        $dateDebut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d');
        $dateFin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d', strtotime('+3 months'));
        
        $stmt->bind_param("issdss", $entrepriseId, $titre, $description, $remuneration, $dateDebut, $dateFin);
        
        if ($stmt->execute()) {
            $offre_id = $db->insert_id;
            echo "<p>Offre créée avec succès! ID: $offre_id</p>";
            
            // Maintenant insérer les compétences
            if (isset($_POST['competences']) && is_array($_POST['competences'])) {
                $sql_comp = "INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)";
                $stmt_comp = $db->prepare($sql_comp);
                
                $successes = 0;
                $errors = 0;
                
                foreach ($_POST['competences'] as $comp_id) {
                    $comp_id = (int)$comp_id;
                    $stmt_comp->bind_param("ii", $offre_id, $comp_id);
                    
                    if ($stmt_comp->execute()) {
                        $successes++;
                    } else {
                        $errors++;
                        echo "<p>Erreur lors de l'ajout de la compétence $comp_id: " . $stmt_comp->error . "</p>";
                    }
                }
                
                echo "<p>Résultat de l'ajout des compétences: $successes réussites, $errors erreurs</p>";
            } else {
                echo "<p>Aucune compétence sélectionnée.</p>";
            }
        } else {
            echo "<p>Erreur lors de la création de l'offre: " . $stmt->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Formulaire Offre</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        form { background: #f8f8f8; padding: 20px; border-radius: 8px; }
        label { display: block; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; max-height: 200px; overflow-y: auto; }
        .checkbox-item { display: flex; align-items: center; }
        .checkbox-item input { width: auto; margin-right: 8px; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test du formulaire d'offre</h1>
    
    <form method="post">
        <div>
            <label for="titre">Titre de l'offre*</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        
        <div>
            <label for="description">Description*</label>
            <textarea id="description" name="description" rows="4" required></textarea>
        </div>
        
        <div>
            <label for="entreprise_id">Entreprise*</label>
            <select id="entreprise_id" name="entreprise_id" required>
                <option value="">Sélectionnez une entreprise</option>
                <?php 
                // Récupérer les entreprises
                $db = Database::getInstance();
                $result = $db->query("SELECT id, nom FROM Entreprises ORDER BY nom");
                while ($row = $result->fetch_assoc()): 
                ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div>
            <label for="base_remuneration">Rémunération (€)*</label>
            <input type="number" id="base_remuneration" name="base_remuneration" step="0.01" required>
        </div>
        
        <div>
            <label for="date_debut">Date de début</label>
            <input type="date" id="date_debut" name="date_debut">
        </div>
        
        <div>
            <label for="date_fin">Date de fin</label>
            <input type="date" id="date_fin" name="date_fin">
        </div>
        
        <div>
            <label>Compétences requises*</label>
            <div class="checkbox-grid">
                <?php if (empty($competences)): ?>
                <p>Aucune compétence disponible. <a href="test_competences.php">Ajouter des compétences</a></p>
                <?php else: ?>
                <?php foreach ($competences as $competence): ?>
                <div class="checkbox-item">
                    <input type="checkbox" 
                           id="comp_<?= $competence['id'] ?>" 
                           name="competences[]" 
                           value="<?= $competence['id'] ?>">
                    <label for="comp_<?= $competence['id'] ?>"><?= htmlspecialchars($competence['nom']) ?></label>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <button type="submit">Tester la création d'offre</button>
    </form>
</body>
</html>
