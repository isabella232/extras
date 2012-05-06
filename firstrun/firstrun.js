jQuery(function($){
    var startPadding = Number($('body').css('padding-top').replace('px', ''));

    $('#origin-firstrun').show();
    $('body').css('padding-top', startPadding + $('#origin-firstrun').outerHeight());

    var interval = setInterval(function(){
        $('body').css('padding-top', startPadding + $('#origin-firstrun').outerHeight());
    }, 250);

    $('#origin-firstrun .dismiss').click(function(){
        clearInterval(interval);
        $('#origin-firstrun').remove();
        $('body').css('padding-top', startPadding );
        return false;
    });
});