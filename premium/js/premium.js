/**
 * (c) Greg Priday, freely distributable under the terms of the GPL 2.0 license.
 */

jQuery( function ( $ ) {

    var minPrice = Number( $('#theme-upgrade input[name=amount]').attr('min') );

    // Handle clicking the play button
    $('#theme-upgrade #click-to-play').click(function(){
        // Only load the video from Vimeo when the user clicks play
        $(this).replaceWith('<iframe src="http://player.vimeo.com/video/' + $(this).data('video-id') + '?title=0&byline=0&portrait=0&autoplay=1" width="640" height="362" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');
        return false;
    })

    $('#theme-upgrade #custom-price-form').submit(function(){
        $( '#theme-upgrade-info' ).slideDown();

        // Make sure the amount is OK
        $input = $(this).find('input[name=amount]');
        var val = $input.val().replace(/[^0-9.]/g, '');
        val = parseFloat(val).toFixed(2);
        if(isNaN(val)) val = minPrice;
        $input.val(val);

        window.open('', 'paymentwindow', 'width=960,height=800,resizeable,scrollbars');
        this.target = 'paymentwindow';
    });

    $('#theme-upgrade #custom-price-form input[name=amount]').keyup(function(){
        var $$ = $(this);
        $($('#purchase-form a').removeClass('selected').get().reverse()).each(function(){
            var $l = $(this);
            if(parseFloat($$.val()) >= parseFloat($l.data('amount')) ) {
                $l.addClass('selected');
                return false;
            }
        });
    }).change( function(){ $(this).keyup() } );

    $('#purchase-form a').click(function(){
        window.open($(this).attr('href'), 'paymentwindow', 'width=960,height=800,resizeable,scrollbars');
        $( '#theme-upgrade-info' ).slideToggle();
        return false;
    })


    // Display the form
    $( '#theme-upgrade-already-paid' ).click( function () {
        $( '#theme-upgrade-info' ).slideToggle();
        return false;
    } );

} );