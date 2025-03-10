<?php
// require_once 'config.php';
// require_once 'utils/permissions.php';

// // Vérifier si l'utilisateur a la permission de créer une offre
// if (!isset($_SESSION['user_id']) || !hasPermission($_SESSION['user_id'], 'CREER_OFFRE')) {
//     header('Location: accueil.php');
//     exit();
// }

$message = "";

// // Récupérer la liste des entreprises
// $entreprises = $conn->query("SELECT id, nom FROM Entreprises ORDER BY nom");

// // Récupérer la liste des compétences
// $competences = $conn->query("SELECT id, nom FROM Competences ORDER BY nom");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $remuneration = trim($_POST['remuneration']);
    $date_debut = !empty($_POST['date_debut']) ? $_POST['date_debut'] : null;
    $date_fin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : null;
    $entreprise_id = isset($_POST['entreprise_id']) ? $_POST['entreprise_id'] : null;
    $competences_selectionnees = isset($_POST['competences']) ? $_POST['competences'] : [];

    // Validation des données
    $erreurs = [];
    if (empty($titre)) $erreurs[] = "Le titre est requis.";
    if (empty($description)) $erreurs[] = "La description est requise.";
    if (empty($remuneration)) $erreurs[] = "La rémunération est requise.";
    if (empty($entreprise_id)) $erreurs[] = "L'entreprise est requise.";
    if (empty($competences_selectionnees)) $erreurs[] = "Au moins une compétence est requise.";
    if (!empty($date_debut) && !empty($date_fin) && strtotime($date_fin) < strtotime($date_debut)) {
        $erreurs[] = "La date de fin doit être postérieure à la date de début.";
    }

    if (empty($erreurs)) {
        try {
            // Démarrer une transaction
            // $conn->begin_transaction();

            // Debug des valeurs
            // error_log("Données reçues : " . print_r([
            //     'entreprise_id' => $entreprise_id,
            //     'titre' => $titre,
            //     'description' => $description,
            //     'remuneration' => $remuneration,
            //     'date_debut' => $date_debut,
            //     'date_fin' => $date_fin
            // ], true));

            // // Insérer l'offre dans la table Offres
            // $sql = "INSERT INTO Offres (entreprise_id, titre, description, base_remuneration, date_debut, date_fin) 
            //         VALUES (?, ?, ?, ?, ?, ?)";
            // $stmt = $conn->prepare($sql);
            
            // // Convertir la rémunération en décimal
            // $remuneration = floatval($remuneration);
            
            // $stmt->bind_param("issdss", 
            //     $entreprise_id, 
            //     $titre, 
            //     $description, 
            //     $remuneration,
            //     $date_debut,
            //     $date_fin
            // );

            // if ($stmt->execute()) {
            //     $offre_id = $stmt->insert_id;

            //     // // Insérer les compétences liées dans Offres_Competences
            //     // if (!empty($competences_selectionnees)) {
            //     //     $sql_comp = "INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)";
            //     //     $stmt_comp = $conn->prepare($sql_comp);
                    
            //     //     foreach ($competences_selectionnees as $competence_id) {
            //     //         $stmt_comp->bind_param("ii", $offre_id, $competence_id);
            //     //         if (!$stmt_comp->execute()) {
            //     //             throw new Exception("Erreur lors de l'ajout des compétences");
            //     //         }
            //     //     }
            //     // }

            //     // // Valider la transaction
            //     // $conn->commit();
                
            //     // $_SESSION['success_message'] = "L'offre a été créée avec succès !";
            //     // header("Location: offres.php");
            //     // exit();
            // } else {
            //     throw new Exception("Erreur lors de la création de l'offre : " . $stmt->error);
            // }
        } catch (Exception $e) {
            // // En cas d'erreur, annuler la transaction
            // $conn->rollback();
            // error_log("Erreur SQL : " . $e->getMessage());
            // $message = "<p class='error'>Erreur lors de la création de l'offre : " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p class='error'>" . implode("<br>", $erreurs) . "</p>";
    }
}
?>

<div class="container">
    <h2>Créer une nouvelle offre</h2>
    <?= $message ?>
    
    <form method="post" action="creer-offre.php">
        <div class="form-group">
            <label for="titre">Titre de l'offre* :</label>
            <input type="text" id="titre" name="titre" value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description* :</label>
            <textarea id="description" name="description" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        </div>

        <div class="form-group">
            <label for="remuneration">Rémunération (€)* :</label>
            <input type="number" id="remuneration" name="remuneration" step="0.01" value="<?= isset($_POST['remuneration']) ? htmlspecialchars($_POST['remuneration']) : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="entreprise_id">Entreprise* :</label>
            <select id="entreprise_id" name="entreprise_id" required>
                <option value="">Sélectionnez une entreprise</option>
                <?php // while ($row = $entreprises->fetch_assoc()): ?>
                <?php // endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="date_debut">Date de début :</label>
            <input type="date" id="date_debut" name="date_debut" value="<?= isset($_POST['date_debut']) ? htmlspecialchars($_POST['date_debut']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="date_fin">Date de fin :</label>
            <input type="date" id="date_fin" name="date_fin" value="<?= isset($_POST['date_fin']) ? htmlspecialchars($_POST['date_fin']) : '' ?>">
        </div>

        <div class="competences-container">
            <label>Compétences requises* :</label>
            <div class="checkbox-grid">
                <?php 
                // $selected_competences = isset($_POST['competences']) ? $_POST['competences'] : [];
                // $competences->data_seek(0); // Réinitialiser le pointeur des résultats
                // while ($row = $competences->fetch_assoc()): 
                ?>
                <?php // endwhile; ?>
            </div>
            <p class="info-text">Sélectionnez au moins une compétence requise pour l'offre.</p>
        </div>

        <button type="submit" class="btn-primary">Créer l'offre</button>
    </form>
</div>

<script src="public/js/offre-validation.js"></script>