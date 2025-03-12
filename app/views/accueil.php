<?php
$pageTitle = $pageTitle ?? "Accueil - StageLink";
include('header.php');
?>

<head>
    <link rel="stylesheet" href="public/css/accueil.css" />
</head>
<section>
    <div class="contain">
        <div class="bloc1">
            <img src="public/images/accueil/iphone-screen1.png" alt="" class="iphone" />
        </div>
        <div class="contexte">
            <div class="icone">
                <img src="public/images/accueil/lock.svg" alt="" class="svg" />
            </div>
            <div class="titre">Authentification sécurisée</div>
            <div class="texte">
                Connexion avec gestion des rôles via email et mot de passe.
            </div>
        </div>
    </div>
    <div class="contain2">
        <div class="bloc2">
            <img src="public/images/accueil/Macbook.png" alt="" class="iphone" />
        </div>
        <div class="contexte">
            <div class="icone2">
                <img src="public/images/accueil/pencil.svg" alt="" class="svg" />
            </div>
            <div class="titre">Gestion des entreprises</div>
            <div class="texte">
                Rechercher, créer, modifier, évaluer et supprimer des entreprises.
            </div>
        </div>
    </div>
    <div class="contain">
        <div class="bloc3">
            <img src="public/images/accueil/offre.svg" alt="" class="iphone" />
        </div>
        <div class="contexte">
            <div class="icone3">
                <img src="public/images/accueil/list.svg" alt="" class="svg" />
            </div>
            <div class="titre">Gestion des offres de stage</div>
            <div class="texte">
                Publier, modifier, supprimer et consulter les statistiques des
                offres.
            </div>
        </div>
    </div>
    <div class="contain2">
        <div class="bloc4">
            <img src="public/images/accueil/utilisateur.svg" alt="" class="iphone2" />
        </div>
        <div class="contexte">
            <div class="icone4">
                <img src="public/images/accueil/compteN.svg" alt="" class="svg" />
            </div>
            <div class="titre">Gestion des utilisateurs</div>
            <div class="texte">
                Créer, modifier et suivre les comptes étudiants et pilotes.
            </div>
        </div>
    </div>
    <div class="contain">
        <div class="bloc5">
            <img src="public/images/accueil/notation.svg" alt="" class="iphone" />
        </div>
        <div class="contexte">
            <div class="icone5">
                <img src="public/images/accueil/star.svg" alt="" class="svg" />
            </div>
            <div class="titre">Candidatures et wish-list</div>
            <div class="texte">
                Postuler aux offres, ajouter/supprimer des offres favorites.
            </div>
        </div>
    </div>
</section>
<section>
    <div class="contain3">
        <img class="img-download" img src="public\images\accueil\telechargement.png" alt="">
        <div class="contexte">
            <div class="titre">Téléchargez l'app <br>
                <span class="gras">pour ne rien manquer</span>
            </div>
            <div class="texte">Faites avancer votre recherche et suivez les nouveaux jobs où que vous soyez.</div>
            <div class="texte gras">Bientôt disponible</div>
            <div class="container-bouton">
                <div class="bouton">
                    <img class="svg-download" src="public\images\accueil\gg-play.png" alt="">
                    <div class="texte">Google Play</div>
                </div>
                <div class="bouton">
                    <img class="svg-download" src="public\images\accueil\apple.svg" alt="">
                    <div class="texte">Apple Store</div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="job">
    <section>
        <div class="contain4">
            <div class="titre">Tout connaître<br>
                <span class="gras">du monde de l'emploi</span>
            </div>
            <div class="texte">Plongez dans l’actualité de l’emploi, découvrez nos conseils pour décrocher votre job
                idéal, vous épanouir au travail, et accédez aux clés pour booster votre vie professionnelle.</div>
        </div>
    </section>
</div>

<?php include("footer.php"); ?>