document.addEventListener("DOMContentLoaded", function () {
    const chatForm = document.getElementById("chat-form");
    const messagesContainer = document.getElementById("messages-container");

    if (!chatForm || !messagesContainer) return; // Si on n'est pas sur la page discussion_map.php, on ne fait rien

    const mapId = document.getElementById("map_id").value;

    function loadMessages() {
        fetch(`load_messages.php?map_id=${mapId}`)
            .then(response => response.json())
            .then(data => {
                messagesContainer.innerHTML = data.map(msg => `
                    <div class="message">
                        <p><strong>${msg.username}</strong> <span class="msg-time">[${msg.created_at}]</span></p>
                        <p>${msg.message}</p>
                    </div>
                `).join("");
            })
            .catch(error => console.error("Erreur de chargement des messages:", error));
    }

    chatForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const message = document.getElementById("message").value.trim();

        if (message === "") return;

        fetch("post_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ map_id: mapId, message: message }),
        })
        .then(() => {
            loadMessages();
            document.getElementById("message").value = "";
        })
        .catch(error => console.error("Erreur d'envoi du message:", error));
    });

    // Charger les messages au chargement de la page et actualiser toutes les 5 secondes
    loadMessages();
    setInterval(loadMessages, 5000);
});
