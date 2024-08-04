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
                boardUI(response);
            }
        });

        const cells = document.querySelectorAll('.cell');
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
                boardUI(response);
                if (response.winning_state) {
                    setTimeout(function() {
                        alert(response.winning_state === 'DRAW' ? 'It is a draw!' : response.winning_state + ' wins!');
                    }, 10);
                } else {
                    if (response.player === 'O') {
                        computerMove();
                    }
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
                boardUI(response);
                if (response.winning_state) {
                    setTimeout(function() {
                        alert(response.winning_state === 'DRAW' ? 'It is a draw!' : response.winning_state + ' wins!');
                    }, 10);
                }
            }
        });
    }

    function boardUI(data) {
        const cells = document.querySelectorAll('.cell');

        data.ttt_board.forEach((value, index) => {
            cells[index].innerText = value ? value : '';
        });
    }

    function fetchLeaderboard() {
        $.ajax({
            url: 'game.php',
            method: 'POST',
            data: { post_value: 'leaderboard' },
            success: function(response) {
                let leaderboardHTML = '<h3>Last 10 wins</h3><table><tr><th>Player</th><th>Score</th></tr>';
                response.forEach(entry => {
                    leaderboardHTML += `<tr><td>${entry.player_name}</td><td>${entry.score}</td></tr>`;
                });
                leaderboardHTML += '</table>';
                $('#leaderboard').html(leaderboardHTML);
            }
        });
    }

});