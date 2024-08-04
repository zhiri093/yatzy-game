$(document).ready(function() {
    const cells = document.querySelectorAll('.cell');
    const resetButton = document.getElementById('reset');

    cells.forEach(cell => {
        cell.addEventListener('click', () => {
            const square = cell.getAttribute('data-index');
            makeMove(square);
        });
    });

    resetButton.addEventListener('click', function() {
        initializeGame();
        fetchLeaderboard();
    });

    $('#user-form').on('submit', function(event) {
        event.preventDefault();
        saveUser();
    });

    initializeGame();
    fetchLeaderboard();

    function initializeGame() {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: { post_value: 'start' },
            success: function(response) {
                updateBoardUI(response);
            }
        });

        cells.forEach(cell => {
            cell.innerText = '';
        });
    }

    function makeMove(value) {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: { post_value: 'play', x: value },
            success: function(response) {
                updateBoardUI(response);
                if (response.winning_state) {
                    setTimeout(function() {
                        alert(response.winning_state === 'DRAW' ? 'It is a draw!' : `${response.winning_state} wins!`);
                    }, 100);
                } else {
                    computerMove();
                }
            }
        });
    }

    function computerMove() {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: { post_value: 'computer_play' },
            success: function(response) {
                updateBoardUI(response);
                if (response.winning_state) {
                    setTimeout(function() {
                        alert(response.winning_state === 'DRAW' ? 'It is a draw!' : `${response.winning_state} wins!`);
                    }, 100);
                }
            }
        });
    }

    function updateBoardUI(gameState) {
        cells.forEach((cell, index) => {
            cell.innerText = gameState.ttt_board[index] || '';
        });
    }

    function fetchLeaderboard() {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: { post_value: 'leaderboard' },
            success: function(response) {
                const leaderboard = document.getElementById('leaderboard');
                leaderboard.innerHTML = '';
                response.forEach(entry => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${entry.player_name}: ${entry.score}`;
                    leaderboard.appendChild(listItem);
                });
            }
        });
    }

    function saveUser() {
        const name = $('#name').val();
        const username = $('#username').val();
        const location = $('#location').val();

        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: {
                post_value: 'save_user',
                name: name,
                username: username,
                location: location
            },
            success: function(response) {
                if (response.success) {
                    alert('User saved successfully!');
                } else {
                    alert('Error saving user: ' + response.error);
                }
            }
        });
    }
});
