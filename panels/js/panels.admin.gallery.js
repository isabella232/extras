jQuery(function($){
    var originalInsert = wp.media.editor.insert; 
    
    wp.media.editor.insert = function(h){
        // Check that panels tab is active
        if($('#wp-content-editor-tools #content-panels').hasClass('panels-tab-active') && h.indexOf('[gallery') !== -1) {
            // Get the IDs of the gallery
            var he;
            
            he = h.replace('[gallery', '<gallery');
            he = he.replace(']', '/>');
            var el = $(he);
            
            // Create a new gallery panel
            var panel = window.panels.createPanel('SiteOrigin_Widgets_Gallery', {
                'ids' : el.attr('ids') 
            });
            
            // The panel couldn't be created. Possible the widgets gallery isn't being used.
            if(panel == null) {
                originalInsert(h);
            }
            else{
                $( '#panels-container .cell .panels-container' ).last().append( panel );
                $( '#panels-container .cell .panels-container' ).sortable( "refresh" ).trigger( 'refreshcells' );
                window.panels.resizeCells( $( '#panels-container .cell .panels-container' ).last().closest( '.grid-container' ) );
                $( '#panels-container .panel.new-panel' ).hide().fadeIn( 'slow' ).removeClass( 'new-panel' );
            }
        }
        else{
            originalInsert(h);
        }
    }
});