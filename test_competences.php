<?php
require_once 'config/config.php';
require_once 'app/models/Database.php';

// Connexion à la base de données
$db = Database::getInstance();

// Vérifier les tables
echo "<h2>Vérification des tables</h2>";

// 1. Vérifier la table Competences
$result = $db->query("SELECT * FROM Competences");
echo "<h3>Compétences</h3>";
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: {$row['id']} - Nom: {$row['nom']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucune compétence trouvée dans la base de données.</p>";
    
    // Proposer d'ajouter des compétences de test
    echo "<form method='post'>";
    echo "<p>Voulez-vous ajouter des compétences de test?</p>";
    echo "<button type='submit' name='add_competences'>Ajouter des compétences</button>";
    echo "</form>";
}

// 2. Vérifier la table Offres_Competences
$result = $db->query("SELECT oc.*, o.titre as offre_titre, c.nom as competence_nom FROM Offres_Competences oc 
                      LEFT JOIN Offres o ON oc.offre_id = o.id 
                      LEFT JOIN Competences c ON oc.competence_id = c.id");
echo "<h3>Relations Offres-Compétences</h3>";
if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>Offre ID: {$row['offre_id']} ({$row['offre_titre']}) - Compétence ID: {$row['competence_id']} ({$row['competence_nom']})</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucune relation offre-compétence trouvée dans la base de données.</p>";
}

// Si l'utilisateur a demandé d'ajouter des compétences de test
if (isset($_POST['add_competences'])) {
    $competences = [
        'PHP', 'JavaScript', 'HTML', 'CSS', 'SQL', 'Java', 'Python', 
        'C#', 'ReactJS', 'Angular', 'Vue.js', 'Node.js', 'TypeScript',
        'Docker', 'Kubernetes', 'Git', 'AWS', 'Azure', 'Google Cloud',
        'API REST', 'GraphQL', 'MongoDB', 'MySQL', 'PostgreSQL'
    ];
    
    $count = 0;
    foreach ($competences as $competence) {
        $stmt = $db->prepare("INSERT INTO Competences (nom) VALUES (?)");
        $stmt->bind_param("s", $competence);
        if ($stmt->execute()) {
            $count++;
        }
    }
    
    echo "<p>$count compétences ont été ajoutées avec succès!</p>";
    echo "<p><a href='test_competences.php'>Rafraîchir la page</a></p>";
}
?>
