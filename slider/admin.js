jQuery(function($){

    /**
     * Add a new slide
     * @param data The current slide data
     */
    var addSlide = function(data){
        var newSlide = $('<li />')
            .addClass('slide')
            .html($('#slider-builder-slides-skeleton').html())
            .appendTo('#slider-builder-slides');

        newSlide.find('.slide-content').hide();
        $('#slider-builder-slides').sortable( "refresh" );
        updateMedia(newSlide);
        
        
        if(data != undefined){
            // Add the data
            for (field in data) {
                newSlide.find('*[data-field="'+field+'"]').val(data[field]);
            }

            if(data.title != undefined && data.title != '') newSlide.find('.slide-title strong').html(data.title);
        }
    }

    /**
     * Update all seven media fields
     * @param par
     */
    var updateMedia = function(par){
        if(par == undefined) par = $('body');

        par.find('select.seven-media').each(function(){
            var $$ = $(this);
            var v = $$.val();
            
            $$.find('option[value!="-1"]').remove();
            for(var i in sevenSlider.images){
                $$.append($('<option>'+sevenSlider.images[i]+'</option>').attr('value', i));
            }
            
            $$.val(v);
        })
        
    }
    
    // Add all the initial slides
    if(sevenSlider.slides.length){
        for(var i = 0; i < sevenSlider.slides.length; i++){
            addSlide(sevenSlider.slides[i]);
        }
    }
    
    // Update the title when it changes
    $('#slider-builder-slides input.title-field').live('change', function(){
        var $$ = $(this);
        var title = $$.val();
        if(title == '') title = '&nbsp'
        $$.closest('.slide').find('.slide-title strong').html(title);
    });
    
    // Create the sortable
    $('#slider-builder-slides').sortable({
        placeholder: 'state-highlight',
        handle : '.slide-title'
    });
    
    // Click the widget action button
    $('#slider-builder .widget-action').live('click', function(){
        var $$ = $(this);
        var slide = $$.closest('.slide');
        slide.toggleClass('expanded');
        slide.find('.slide-content').slideToggle('fast');
    });
    
    // Add a new slide
    $('#slider-builder-add').click(function(){
        addSlide();
    });
    
    // When the media browser is closed, fetch all the images
    $("#content-add_media").one('click', function() {
        var old_tb_remove = window.tb_remove;
        window.tb_remove = function() {
            old_tb_remove(); // calls the tb_remove() of the Thickbox plugin
            
            // Get a refreshed list of media
            $.get(
                ajaxurl,
                { post_ID : $('#post_ID').val() , action: 'seven_slider_images'},
                function(data){
                    // Update the sevenSlider value
                    sevenSlider.images = data;
                    
                    // Update the media
                    updateMedia();
                }
            );
        };
    });
    
    $('#slider-builder a.delete').live('click', function(){
        var $$ = $(this);
        if(confirm($$.attr('data-confirm'))){
            $$.closest('#slider-builder-slides li').slideUp(function(){
                $(this).remove();
            });
        }
    });
    
    // Remove the skeleton before submitting the form
    $('form#post').submit(function(){
        $('#slider-builder-slides-skeleton').remove();
    });
});