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
<?php include("footer.php"); ?>