jQuery(function($){
    var startPadding = Number($('body').css('padding-top').replace('px', ''));
    
    $('#siteorigin-admin-bar').show();
    $('body').css('padding-top', startPadding + $('#siteorigin-admin-bar').outerHeight());
    
    var position = function(){
        $('#siteorigin-admin-bar').css('top', -$('#siteorigin-admin-bar').outerHeight()-1);
        $('body').css('padding-top', startPadding + $('#siteorigin-admin-bar').outerHeight()+1);
    }
    position();
    
    var interval = setInterval(position, 250);
        

    $('#siteorigin-admin-bar .dismiss').click(function(){
        clearInterval(interval);
        $('#siteorigin-admin-bar').slideUp('fast');
        $('body').animate({'padding-top': startPadding}, 'fast');
        
        // Send the message to the server to dismiss this bar
        $.post(
            ajaxurl + '?action=so_admin_dismiss_bar',
            { bar : $('#siteorigin-admin-bar').attr('data-type') }
        );
        
        return false;
    });
});