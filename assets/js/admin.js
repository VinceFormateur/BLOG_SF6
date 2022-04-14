import '../styles/admin.scss';

$(document).ready(function() {

    // Modifie la cellule de tableau Sortable
    $('#main-admin thead tr th a span').attr("class", "float-none pe-2 text-title-h2");

    // Permet de rendre cliquable une ligne de Tableau et de rediriger vers une route d'un controller Symfony
    $('#main-admin tr[data-href]').on("click", function() {
        document.location = $(this).data('href');
    });

});