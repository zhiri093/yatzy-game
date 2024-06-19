const cells = document.querySelectorAll('.cell');
const resetButton = document.getElementById('reset');
const player = 'X';
const ai = 'O';
let board = Array(9).fill(null);

// Winning combinations
const winningCombinations = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8],
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8],
    [0, 4, 8],
    [2, 4, 6]
];

// Check for a win or a tie
function checkWinner(board) {
    for (let combination of winningCombinations) {
        const [a, b, c] = combination;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
            return board[a];
        }
    }
    return board.includes(null) ? null : 'Tie';
}

// AI makes a move
function aiMove() {
    let availableCells = board.map((cell, index) => cell === null ? index : null).filter(val => val !== null);
    let move = availableCells[Math.floor(Math.random() * availableCells.length)];
    board[move] = ai;
    cells[move].innerText = ai;
    cells[move].removeEventListener('click', handleCellClick);
    if (checkWinner(board)) {
        endGame();
    }
}

// Handle cell click
function handleCellClick(event) {
    const index = event.target.getAttribute('data-index');
    if (!board[index]) {
        board[index] = player;
        event.target.innerText = player;
        event.target.removeEventListener('click', handleCellClick);
        if (checkWinner(board)) {
            endGame();
        } else {
            aiMove();
        }
    }
}

// End the game
function endGame() {
    const winner = checkWinner(board);
    if (winner) {
        setTimeout(() => alert(winner === 'Tie' ? 'It\'s a tie!' : `${winner} wins!`), 10);
        cells.forEach(cell => cell.removeEventListener('click', handleCellClick));
    }
}

// Reset the game
function resetGame() {
    board.fill(null);
    cells.forEach(cell => {
        cell.innerText = '';
        cell.addEventListener('click', handleCellClick);
    });
}

resetButton.addEventListener('click', resetGame);

// Initialize game
resetGame();
