document.addEventListener('DOMContentLoaded', () => {
    const quizContainer = document.getElementById('quiz-container');
    const attemptData = JSON.parse(sessionStorage.getItem('attemptData'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    if (!attemptData) {
        quizContainer.innerHTML = '<h1>Error: Could not load quiz data.</h1><p>Please start a new attempt from the dashboard.</p>';
        return;
    }

    let currentQuestionIndex = 0;

    function renderQuestion() {
        const question = attemptData.questions[currentQuestionIndex];
        let choicesHtml = '';
        for (const [label, content] of Object.entries(question.choices)) {
            choicesHtml += `
                <label class="choice">
                    <input type="radio" name="question-${question.id}" value="${label}">
                    <span>${label}. ${content}</span>
                </label>
            `;
        }

        quizContainer.innerHTML = `
            <div class="question">
                <h3>Question ${question.number} of ${attemptData.questions.length}</h3>
                <p class="stem">${question.stem}</p>
                <div class="choices">${choicesHtml}</div>
                <button id="next-question" class="button">Save & Next</button>
            </div>
        `;

        document.getElementById('next-question').addEventListener('click', handleNextQuestion);
    }

    function handleNextQuestion() {
        const selectedChoice = quizContainer.querySelector(`input[name="question-${attemptData.questions[currentQuestionIndex].id}"]:checked`);
        if (!selectedChoice) {
            alert('Please select an answer.');
            return;
        }

        // Save the answer
        fetch('/attempt/answer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `attempt_id=${attemptData.attempt_id}&question_id=${attemptData.questions[currentQuestionIndex].id}&choice=${selectedChoice.value}&csrf_token=${csrfToken}`
        });

        currentQuestionIndex++;

        if (currentQuestionIndex < attemptData.questions.length) {
            renderQuestion();
        } else {
            showSubmitScreen();
        }
    }

    function showSubmitScreen() {
        quizContainer.innerHTML = `
            <div class="submission-screen">
                <h2>You have answered all questions.</h2>
                <p>Click the button below to submit your attempt and see your score.</p>
                <button id="submit-attempt" class="button">Submit Attempt</button>
            </div>
        `;
        document.getElementById('submit-attempt').addEventListener('click', handleSubmit);
    }

    function handleSubmit() {
        fetch('/attempt/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-w' },
            body: `attempt_id=${attemptData.attempt_id}&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.score !== undefined) {
                sessionStorage.removeItem('attemptData');
                // Redirect to the new result page
                window.location.href = `/attempt/${attemptData.attempt_id}/result`;
            } else {
                alert('Error submitting your attempt. Please try again.');
            }
        });
    }

    renderQuestion();
});