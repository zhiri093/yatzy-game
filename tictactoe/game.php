<?php
session_start();

header('Content-Type: application/json');

$dsn = 'pgsql:host=localhost;port=5432;dbname=tictactoe;';
$username = 'postgres';  // Replace with your PostgreSQL username
$password = '12345';  // Replace with your PostgreSQL password

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if (!isset($_SESSION['game_board'])) {
    $_SESSION['game_board'] = []; // Initialization of leaderboard
}

$post_value = $_POST['post_value'] ?? '';

$response = match ($post_value) {
    '' => ['error' => 'Invalid action'],
    'start' => initializeGame(),
    'play' => makeMove(intval($_POST['x'] ?? -1)),
    'computer_play' => computerMove(),
    'leaderboard' => getLeaderboard(),
    'save_user' => saveUser($pdo, $_POST['name'], $_POST['username'], $_POST['location']),
    default => getGameboard()
};

echo json_encode($response);

function initializeGame() {
    $_SESSION['game_state'] = [
        'ttt_board' => array_fill(0, 9, null),
        'player' => 'X',
        'winning_state' => null
    ];
    return $_SESSION['game_state'];
}

function getGameboard() {
    return $_SESSION['game_state'];
}

function boardUpdateAfterWin($winning_state) {
    if ($winning_state !== 'DRAW') {
        if (count($_SESSION['game_board']) < 10) {
            $_SESSION['game_board'][] = $winning_state;
        } else {
            array_shift($_SESSION['game_board']);
            $_SESSION['game_board'][] = $winning_state;
        }
    }
}

function gameOver($ttt_board) {
    $all_winning_combination = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];

    foreach ($all_winning_combination as $tmp) {
        [$sqr1, $sqr2, $sqr3] = $tmp;
        if ($ttt_board[$sqr1] !== null && $ttt_board[$sqr1] === $ttt_board[$sqr2] && $ttt_board[$sqr2] === $ttt_board[$sqr3]) {
            return true;
        }
    }
    return false;
}

function makeMove($index_of_square) {
    if ($_SESSION['game_state']['winning_state'] !== null || $_SESSION['game_state']['ttt_board'][$index_of_square] !== null) {
        return $_SESSION['game_state'];
    }

    $_SESSION['game_state']['ttt_board'][$index_of_square] = $_SESSION['game_state']['player'];

    if (gameOver($_SESSION['game
