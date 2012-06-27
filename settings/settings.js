jQuery(function($){
    $('.colorpicker-wrapper').each(function(){
        var $$ = $(this);

        var picker = $.farbtastic($$.find('.farbtastic-container').hide());
        
        picker.linkTo(function(color){
            $$.find('input').val(color);
            $$.find('.color-indicator').css('background', color);
        });

        picker.setColor($$.find('input').val());

        $$.find('input')
            .focus(function(){ $$.find('.farbtastic-container').show() })
            .blur(function(){ $$.find('.farbtastic-container').hide() });
    });
});