jQuery( function($){

    var currentSlide;

    var addButtonToLayered = function(){
        jQuery('.metaslider .left table .slide').has('textarea.wysiwyg').each(function(){
            var $$ = $(this);
            if( !$$.has('.prebuiltSlides').length ) {
                var button = $('<p class="prebuiltSlides">' + siteoriginMetaslider.prebuilt + '</p>');
                $$.find('.rawEdit').after(button);

                button.click( function(){
                    var $$ = $(this);
                    currentSlide = $$.closest('.slide');

                    $('#siteorigin-metaslider-prebuilt-layouts-overlay').fadeIn();
                    $('#siteorigin-metaslider-prebuilt-layouts').fadeIn();
                    return false;
                } );
            }
        });
    }
    addButtonToLayered();

    $('#siteorigin-metaslider-prebuilt-layouts-overlay').click(function(){
        $('#siteorigin-metaslider-prebuilt-layouts-overlay').hide();
        $('#siteorigin-metaslider-prebuilt-layouts').hide();
    });

    $('#siteorigin-metaslider-prebuilt-layouts .layouts .layout').click(function(){
        var $$ = $(this);
        var html = $$.data('html');

        if(confirm('Are you sure you want to replace your current slide content with this prebuilt layout?')){
            currentSlide.find('textarea.wysiwyg').val(html);
            $('#siteorigin-metaslider-prebuilt-layouts-overlay').fadeOut('fast');
            $('#siteorigin-metaslider-prebuilt-layouts').fadeOut('fast');
        }

        return false;
    });

    $('#siteorigin-metaslider-prebuilt-layouts .close').click(function(){
        $('#siteorigin-metaslider-prebuilt-layouts-overlay').fadeOut('fast');
        $('#siteorigin-metaslider-prebuilt-layouts').fadeOut('fast');
        return false;
    });

    $(document).ajaxSuccess(function() {
        addButtonToLayered();
    });

} );