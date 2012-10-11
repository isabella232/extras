jQuery( function ( $ ) {
    // Create the main dialog
    $( '#panels-dialog' ).show().dialog( {
        autoOpen: false,
        resizable:false,
        draggable:false,
        modal:    true,
        title:    $( '#panels-dialog' ).attr( 'data-title' ),
        minWidth: 960,
        maxHeight:720,
        close:    function () {
            $( '#panels-container .panel.new-panel' ).hide().fadeIn( 'slow' ).removeClass( 'new-panel' );
        }
    } ).find( '.panel-type' ).disableSelection();

    // The button for adding a panel
    $( '#panels .panels-add' )
        .button( {
            icons:{primary:'ui-icon-add'},
            text: false
        } )
        .click( function () {
            $( '#panels-dialog' ).dialog( 'open' );
            return false;
        } );

    // The button for adding a grid
    $( '#panels .grid-add' )
        .button( {
            icons:{primary:'ui-icon-columns'},
            text: false
        } )
        .click( function () {
            $( '#grid-add-dialog' ).dialog( 'open' );
            return false;
        } );

    var newPanelId = 0;

    /**
     * Create a new panel
     *
     * @param type
     * @return {*}
     */
    var createPanel = function ( type, data ) {
        var $$;
        if ( typeof type == 'string' ) $$ = $( '#panels-dialog .panel-type[data-class="' + type + '"]' );
        else $$ = type;

        var panel = $( '<div class="panel new-panel"><div class="panel-wrapper"><h4></h4><small class="description"></small><div class="form"></div></div></div>' );
        var dialog;

        var formHtml = $$.attr( 'data-form' );
        formHtml = formHtml.replace( /\{\$id\}/g, newPanelId++ );

        panel
            .data( {
                // We need this data to update the title
                'title-field':$$.attr( 'data-title-field' ),
                'title':      $$.attr( 'data-title' )
            } )
            .find( 'h4' ).click( function () {
                dialog.dialog( 'open' );
            } )
            .end().find( '.description' ).html( $$.find( '.description' ).html() )
            .end().find( '.form' ).html( formHtml );

        // Create the dialog buttons
        var dialogButtons = {};
        // The delete button
        dialogButtons[panelsLoc.buttons['delete']] = function () {
            if ( confirm( panelsLoc.messages['confirmDeletePanel'] ) ) {
                panel.fadeOut( function () {
                    $( this ).remove();
                    $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
                } );
                dialog.dialog( 'close' );
            }
        };
        // The done button
        dialogButtons[panelsLoc.buttons['done']] = function () {
            $( this ).trigger( 'panelsdone' );

            // Transfer the dialog values across
            dialog.find( '*[name]' ).not( '[data-info-field]' ).each( function () {
                var f = panel.find( '.form *[name="' + $( this ).attr( 'name' ) + '"]' );

                if ( f.attr( 'type' ) == 'checkbox' ) {
                    console.log( 'bar' );
                    console.log( $( this ).is( ':checked' ) );
                    f.prop( "checked", $( this ).is( ':checked' ) );
                }
                else f.val( $( this ).val() );
            } );

            // Change the title of the panel
            setPanelTitle( panel );

            dialog.dialog( 'close' );
        }

        dialog = $( '<div id="panel-dialog" />' ).addClass( 'dialog-form' )
            .html( formHtml ).dialog( {
                autoOpen:false,
                modal:   true,
                title:   ('Edit %s Panel').replace( '%s', $$.attr( 'data-title' ) ),
                minWidth:700,
                open:    function () {
                    // Transfer the values of the form to the dialog
                    panel.find( '.form *[name]' ).not( '[data-info-field]' ).each( function () {
                        var f = dialog.find( '*[name="' + $( this ).attr( 'name' ) + '"]' );

                        if ( f.attr( 'type' ) == 'checkbox' ) {
                            console.log( 'foo' );
                            console.log( $( this ).is( ':checked' ) );
                            f.prop( "checked", $( this ).is( ':checked' ) )
                        }
                        else f.val( $( this ).val() );
                    } );

                    // This gives panel types a chance to influence the form
                    $( this ).trigger( 'panelsopen' );
                },
                buttons: dialogButtons
            } );

        dialog.find( 'label' ).each( function () {
            // Make labels work as expected
            var f = $( '#' + $( this ).attr( 'for' ) );
            $( this ).disableSelection();

            $( this ).click( function () {
                // Toggle the checked value
                if ( f.attr( 'type' ) == 'checkbox' ) f.prop( 'checked', !f.prop( 'checked' ) );
                else f.focus();
            } );
        } );
        panel.disableSelection();

        if ( data != undefined ) {
            // Populate the form values
            for ( c in data ) {
                if ( c != 'info' ) {
                    var pe = panel.find( '.form *[name$="[' + c + ']"]' );
                    var de = dialog.find( '*[name$="[' + c + ']"]' );

                    if ( pe.attr( 'type' ) == 'checkbox' ) {
                        pe.prop( 'checked', Boolean( data[c] ) );
                        de.prop( 'checked', Boolean( data[c] ) );
                    }
                    else {
                        pe.val( data[c] );
                        de.val( data[c] );
                    }
                }
            }
        }

        setPanelTitle( panel );

        // This is to refresh the dialog positions
        $( window ).resize();
        return panel;
    }

    /**
     * Set the title of the panel
     *
     * @param {*} panel
     */
    var setPanelTitle = function ( panel ) {
        var titleField = panel.data( 'title-field' );
        var titleValue;

        if ( titleField != undefined ) {
            titleValue = panel.find( '*[name$="[' + titleField + ']"]' ).val();
        }

        if ( titleValue == '' || titleValue == undefined ) {
            panel.find( 'h4' ).html( panel.data( 'title' ) );
        }
        else {
            panel.find( 'h4' ).html( panel.data( 'title' ) + ': ' + titleValue );
        }
    }

    // Handle adding a new panel
    $( '#panels-dialog .panel-type' ).click( function () {
        var panel = createPanel( $( this ) );
        $( '#panels-container .cell .panels-container' ).last().append( panel );
        $( '#panels-container .cell .panels-container' ).sortable( "refresh" ).trigger( 'refreshcells' );
        $.grid.resizeCells( $( '#panels-container .cell .panels-container' ).last().closest( '.grid-container' ) );

        // Close the add panel dialog
        $( '#panels-dialog' ).dialog( 'close' );
    } );

    if ( typeof panelsData != 'undefined' ) {
        // Create all the content
        for ( var gi in panelsData.grids ) {
            var cellWeights = [];

            // Get the cell weights
            for ( var ci in panelsData.grid_cells ) {
                if ( Number( panelsData.grid_cells[ci]['grid'] ) == gi ) {
                    cellWeights[cellWeights.length] = Number( panelsData.grid_cells[ci].weight );
                }
            }

            // Create the grids
            var grid = $.grid.createGrid( Number( panelsData.grids[gi]['cells'] ), cellWeights );
            $.grid.setupGrid( grid );

            // Add panels to the grid cells
            for ( var pi in panelsData.widgets ) {

                if ( Number( panelsData.widgets[pi]['info']['grid'] ) == gi ) {
                    var pd = panelsData.widgets[pi];
                    var panel = createPanel( pd['info']['class'], pd );
                    grid
                        .find( '.panels-container' ).eq( Number( panelsData.widgets[pi]['info']['cell'] ) )
                        .append( panel )
                }
            }
        }

        $( '#panels-container .panels-container' )
            .sortable( 'refresh' )
            .trigger( 'refreshcells' );

        // Remove the new-panel class from any of these created panels
        $( '#panels-container .panel' ).removeClass( 'new-panel' );
        // Make sure everything is sized properly
        $( '#panels-container .grid-container' ).each( function () {
            $.grid.resizeCells( $( this ) );
        } );
    }
    else {
        // Create an initial grid container
        $.grid.setupGrid( $.grid.createGrid( 1 ) );
    }

    $( window ).resize( function () {
        $( '.panels-admin-dialog' ).dialog( "option", "position", "center" );
    } );
} );