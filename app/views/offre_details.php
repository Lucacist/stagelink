<?php
include('header.php');
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

        <?php if(!empty($competences)): ?>
        <div class="offre-section">
            <h3>Compétences requises</h3>
            <div class="competences">
                <?php foreach($competences as $competence): ?>
                    <span class="competence-tag"><?= htmlspecialchars($competence['nom']) ?></span>
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

<!-- Section de candidature -->
<?php if(isset($_SESSION['user_id'])): ?>
    <div class="offre-section candidature-section">
        <?php 
        // Vérifier si l'utilisateur a déjà postulé
        require_once ROOT_PATH . '/app/models/CandidatureModel.php';
        require_once ROOT_PATH . '/app/models/Database.php';
        $candidatureModel = new CandidatureModel();
        $dejaPostule = $candidatureModel->candidatureExiste($_SESSION['user_id'], $offre['id']);
        
        if($dejaPostule): 
        ?>
            <div class="alert alert-info">
                Vous avez déjà postulé à cette offre.
            </div>
        <?php else: ?>
            <!-- Vérifier s'il y a des messages flash pour déterminer l'affichage initial -->
            <?php $afficherFormulaire = isset($_SESSION['flash']); ?>
            
            <!-- Bouton pour afficher le formulaire (caché si des messages flash existent) -->
            <button id="postuler-btn" class="btn-primary" <?php echo $afficherFormulaire ? 'style="display: none;"' : ''; ?>>
                Postuler à cette offre
            </button>
            
            <!-- Formulaire de candidature (affiché si des messages flash existent) -->
            <div id="candidature-form-container" class="<?php echo $afficherFormulaire ? '' : 'hidden'; ?>">
                <h3>Postuler à cette offre</h3>
                
                <?php if(isset($_SESSION['flash'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                        <?= $_SESSION['flash']['message'] ?>
                    </div>
                    <?php unset($_SESSION['flash']); ?>
                <?php endif; ?>
                
                <form action="index.php?route=candidature_postuler" method="post" enctype="multipart/form-data" class="candidature-form" id="candidature-form">
                    <input type="hidden" name="csrf_token" value="<?= (new CandidatureController())->generateCsrfToken() ?>">
                    <input type="hidden" name="offre_id" value="<?= $offre['id'] ?>">
                    
                    <div class="form-group">
                        <label for="lettre_motivation">Lettre de motivation</label>
                        <textarea name="lettre_motivation" id="lettre_motivation" class="form-control" rows="6" required><?= isset($_POST['lettre_motivation']) ? htmlspecialchars($_POST['lettre_motivation']) : '' ?></textarea>
                        <small class="form-text text-muted">Expliquez pourquoi vous êtes intéressé par cette offre et ce que vous pouvez apporter.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cv">CV (Format PDF uniquement)</label>
                        <input type="file" name="cv" id="cv" class="form-control-file" accept=".pdf" required>
                        <small class="form-text text-muted">Taille maximale: 2 Mo</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" id="annuler-btn" class="btn-cancel">Annuler</button>
                        <button type="submit" class="btn-primary">Envoyer ma candidature</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="offre-section">
        <p class="connexion-required">
            <a href="index.php?route=login" class="btn-primary">Connectez-vous</a> pour postuler à cette offre.
        </p>
    </div>
<?php endif; ?>



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
