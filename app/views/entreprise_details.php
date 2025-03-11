<?php
include('header.php');
?>

<head>
    <link rel="stylesheet" href="public/css/entreprise-detail.css">
</head>

<div class="entreprise-details">
    <div class="centre">
        <a href="index.php?route=entreprises" class="navbar">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="16" fill="none" viewBox="0 0 22 16">
                <path fill="#000" d="M21 7a1 1 0 1 1 0 2V7ZM.293 8.707a1 1 0 0 1 0-1.414L6.657.929A1 1 0 0 1 8.07 2.343L2.414 8l5.657 5.657a1 1 0 1 1-1.414 1.414L.293 8.707ZM21 9H1V7h20v2Z"/>
            </svg>
            <div class="texte">Retour</div>
        </a>
    </div>

    <div class="entreprise-content">
        <div class="entreprise-header">
            <h1><?= htmlspecialchars($entreprise['nom']) ?></h1>
        </div>

        <div class="info-box rating-box">
            <div class="rating-stars">
                <?php 
                $noteValue = isset($entreprise['note_moyenne']) ? $entreprise['note_moyenne'] : 0;
                $note = min(5, max(0, round($noteValue * 2) / 2)); 
                
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $note) {
                        echo '<span class="star full">★</span>';
                    } else if ($i - 0.5 == $note) {
                        echo '<span class="star half">★</span>';
                    } else {
                        echo '<span class="star empty">★</span>';
                    }
                }
                ?>
            </div>
            <div class="rating-value"><?= number_format($note, 1) ?>/5</div>
            <div class="rating-count">(<?= isset($entreprise['nombre_evaluations']) ? $entreprise['nombre_evaluations'] : 0 ?> avis)</div>
        </div>

        <div class="entreprise-section">
            <h2>À propos</h2>
            <p><?= nl2br(htmlspecialchars($entreprise['description'])) ?></p>
        </div>

        <div class="entreprise-section contact-section">
            <h2>Contact</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($entreprise['email']) ?></span>
                </div>
                <div class="contact-item">
                    <strong>Téléphone:</strong>
                    <span><?= htmlspecialchars($entreprise['telephone']) ?></span>
                </div>
            </div>
        </div>

        <div class="entreprise-section offres-section">
            <h2>Offres de stage disponibles</h2>
            <?php if (empty($offres)): ?>
                <p class="no-offres">Aucune offre disponible actuellement</p>
            <?php else: ?>
                <div class="offres-grid">
                    <?php foreach ($offres as $offre): ?>
                    <div class="offre-card">
                        <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                        <p class="offre-dates">
                            Du <?= date('d/m/Y', strtotime($offre['date_debut'])) ?> 
                            au <?= date('d/m/Y', strtotime($offre['date_fin'])) ?>
                        </p>
                        <p class="offre-desc"><?= substr(htmlspecialchars($offre['description']), 0, 100) ?>...</p>
                        <p class="offre-remuneration"><?= number_format($offre['base_remuneration'], 2) ?> €</p>
                        <p class="offre-candidatures"><?= $offre['nombre_candidatures'] ?> candidature(s)</p>
                        <a href="index.php?route=offre_details&id=<?= $offre['id'] ?>" class="btn-voir">Voir détails</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="entreprise-section avis-section">
            <h2>Avis des étudiants</h2>
            <?php if (isset($_SESSION['user_id']) && !isset($entreprise['user_has_rated'])): ?>
            <div class="add-avis">
                <h3>Donnez votre avis</h3>
                <form action="index.php?route=ajouter_evaluation" method="POST" class="avis-form">
                    <input type="hidden" name="entreprise_id" value="<?= $entreprise['id'] ?>">
                    
                    <div class="rating-input">
                        <p>Votre note :</p>
                        <div class="stars-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="note" id="star<?= $i ?>" value="<?= $i ?>">
                            <label for="star<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="commentaire">Votre commentaire :</label>
                        <textarea id="commentaire" name="commentaire" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Envoyer</button>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if (empty($evaluations)): ?>
                <p class="no-avis">Aucun avis pour le moment</p>
            <?php else: ?>
                <div class="avis-list">
                    <?php foreach ($evaluations as $avis): ?>
                    <div class="avis-item">
                        <div class="avis-header">
                            <div class="avis-user"><?= htmlspecialchars($avis['prenom']) ?> <?= htmlspecialchars($avis['nom']) ?></div>
                            <div class="avis-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $avis['note'] ? 'full' : 'empty' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="avis-content">
                            <p><?= nl2br(htmlspecialchars($avis['commentaire'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>