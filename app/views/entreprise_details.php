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
            <h2>Notation de l'entreprise</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="add-avis">
                <h3>Notez cette entreprise</h3>
                
                <!-- Système d'évaluation avec étoiles -->
                <form action="index.php?route=rate_entreprise" method="POST" id="rating-form">
                    <input type="hidden" name="entreprise_id" value="<?= $entreprise['id'] ?>">
                    <input type="hidden" name="note" id="selected-rating" value="">
                    
                    <div style="margin-bottom: 20px;">
                        <p style="margin-bottom: 10px;">Votre note :</p>
                        <div class="star-rating">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                            <span id="rating-text" style="margin-left: 10px; font-size: 14px;"></span>
                        </div>
                    </div>
                    
                    <button type="submit" id="submit-rating" style="display: block; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        Envoyer votre note
                    </button>
                </form>
                
                <style>
                    .star-rating {
                        display: flex;
                        align-items: center;
                    }
                    .star {
                        font-size: 30px;
                        color: #ddd;
                        cursor: pointer;
                        transition: color 0.2s;
                    }
                    .star.active {
                        color: #FFD700;
                    }
                    #submit-rating {
                        opacity: 0.5;
                        pointer-events: none;
                    }
                    #submit-rating.active {
                        opacity: 1;
                        pointer-events: auto;
                    }
                </style>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const stars = document.querySelectorAll('.star');
                        const ratingInput = document.getElementById('selected-rating');
                        const submitButton = document.getElementById('submit-rating');
                        const ratingText = document.getElementById('rating-text');
                        const ratingLabels = ['Très mauvais', 'Mauvais', 'Moyen', 'Bon', 'Excellent'];
                        
                        // Désactiver le bouton d'envoi jusqu'à ce qu'une note soit sélectionnée
                        submitButton.classList.remove('active');
                        
                        stars.forEach((star, index) => {
                            star.addEventListener('mouseover', function() {
                                // Effet de survol
                                for (let i = 0; i <= index; i++) {
                                    stars[i].classList.add('active');
                                }
                                for (let i = index + 1; i < stars.length; i++) {
                                    stars[i].classList.remove('active');
                                }
                                ratingText.textContent = ratingLabels[index];
                            });
                            
                            star.addEventListener('mouseout', function() {
                                // Rétablir l'affichage si aucune note n'est sélectionnée
                                if (!ratingInput.value) {
                                    stars.forEach(s => s.classList.remove('active'));
                                    ratingText.textContent = '';
                                } else {
                                    // Sinon, afficher la note sélectionnée
                                    const selectedIndex = parseInt(ratingInput.value) - 1;
                                    for (let i = 0; i <= selectedIndex; i++) {
                                        stars[i].classList.add('active');
                                    }
                                    for (let i = selectedIndex + 1; i < stars.length; i++) {
                                        stars[i].classList.remove('active');
                                    }
                                    ratingText.textContent = ratingLabels[selectedIndex];
                                }
                            });
                            
                            star.addEventListener('click', function() {
                                const value = this.getAttribute('data-value');
                                ratingInput.value = value;
                                
                                // Activer le bouton d'envoi
                                submitButton.classList.add('active');
                                
                                // Mettre à jour l'affichage des étoiles
                                for (let i = 0; i <= index; i++) {
                                    stars[i].classList.add('active');
                                }
                                for (let i = index + 1; i < stars.length; i++) {
                                    stars[i].classList.remove('active');
                                }
                                
                                ratingText.textContent = ratingLabels[index];
                            });
                        });
                        
                        // Validation du formulaire
                        document.getElementById('rating-form').addEventListener('submit', function(e) {
                            if (!ratingInput.value) {
                                e.preventDefault();
                                alert('Veuillez sélectionner une note avant d\'envoyer.');
                            }
                        });
                    });
                </script>
            </div>
            <?php endif; ?>
            
            <?php if (empty($evaluations)): ?>
                <p class="no-avis">Aucune note pour le moment</p>
            <?php else: ?>
                <div class="avis-list">
                    <div class="rating-distribution">
                        <h3>Distribution des notes (<?= count($evaluations) ?> notes)</h3>
                        <div class="rating-bars">
                            <?php 
                            $total = count($evaluations);
                            $counts = array_fill(1, 5, 0);
                            foreach ($evaluations as $avis) {
                                $counts[$avis['note']]++;
                            }
                            for ($i = 5; $i >= 1; $i--): 
                                $percentage = $total > 0 ? ($counts[$i] / $total) * 100 : 0;
                            ?>
                            <div class="rating-bar">
                                <div class="rating-label"><?= $i ?> ★</div>
                                <div class="bar-container">
                                    <div class="bar" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <div class="rating-count"><?= $counts[$i] ?></div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>