document.querySelectorAll('.start-attempt').forEach(button => {
    button.addEventListener('click', () => {
        const subtestId = button.dataset.subtestid;
        const mode = button.dataset.mode;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/attempt/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `subtest_id=${subtestId}&mode=${mode}&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                // Store attempt data in session storage to pass to the next page
                sessionStorage.setItem('attemptData', JSON.stringify(data));
                window.location.href = `/attempt`;
            }
        });
    });
});