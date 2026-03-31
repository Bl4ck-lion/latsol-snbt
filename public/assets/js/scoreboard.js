const subtestFilter = document.getElementById('subtest-filter');
const modeFilter = document.getElementById('mode-filter');
const dateFilter = document.getElementById('date-filter');
const scoreboardTableBody = document.querySelector('#scoreboard-table tbody');

function fetchScoreboard() {
    const subtestId = subtestFilter.value;
    const mode = modeFilter.value;
    const date = dateFilter.value;

    fetch(`/api/scoreboard?subtest_id=${subtestId}&mode=${mode}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            scoreboardTableBody.innerHTML = '';
            if (data.top) {
                data.top.forEach((row, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${row.username}</td>
                        <td>${row.score}</td>
                        <td>${row.duration_seconds}</td>
                    `;
                    scoreboardTableBody.appendChild(tr);
                });
            }
        });
}

subtestFilter.addEventListener('change', fetchScoreboard);
modeFilter.addEventListener('change', fetchScoreboard);
dateFilter.addEventListener('change', fetchScoreboard);

// Fetch initial data
fetchScoreboard();

// Poll for updates every 5 seconds
setInterval(fetchScoreboard, 5000);