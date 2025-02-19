document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('avatar');
    const canvas = document.getElementById('avatarPreview');
    const ctx = canvas.getContext('2d');

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) {
            canvas.style.display = 'none';
            return;
        }

        // Vérif basique du type MIME
        const allowedTypes = ['image/png', 'image/jpeg'];
        if (!allowedTypes.includes(file.type)) {
            alert("Seuls les fichiers JPG et PNG sont autorisés.");
            fileInput.value = "";
            canvas.style.display = 'none';
            return;
        }

        // Lire le fichier via FileReader
        const reader = new FileReader();
        reader.onload = (evt) => {
            const img = new Image();
            img.onload = () => {
                // Dessiner l'image dans le canvas (100×100)
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                canvas.style.display = 'block';
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
    });
});
