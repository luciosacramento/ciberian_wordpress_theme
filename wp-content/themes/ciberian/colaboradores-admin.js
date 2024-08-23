jQuery(document).ready(function($) {
    $('#add-rede-social').on('click', function() {
        $('#redes-sociais-wrapper').append(
            '<div class="rede-social">' +
            '<label>Nome:</label><input type="text" name="colaboradores_redes_sociais_nome[]" value="" />' +
            '<label>URL:</label><input type="url" name="colaboradores_redes_sociais_url[]" value="" />' +
            '<button type="button" class="remove-social">Remover</button>' +
            '</div>'
        );
    });

    $('body').on('click', '.remove-social', function() {
        $(this).parent('.rede-social').remove();
    });
});