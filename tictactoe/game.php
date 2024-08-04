<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'tictactoe_db';
$username = 'tictactoe';
$password = '12345';
$dsn = "pgsql:host=$host;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['game_board'])) {
    $_SESSION['game_board'] = []; // Initialization of leaderboard
}

if (isset($_POST['post_value'])) {
    $post_value = $_POST['post_value'];
} else {
    $post_value = '';
}

if ($post_value === '') {
    $response = ['error' => 'Invalid action'];
} elseif ($post_value === 'start') {
    $response = initializeGame();
} elseif ($post_value === 'play') {
    if (isset($_POST['x'])) {
        $x = intval($_POST['x']);
    } else {
        $x = -1;
    }
    $response = makeMove($x);
} elseif ($post_value === 'computer_play') {
    $response = computerMove();
} elseif ($post_value === 'leaderboard') {
    $response = getLeaderboard();
} elseif ($post_value === 'save_user') {
    if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['location'])) {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $location = $_POST['location'];
        $response = saveUser($name, $username, $location);
    } else {
        $response = ['error' => 'Missing user information'];
    }
} else {
    $response = getGameboard();
}

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

    if (gameOver($_SESSION['game_state']['ttt_board'])) {
        $_SESSION['game_state']['winning_state'] = $_SESSION['game_state']['player'];
        boardUpdateAfterWin($_SESSION['game_state']['winning_state']);
    } elseif (!in_array(null, $_SESSION['game_state']['ttt_board'])) {
        $_SESSION['game_state']['winning_state'] = 'DRAW';
    } else {
        if ($_SESSION['game_state']['player'] === 'X') {
            $_SESSION['game_state']['player'] = 'O';
        } else {
            $_SESSION['game_state']['player'] = 'X';
        }
    }
    return $_SESSION['game_state'];
}

function computerMove() {
    $available_moves = [];
    foreach ($_SESSION['game_state']['ttt_board'] as $index => $value) {
        if ($value === null) {
            $available_moves[] = $index;
        }
    }

    if (count($available_moves) > 0) {
        $random_move = $available_moves[array_rand($available_moves)];
        $_SESSION['game_state']['ttt_board'][$random_move] = 'O';

        if (gameOver($_SESSION['game_state']['ttt_board'])) {
            $_SESSION['game_state']['winning_state'] = 'O';
            boardUpdateAfterWin($_SESSION['game_state']['winning_state']);
        } elseif (!in_array(null, $_SESSION['game_state']['ttt_board'])) {
            $_SESSION['game_state']['winning_state'] = 'DRAW';
        } else {
            $_SESSION['game_state']['player'] = 'X';
        }
    }

    return $_SESSION['game_state'];
}

function getLeaderboard() {
    $leaderboard = array_count_values(array_filter($_SESSION['game_board'])); // Count occurrences
    arsort($leaderboard); // Sort by count in descending order
    $top_leaderboard = array_slice($leaderboard, 0, 10, true); // Get top 10 

    $formatted_leaderboard = [];
    foreach ($top_leaderboard as $player => $score) {
        $formatted_leaderboard[] = ['player_name' => $player, 'score' => $score];
    }

    return $formatted_leaderboard;
}

function saveUser($name, $username, $location) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (name, username, location) VALUES (:name, :username, :location)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':location', $location);
    if ($stmt->execute()) {
        return ['success' => 'User added successfully'];
    } else {
        return ['error' => 'Failed to add user'];
    }
}
