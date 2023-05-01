jQuery(document).ready(function($) {
    $('body').append($('#popup-news'));
    setTimeout(function() {
        $('#popup-news').fadeIn('slow');
    }, 3000); // Ajusta el tiempo de aparición del pop-up (3000ms = 3 segundos)

    $('#popup-news').on('click', function() {
        $(this).fadeOut('slow');
    });
});