<?php
session_start();


if (!isset($_SESSION['game_state'])) {
    initializeGame();
}

if (!isset($_SESSION['game_board'])) {
    $_SESSION['game_board']=[];
}

function initializeGame(){
    $_SESSION['game_state']=[
        'ttt_board'=> array_fill(0, 9, null),
        'player' => 'X',
        'winning_state' => null
    ];
}

function getLeaderboard() {
    return array_count_values($_SESSION['game_board']);
}


function boardUpdateAfterMove($winning_state){
    if($winning_state!='DRAW'){
        
        if(count($_SESSION['$game_board'])<10){
            $_SESSION['game_board'][] =$winning_state;
        }
        else{
            array_shift($_SESSION['$game_board']);
            $_SESSION['game_board'][] =$winning_state;
        }
    }
}


function gameOver($ttt_board){
    $all_winning_combination = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]]; 
   
    foreach ($all_winning_combination as $tmp) {
        [$sqr1, $sqr2, $sqr3] = $tmp;


        if ($ttt_board[$sqr1] !== null && $ttt_board[$sqr1] === $ttt_board[$sqr2] && $ttt_board[$sqr2] === $ttt_board[$sqr3]) {
            return true;
        }
     }
    return false;

}


function makeMove($index) {}


?>