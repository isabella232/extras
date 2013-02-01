jQuery(function($){
    var originalInsert = wp.media.editor.insert;

    wp.media.editor.insert = function(h){
        // Check that panels tab is active
        if(!$('#wp-content-editor-tools #content-panels').hasClass('panels-tab-active')) return originalInsert(h);

        if(h.indexOf('[gallery') !== -1) {
            // Get the IDs of the gallery
            var attachments = wp.media.gallery.attachments( wp.shortcode.next( 'gallery', h ).shortcode );
            var ids = attachments.models.map(function(e){ return e.id });
            
            // Create a new gallery panel
            var panel = window.panels.createPanel('SiteOrigin_Widgets_Gallery', {
                'ids' : ids.join(',') 
            });
            
            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if(panel == null) originalInsert(h);
            else window.panels.addPanel(panel);
            
            return;
        }
        else if(h.indexOf('<a ') !== -1 || h.indexOf('<img ') !== -1) {
            // Figure out how we can add this to panels
            var $el = $(h);
            
            var panel;
            if($el.prop("tagName") == 'A' && $el.children().eq(0 ).prop('tagName') == 'IMG'){
                // This is an image with a link
                panel = window.panels.createPanel('SiteOrigin_Widgets_Image', {
                    'href' : $el.attr('href'),
                    'src' : $el.children().eq(0 ).attr('src')
                });
            }
            else if($el.prop("tagName") == 'IMG'){
                // This is just an image tag
                panel = window.panels.createPanel('SiteOrigin_Widgets_Image', {
                    'src' : $el.children().eq(0 ).attr('src')
                });
            }

            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if(panel == null) originalInsert(h);
            else window.panels.addPanel(panel);
            
            return;
        }
        
        originalInsert(h);
    }
    
    // When a user clicks on the gallery edit button, we'll try intercept
    $('a.button.media-button-insert' ).live('click', function(){
        // A slightly hackey way to make sure we just clicked on update gallery
        if($(this ).html() != _wpMediaViewsL10n.updateGallery) return;
        
        var activeDialog = $('.panels-admin-dialog:visible' );
        if(activeDialog.length == 0) return;
        
        var c = activeDialog.find('input[data-info-field="class"]');
        if( c.val() != 'SiteOrigin_Widgets_Gallery') return;
        
        // We've made it past all the tests, so we know the active dialog is a gallery
        
        var ids = wp.media.gallery.frame.options.selection.models.map(function(e){return e.id});
        activeDialog.find('input[name$="[ids]"]' ).val(ids.join(','));
    });

    // When the user clicks on the select button, we need to display the gallery editing
    $('.so-gallery-widget-select-attachments' ).live('click', function(){
        // Activate the media editor
        var val = $(this ).closest('.ui-dialog' ).find('*[name$="[ids]"]').val();
        wp.media.gallery.edit('[gallery ids="' + val  + '"]');
        return false;
    });
});