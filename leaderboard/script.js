document.addEventListener("DOMContentLoaded", function () {
    const creatorFilter = document.getElementById("creator");
    const playerFilter = document.getElementById("player");
    const leaderboardContainer = document.getElementById("leaderboard-container");

    function fetchMaps() {
        const creator = creatorFilter.value;
        const player = playerFilter.value;

        fetch(`load_maps.php?creator=${creator}&player=${player}`)
            .then(response => response.text())
            .then(data => {
                leaderboardContainer.innerHTML = data;
            });
    }

    function fetchFilters() {
        fetch("get_filters.php")
            .then(response => response.json())
            .then(data => {
                data.creators.forEach(creator => {
                    const option = document.createElement("option");
                    option.value = creator.user_id;
                    option.textContent = creator.username;
                    creatorFilter.appendChild(option);
                });

                data.players.forEach(player => {
                    const option = document.createElement("option");
                    option.value = player.user_id;
                    option.textContent = player.username;
                    playerFilter.appendChild(option);
                });
            });
    }

    creatorFilter.addEventListener("change", fetchMaps);
    playerFilter.addEventListener("change", fetchMaps);

    fetchFilters();
    fetchMaps();
});
