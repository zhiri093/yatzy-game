$(document).ready(function() {
    const cells = document.querySelectorAll('.cell');
    const resetButton = document.getElementById('reset');

    cells.forEach(cell => {
        cell.addEventListener('click', () => {
            const square = cell.getAttribute('data-index');
            makeMove(square);
        });
    });

    resetButton.addEventListener('click', function() {initializeGame()

    });

    initializeGame();






//when called sends value 'start' to php sevrer as a POSt request to let it know game has started (can alos be used wfor when game is reset)
function initializeGame(){
    $.ajax({
        url: 'game.php',
        method: 'POST',
        data: { post_value: 'start' },
        success: function(response) { 
            boardUI(response);
             
        } 

    }); 

    //clears board

    const cells = document.querySelectorAll('.cell');
    cells.forEach(cell=>{
        cell.innerText='';
    });
    
}


//when called sends value 'play' t php server as POST reuqets
function makeMove(value){
    $.ajax({
        url:'game.php',
        method: 'POST', 
        data: { post_value: 'play', x: value },
        success: function(response) {
            boardUI(response);
            if (response.winning_state) {
                setTimeout(function() {
                    alert(response.winning_state === 'DRAW' ? 'It is a draw! ' : response.winning_state + ' wins!');
                }, 10);
            }
        }
    });
}


//This function updates the UI of the board
function boardUI(data){
    const cells = document.querySelectorAll('.cell');

    data.ttt_board.forEach((value, index) => {
            cells[index].innerText = value ? value : '';
        });
    }


});