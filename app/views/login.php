<?php
// Les inclusions suivantes ne sont plus nécessaires car elles sont gérées par le contrôleur
// require_once 'config.php';
// require_once 'utils/permissions.php';

// Variable pour le message (sera fournie par le contrôleur)
$error = $error ?? "";

// La logique de connexion a été déplacée dans le contrôleur AuthController
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="public/css/variable.css" />
    <link rel="stylesheet" href="public/css/login.css" />
    <link rel="icon" href="public/images/favicon.svg" type="image/svg" />
    <title><?php echo $pageTitle ?? 'Connexion - StageLink'; ?></title>
</head>

<body>
    <div class="container">
        <div class="stagelink">StageLink</div>
        <div class="form-container">
            <div class="texte-intro">
                Ravi de vous retrouver sur StageLink !<br />
                Retrouvez toutes vos offres et candidatures en vous connectant :
            </div>

            <!-- Bloc de message pour afficher les erreurs -->
            <?php if (!empty($error)): ?>
            <div class="message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?route=login">
                <div class="bloc-input">
                    <label for="">Adresse mail <input type="email" name="email" required /></label>
                    <label for="">Mot de passe <input type="password" name="mot_de_passe" required /></label>
                </div>
                <button type="submit">Je me connecte</button>
            </form>
        </div>
    </div>
</body>

</html>