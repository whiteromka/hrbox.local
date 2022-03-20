$(document).ready(function() {

    let intervalTimer;
    let timer = 0;

    runTimer();
    pasteOddRows();

    function runTimer() {
        intervalTimer = setInterval( () => {
            updateTimer();
        }, 1000);
    }

    function updateTimer() {
        timer++;
        $('#js-timer').text(timer);
    }

    function pasteOddRows() {
        $.get('/site/odd-rows').done(function(data) {
            if (data.success) {
                $('#js-ajax-content').html(data.html);
                $('#js-loader-message').text('Done!');
            } else {
                alert(data.error);
                $('#js-loader-message').html('<p class="text-danger">Прочтите /web/readme.txt</p>');
            }
            clearInterval(intervalTimer);
        });
    }

});