jQuery( function ( $ ) {
    var paymentWindow;
    $( '#theme-upgrade .buy-button' ).not('.variable-pricing, .variable-pricing-submit').click( function () {
        var $$ = $( this );

        paymentWindow = window.open( $$.attr( 'href' ), 'payment', 'height=800,width=1024' );
        $( '#theme-upgrade-info' ).slideDown();
        $( 'html, body' ).animate( {'scrollTop':0} );

        return false;
    } );

    $( '#theme-upgrade .buy-button.variable-pricing').click( function(){
        $('#theme-upgrade #variable-pricing-form').addClass('highlight-form');
        setTimeout(function(){
            $('#theme-upgrade #variable-pricing-form').removeClass('highlight-form');
        }, 2000);
    } );

    $( '#theme-upgrade .buy-button.variable-pricing-submit').click(function(e){
        e.preventDefault();
        $(this).closest('form').submit();
        $( '#theme-upgrade-info' ).slideDown();
        $( 'html, body' ).animate( {'scrollTop':0} );
        return false;
    })

    $('#theme-upgrade #variable-pricing-form .options input[type=radio]').change(function(){
        var val = $(this).val();
        if($(this).hasClass('custom-price')) {
            val = $('#theme-upgrade #variable-pricing-form .options input[name=variable_pricing_custom]').val();
            val = parseFloat(val).toFixed(2);
            if(isNaN(val)) val = 5;
            val = Math.max(val,5);
        }

        $('#theme-upgrade #variable-pricing-form input[name=amount]').val(val);
        $('#theme-upgrade #variable-pricing-form .variable-pricing-submit em').html('$'+val);
    });

    $('#theme-upgrade #variable-pricing-form .options input[name=variable_pricing_custom]').keyup(function(){
        var val = $(this).val().replace(/[^0-9.]/g, '');
        val = parseFloat(val).toFixed(2);
        if(isNaN(val)) val = 5;
        val = Math.max(val,5);

        $(this).closest('form').find('.custom-price').click();

        $('#theme-upgrade #variable-pricing-form input[name=amount]').val(val);
        $('#theme-upgrade #variable-pricing-form .variable-pricing-submit em').html('$'+val);
    });



    $('#support-choice-overlay' ).click(function(){
        $('#support-choice, #support-choice-overlay' ).fadeOut();
    });

    // Toggle the key entry data
    $( '#theme-upgrade-already-paid' ).click( function () {
        $( '#theme-upgrade-info' ).slideToggle();
        return false;
    } );
    
    $('#theme-upgrade .feature-image' ).click(function(){
        $('html, body').animate({scrollTop:0}, 'slow');
    })
    
    // Set up the testimonial cycle
    $('#theme-upgrade .testimonials' ).cycle({
        fx: 'fade',
        random: true,
        timeout : 5000
    });
} );