<?php
$error = $error ?? "";
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

            <?php if (!empty($error)): ?>
                <div class="message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?route=login">
                <div class="bloc-input">
                    <label for="">Adresse mail <input type="email" name="email" required /></label>
                    <label for="">Mot de passe 
                        <input type="password" name="mot_de_passe" required />
                        <div class="password-icon">
                            <i data-feather="eye"></i>
                            <i data-feather="eye-off"></i>
                        </div>
                    </label>
                </div>
                <button type="submit">Je me connecte</button>
            </form>
        </div>
    </div>
</body>
<script src="https://unpkg.com/feather-icons"></script>
<script>
    feather.replace();
    const eye = document.querySelector(".feather-eye");
const eyeoff = document.querySelector(".feather-eye-off");
const passwordField = document.querySelector("input[type=password]");
eye.addEventListener("click", () => {
  eye.style.display = "none";
  eyeoff.style.display = "block";
  passwordField.type = "text";
});

eyeoff.addEventListener("click", () => {
  eyeoff.style.display = "none";
  eye.style.display = "block";
  passwordField.type = "password";
});
</script>

</html>