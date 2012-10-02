jQuery(function($){
    $('[data-tooltip]')
        .live('mouseenter', function(){
            var $$ = $(this);
            var tooltip = $('<div class="panels-tooltip"></div>').appendTo('body').html($$.attr('data-tooltip')).append($('<div class="pointer"></div>'));
            
            tooltip.css({
                top : $$.offset().top -15 - $$.outerHeight(),
                left : $$.offset().left - tooltip.outerWidth()/2 + $$.outerWidth()/2
            });
            
            $$.data('tooltip', tooltip);
        })
        .live('mouseleave', function(){
            var $$ = $(this);
            var tooltip = $$.data('tooltip');
            $$.data('tooltip', undefined);
            tooltip.remove();
        })
});