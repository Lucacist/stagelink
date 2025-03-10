<?php
require_once 'config/config.php';
require_once 'app/models/Database.php';

// Connexion à la base de données
$db = Database::getInstance();

// 1. Vérifier la structure de la table Competences
echo "<h2>Structure de la table Competences</h2>";
$result = $db->query("DESCRIBE Competences");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// 2. Vérifier la structure de la table Offres_Competences
echo "<h2>Structure de la table Offres_Competences</h2>";
$result = $db->query("DESCRIBE Offres_Competences");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// 3. Insérer quelques compétences de test
if (isset($_GET['insert'])) {
    // Vider la table des compétences
    $db->query("TRUNCATE TABLE Competences");
    
    $competences = [
        'PHP', 'JavaScript', 'HTML/CSS', 'SQL', 'Java', 'Python', 
        'C#', 'React', 'Angular', 'Vue.js', 'Node.js', 'TypeScript',
        'Docker', 'Git', 'AWS', 'Azure', 'REST API', 'MongoDB', 'MySQL'
    ];
    
    $successCount = 0;
    foreach ($competences as $competence) {
        $stmt = $db->prepare("INSERT INTO Competences (nom) VALUES (?)");
        $stmt->bind_param("s", $competence);
        if ($stmt->execute()) {
            $successCount++;
        }
    }
    
    echo "<div style='background-color: #dff0d8; padding: 15px; margin: 15px 0; border-radius: 4px;'>";
    echo "<p>$successCount compétences ont été ajoutées avec succès!</p>";
    echo "</div>";
}

// 4. Afficher les compétences actuelles
echo "<h2>Compétences actuelles</h2>";
$result = $db->query("SELECT * FROM Competences ORDER BY nom");
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: {$row['id']} - Nom: {$row['nom']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucune compétence trouvée dans la base de données.</p>";
}

// 5. Bouton pour insérer des compétences de test
echo "<p><a href='insert_competences.php?insert=1' style='display: inline-block; padding: 10px 20px; background-color: #5bc0de; color: white; text-decoration: none; border-radius: 4px;'>Insérer des compétences de test</a></p>";

// 6. Vérifier les tables dans la base de données
echo "<h2>Tables dans la base de données</h2>";
$result = $db->query("SHOW TABLES");
echo "<ul>";
while ($row = $result->fetch_row()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// 7. Test d'insertion directe dans Offres_Competences
if (isset($_GET['test_insert'])) {
    // Récupérer un ID d'offre existant
    $result = $db->query("SELECT id FROM Offres ORDER BY id DESC LIMIT 1");
    $offre_id = $result->fetch_assoc()['id'] ?? 0;
    
    // Récupérer un ID de compétence existant
    $result = $db->query("SELECT id FROM Competences ORDER BY id ASC LIMIT 1");
    $competence_id = $result->fetch_assoc()['id'] ?? 0;
    
    if ($offre_id > 0 && $competence_id > 0) {
        $stmt = $db->prepare("INSERT INTO Offres_Competences (offre_id, competence_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $offre_id, $competence_id);
        
        if ($stmt->execute()) {
            echo "<div style='background-color: #dff0d8; padding: 15px; margin: 15px 0; border-radius: 4px;'>";
            echo "<p>Test d'insertion réussi! Offre ID: $offre_id, Compétence ID: $competence_id</p>";
            echo "</div>";
        } else {
            echo "<div style='background-color: #f2dede; padding: 15px; margin: 15px 0; border-radius: 4px;'>";
            echo "<p>Erreur lors du test d'insertion: " . $stmt->error . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background-color: #f2dede; padding: 15px; margin: 15px 0; border-radius: 4px;'>";
        echo "<p>Impossible de trouver une offre ou une compétence pour le test.</p>";
        echo "</div>";
    }
}

echo "<p><a href='insert_competences.php?test_insert=1' style='display: inline-block; padding: 10px 20px; background-color: #d9534f; color: white; text-decoration: none; border-radius: 4px;'>Tester l'insertion dans Offres_Competences</a></p>";

// 8. Afficher le contenu de la table Offres_Competences
echo "<h2>Associations Offres-Compétences existantes</h2>";
$result = $db->query("SELECT oc.*, o.titre, c.nom 
                     FROM Offres_Competences oc
                     LEFT JOIN Offres o ON oc.offre_id = o.id
                     LEFT JOIN Competences c ON oc.competence_id = c.id");

if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID Relation</th><th>ID Offre</th><th>Titre Offre</th><th>ID Compétence</th><th>Nom Compétence</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . ($row['id'] ?? 'N/A') . "</td>";
        echo "<td>" . $row['offre_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['titre'] ?? 'N/A') . "</td>";
        echo "<td>" . $row['competence_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nom'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Aucune association trouvée dans la base de données.</p>";
}
?>
