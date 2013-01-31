jQuery(function($){
    var originalInsert = wp.media.editor.insert; 
    
    wp.media.editor.insert = function(h){
        // Check that panels tab is active
        if(!$('#wp-content-editor-tools #content-panels').hasClass('panels-tab-active')) return originalInsert(h);
        
        if(h.indexOf('[gallery') !== -1) {
            // Get the IDs of the gallery
            var he;
            
            he = h.replace('[gallery', '<gallery');
            he = he.replace(']', '/>');
            var $el = $(he);
            
            // Create a new gallery panel
            var panel = window.panels.createPanel('SiteOrigin_Widgets_Gallery', {
                'ids' : $el.attr('ids') 
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
});