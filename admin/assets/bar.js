jQuery(function($){
    $('#siteorigin-admin-bar').show();
    
    var position = function(){
        $('#siteorigin-admin-bar').css('top',0);
        $('#wpcontent, #adminmenu').css('padding-top', $('#siteorigin-admin-bar').outerHeight());
    }
    position();
    
    var interval = setInterval(position, 250);
    
    $('#siteorigin-admin-bar .dismiss').click(function(){
        clearInterval(interval);
        $('#siteorigin-admin-bar').slideUp('fast');
        $('#wpcontent, #adminmenu').animate({'padding-top': 0}, 'fast');
        
        // Send the message to the server to dismiss this bar
        $.post(
            ajaxurl + '?action=so_admin_dismiss_bar',
            { bar : $('#siteorigin-admin-bar').attr('data-type') }
        );
        
        return false;
    });
});