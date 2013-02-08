/**
 * Handles pre-built Panel layouts.
 *
 * @copyright Greg Priday 2013
 * @license GPL 2.0 http://www.gnu.org/licenses/gpl-2.0.html
 */

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
                text : panels.i10n.buttons.cancel,
                click: function(){
                    $( '#grid-prebuilt-dialog' ).dialog('close');
                }
            },
            {
                text: panels.i10n.buttons.insert,
                click: function(){
                    var $$ = $('#grid-prebuilt-input' );
                    if($$.val() == '') {
                        
                    }
                    
                    if(confirm(panels.i10n.messages.confirmLayout)){
                        var s = $$.find(':selected');

                        // First clear the grids
                        panels.clearGrids();

                        // Then load the prebuilt layout
                        panels.loadPanels(panelsPrebuiltLayouts[s.attr('data-layout-id')]);
                    }
                    $( '#grid-prebuilt-dialog' ).dialog('close');
                }
            }
        ]
        
    } );

    // Button for adding prebuilt layouts
    $( '#add-to-panels .prebuilt-set' )
        .button( {
            icons: {primary: 'ui-icon-prebuilt'},
            text:  false
        } )
        .click( function () {
            $( '#grid-prebuilt-dialog' ).dialog( 'open' );
            return false;
        } );
});