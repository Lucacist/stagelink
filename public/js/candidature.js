document.addEventListener('DOMContentLoaded', function() {
    // Code pour le formulaire de candidature
    const postulerBtn = document.getElementById('postuler-btn');
    const annulerBtn = document.getElementById('annuler-btn');
    const formContainer = document.getElementById('candidature-form-container');
    
    if (postulerBtn && formContainer) {
        // Afficher le formulaire quand on clique sur le bouton Postuler
        postulerBtn.addEventListener('click', function() {
            formContainer.classList.remove('hidden');
            postulerBtn.style.display = 'none';
        });
        
        // Masquer le formulaire quand on clique sur Annuler
        if (annulerBtn) {
            annulerBtn.addEventListener('click', function() {
                formContainer.classList.add('hidden');
                postulerBtn.style.display = 'inline-block';
                
                // Rechercher des alertes et les masquer aussi
                const alerts = formContainer.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.display = 'none';
                });
            });
        }
    }
    
    // Validation du formulaire
    const candidatureForm = document.getElementById('candidature-form');
    if (candidatureForm) {
        candidatureForm.addEventListener('submit', function(event) {
            const lettreMotivation = document.getElementById('lettre_motivation').value;
            const cvFile = document.getElementById('cv').files[0];
            
            if (!lettreMotivation.trim()) {
                event.preventDefault();
                alert('Veuillez rédiger une lettre de motivation');
                return;
            }
            
            if (!cvFile) {
                event.preventDefault();
                alert('Veuillez télécharger votre CV');
                return;
            }
            
            // Vérification de la taille du fichier (max 2 Mo)
            if (cvFile && cvFile.size > 2 * 1024 * 1024) {
                event.preventDefault();
                alert('Le fichier est trop volumineux. La taille maximale est de 2 Mo.');
                return;
            }
        });
    }
});
