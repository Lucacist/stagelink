document.addEventListener("DOMContentLoaded", function () {
  // Gestion du formulaire de notation standard
  const ratingForm = document.querySelector(".avis-form");
  if (ratingForm) {
    const stars = ratingForm.querySelectorAll(".stars-input input");
    const labels = ratingForm.querySelectorAll(".stars-input label");
    
    // Ajouter une classe active lorsqu'une étoile est sélectionnée
    stars.forEach((star, index) => {
      star.addEventListener("change", function() {
        // Mettre à jour l'apparence des étoiles
        labels.forEach((label, i) => {
          if (i <= index) {
            label.classList.add("active");
          } else {
            label.classList.remove("active");
          }
        });
      });
    });
    
    // Soumettre le formulaire normalement (pas d'AJAX)
    ratingForm.addEventListener("submit", function(e) {
      // Vérifier qu'une note a été sélectionnée
      const selectedRating = ratingForm.querySelector('input[name="note"]:checked');
      if (!selectedRating) {
        e.preventDefault();
        alert("Veuillez sélectionner une note avant de soumettre.");
        return false;
      }
      
      // Le formulaire sera soumis normalement
      return true;
    });
  }
  
  // Affichage des étoiles pour les notes existantes
  document.querySelectorAll(".rating-display").forEach((container) => {
    const rating = parseFloat(container.dataset.rating) || 0;
    const stars = container.querySelectorAll(".star");
    
    stars.forEach((star, index) => {
      if (index < rating) {
        star.classList.add("full");
      } else {
        star.classList.add("empty");
      }
    });
  });
});
