jQuery(function($){
    $( '#grid-prebuilt-dialog' ).show().dialog( {
        dialogClass: 'panels-admin-dialog',
        autoOpen:    false,
        resizable:   false,
        draggable:   false,
        modal:       true,
        title:       $( '#grid-prebuilt-dialog' ).attr( 'data-title' ),
        minWidth:    600,
        create:      function(event, ui){
            $(this ).closest('.ui-dialog' ).find('.ui-dialog-buttonset button' ).eq(0 ).addClass('button-delete');
        },
        buttons : [
            {
                text : panelsLoc.buttons.cancel,
                click: function(){
                    $( '#grid-prebuilt-dialog' ).dialog('close');
                }
            },
            {
                text: panelsLoc.buttons.insert,
                click: function(){
                    var $$ = $('#grid-prebuilt-input' );
                    if($$.val() == '') return;
                    
                    if(confirm(panelsLoc.messages.confirmLayout)){
                        var s = $$.find(':selected');

                        // First clear the grids
                        window.panels.clearGrids();

                        // Then load the prebuilt layout
                        window.panels.loadPanels(panelsPrebuiltLayouts[s.attr('data-layout-id')]);
                    }
                    $( '#grid-prebuilt-dialog' ).dialog('close');
                }
            }
        ]
        
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
});