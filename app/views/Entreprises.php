<?php
include('header.php');
?>

<head>
    <link rel="stylesheet" href="public/css/entreprises.css">
    <link rel="stylesheet" href="public/css/variable.css">
    <link rel="icon" href="public/images/favicon.svg" type="image/svg" />
</head>

<div class="contenu">
    <h1>Entreprises</h1>

    <?php if (empty($entreprises)): ?>
        <div class="message">Aucune entreprise disponible pour le moment.</div>
    <?php else: ?>
        <div class="entreprises-container">
            <?php foreach ($entreprises as $entreprise): ?>
                <a href="index.php?route=entreprise_details&id=<?= $entreprise['id'] ?>" class="entreprise-link">
                    <div class="container">
                        <?php if (!empty($entreprise['logo'])): ?>
                            <img src="public/images/entreprises/<?= htmlspecialchars($entreprise['logo']) ?>"
                                alt="Logo <?= htmlspecialchars($entreprise['nom']) ?>" class="logo-entreprise">
                        <?php endif; ?>
                        <div class="entreprise-info">
                            <h2><?= htmlspecialchars($entreprise['nom']) ?></h2>
                            <div class="container-description">
                                <div class="description">
                                    <?= substr(htmlspecialchars($entreprise['description']), 0, 200) . (strlen($entreprise['description']) > 200 ? '...' : '') ?>
                                </div>

                                <div class="note-container">
                                    <div class="note">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span
                                                class="etoile <?= $i <= round($entreprise['note_moyenne']) ? 'active' : '' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="nombre-avis">(<?= $entreprise['nombre_avis'] ?> avis)</div>
                                </div>
                            </div>

                            <?php if (isset($entreprise['secteur'])): ?>
                                <div class="secteur"><?= htmlspecialchars($entreprise['secteur']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="public/js/search.js"></script>

<?php include('footer.php'); ?>