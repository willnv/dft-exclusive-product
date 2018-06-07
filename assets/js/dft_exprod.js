jQuery(function($) {
    
    $("#campo-exclusivo").select2();
    
    $('#product_catchecklist li > label').each(function() {
        if ( $(this).text() === ' Personalizado' )
            $(this).addClass("checkbox-personalizado").hide();
    });

    $("#campo-exclusivo").change(autoCheckCategory);

    function autoCheckCategory() {
        if ( $(this).val() === "" )
            $(".checkbox-personalizado input").prop("checked", false);
        else 
            $(".checkbox-personalizado input").prop("checked", true);
    }
    
});