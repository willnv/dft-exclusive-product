jQuery(function($) {
    
    // aplicar o select2
    $("#campo-exclusivo").select2();
    
    
    // Esconder a categoria Personalizado
    $('#product_catchecklist li > label').each(function() {
        
        if ($(this).text() === ' Personalizado' ) {
            //$(this).hide();
        }
    });
    
});