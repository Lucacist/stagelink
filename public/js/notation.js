document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".stars").forEach((starsContainer) => {
    const stars = starsContainer.querySelectorAll(".star");
    const entrepriseId = starsContainer.dataset.entrepriseId;
    let userRating = starsContainer.dataset.rating;

    // Initialiser les étoiles avec la note de l'utilisateur si elle existe
    if (userRating) {
      updateStarsDisplay(stars, userRating);
    }

    stars.forEach((star) => {
      // Gestion du survol
      star.addEventListener("mouseover", function () {
        updateStarsDisplay(stars, this.dataset.value);
      });

      // Rétablir l'affichage initial quand la souris quitte la zone
      starsContainer.addEventListener("mouseleave", function () {
        updateStarsDisplay(stars, userRating);
      });

      // Gestion du clic
      star.addEventListener("click", async function (e) {
        e.preventDefault();
        
        if (!confirm("Voulez-vous vraiment noter cette entreprise ?")) {
          return false;
        }

        try {
          const rating = this.dataset.value;
          const response = await fetch("/WEB4ALL-stagelink/index.php?route=rate_entreprise", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `entreprise_id=${entrepriseId}&note=${rating}`,
          });

          const data = await response.json();

          if (data.success) {
            // Mettre à jour la note utilisateur
            starsContainer.dataset.rating = rating;
            userRating = rating;

            // Mettre à jour le compteur d'avis
            const avisCount = starsContainer.parentElement.querySelector(".avis-count");
            if (avisCount) {
              avisCount.textContent = data.nombre_avis + " avis";
            }

            // Mettre à jour les étoiles
            updateStarsDisplay(stars, rating);
            alert("Merci pour votre évaluation !");
          } else {
            throw new Error(data.message || "Erreur lors de l'évaluation");
          }
        } catch (error) {
          console.error("Erreur:", error);
          alert("Une erreur est survenue lors de l'évaluation");
        }
        
        return false;
      });
    });
  });
});

// Fonction utilitaire pour mettre à jour l'affichage des étoiles
function updateStarsDisplay(stars, rating) {
  stars.forEach((s) => {
    if (s.dataset.value <= rating) {
      s.classList.remove("far");
      s.classList.add("fas");
    } else {
      s.classList.remove("fas");
      s.classList.add("far");
    }
  });
}
