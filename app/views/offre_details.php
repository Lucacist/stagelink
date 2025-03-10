<?php
$pageTitle = "Détails de l'offre - StageLink";
require_once 'config.php';
include('header.php');

// Vérifier si l'ID est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: offres.php');
    exit;
}

$id = (int)$_GET['id'];

// Récupérer les détails de l'offre avec les informations de l'entreprise et le nombre de candidatures
$sql = "SELECT o.*, 
        e.nom AS entreprise_nom, 
        e.email AS entreprise_email,
        e.telephone AS entreprise_telephone,
        COUNT(c.id) as nombre_candidatures,
        GROUP_CONCAT(DISTINCT comp.nom SEPARATOR ', ') as competences
        FROM Offres o
        JOIN Entreprises e ON o.entreprise_id = e.id
        LEFT JOIN Candidatures c ON o.id = c.offre_id
        LEFT JOIN Offres_Competences oc ON o.id = oc.offre_id
        LEFT JOIN Competences comp ON oc.competence_id = comp.id
        WHERE o.id = ?
        GROUP BY o.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$offre = $stmt->get_result()->fetch_assoc();

if (!$offre) {
    header('Location: offres.php');
    exit;
}
?>

<head>
    <link rel="stylesheet" href="public/css/offre-details.css">
</head>

<div class="offre-details">
    <div class="centre">
        <a href="index.php?route=offres" class="navbar">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="16" fill="none" viewBox="0 0 22 16">
                <path fill="#000" d="M21 7a1 1 0 1 1 0 2V7ZM.293 8.707a1 1 0 0 1 0-1.414L6.657.929A1 1 0 0 1 8.07 2.343L2.414 8l5.657 5.657a1 1 0 1 1-1.414 1.414L.293 8.707ZM21 9H1V7h20v2Z"/>
            </svg>
            <div class="texte">Retour</div>
        </a>
    </div>

    <div class="offre-content">
        <div class="offre-header">
            <h2><?= htmlspecialchars($offre['titre']) ?></h2>
            <h3><?= htmlspecialchars($offre['entreprise_nom']) ?></h3>
        </div>

        <div class="offre-section">
            <h3>Description du stage</h3>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
        </div>

        <?php if(!empty($offre['competences'])): ?>
        <div class="offre-section">
            <h3>Compétences requises</h3>
            <div class="competences">
                <?php foreach(explode(', ', $offre['competences']) as $competence): ?>
                    <span class="competence-tag"><?= htmlspecialchars($competence) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="offre-section">
            <h3>Informations pratiques</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Période :</strong>
                    <p>Du <?= date('d/m/Y', strtotime($offre['date_debut'])) ?> au <?= date('d/m/Y', strtotime($offre['date_fin'])) ?></p>
                </div>
                
                <?php if($offre['base_remuneration']): ?>
                <div class="info-item">
                    <strong>Base de rémunération :</strong>
                    <p><?= number_format($offre['base_remuneration'], 2) ?> €</p>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <strong>Candidatures reçues :</strong>
                    <p><?= $offre['nombre_candidatures'] ?></p>
                </div>
            </div>
        </div>

        <div class="offre-section">
            <h3>Contact entreprise</h3>
            <p>
                <strong>Email :</strong> <?= htmlspecialchars($offre['entreprise_email']) ?><br>
                <strong>Téléphone :</strong> <?= htmlspecialchars($offre['entreprise_telephone']) ?>
            </p>
        </div>

        <?php if(isset($_SESSION['email'])): ?>
        <div class="actions">
            <button id="btn-show-form" class="btn-postuler">Postuler à cette offre</button>
            
            <div id="form-candidature" class="form-candidature" style="display: none;">
                <h3>Postuler à cette offre</h3>
                <form action="index.php?route=traiter_candidature" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="offre_id" value="<?= $offre['id'] ?>">
                    
                    <div class="form-group">
                        <label for="cv">CV (PDF uniquement) :</label>
                        <input type="file" id="cv" name="cv" accept=".pdf" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lettre_motivation">Lettre de motivation :</label>
                        <textarea id="lettre_motivation" name="lettre_motivation" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="btn-cancel" class="btn-cancel">Annuler</button>
                        <button type="submit" class="btn-submit">Envoyer ma candidature</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="public/js/candidature.js"></script>

<?php include("footer.php"); ?>
