jQuery(function($){
    $( '#grid-prebuilt-dialog' ).show().dialog( {
        dialogClass: 'panels-admin-dialog',
        autoOpen:    false,
        resizable:   false,
        draggable:   false,
        modal:       true,
        title:       $( '#grid-prebuilt-dialog' ).attr( 'data-title' ),
        minWidth:    600
    } );

    // Button for adding prebuilt layouts
    $( '#panels .prebuilt-set' )
        .button( {
            icons: {primary: 'ui-icon-prebuilt'},
            text:  false
        } )
        .click( function () {
            $( '#grid-prebuilt-dialog' ).dialog( 'open' );
            return false;
        } );
    
    $('#grid-prebuilt-input' ).change(function(){
        var $$ = $(this);
        if($$.val() == '') return;
        
        console.log(panelsLoc);
        
        if(confirm(panelsLoc.messages.confirmLayout)){
            var s = $$.find(':selected');
            
            // First clear the grids
            window.panels.clearGrids();
            
            // Then load the prebuilt layout
            window.panels.loadPanels(panelsPrebuiltLayouts[s.attr('data-layout-id')]);
            
            // Close the dialog
            $( '#grid-prebuilt-dialog' ).dialog('close');
        }
    });
    
});