import '../styles/admin.scss';

$(document).ready(function() {

    // Permet de rendre cliquable une ligne de Tableau et de rediriger vers une route d'un controller Symfony
    $('#main-admin tr[data-href]').on("click", function() {
        document.location = $(this).data('href');
    });

});