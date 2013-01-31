if(typeof window.panels == 'undefined') window.panels = {};

jQuery( function ( $ ) {
    // Create the main add widgets dialog
    $( '#panels-dialog' ).show().dialog( {
        dialogClass: 'panels-admin-dialog',
        autoOpen:    false,
        resizable:   false,
        draggable:   false,
        modal:       true,
        title:       $( '#panels-dialog' ).attr( 'data-title' ),
        minWidth:    960,
        close:       function () {
            $( '#panels-container .panel.new-panel' ).hide().fadeIn( 'slow' ).removeClass( 'new-panel' );
        }
    } ).find( '.panel-type' ).disableSelection();

    // The button for adding a panel
    $( '#panels .panels-add' )
        .button( {
            icons: {primary: 'ui-icon-add'},
            text:  false
        } )
        .click( function () {
            $('#panels-text-filter-input' ).val('' ).keyup();
            $( '#panels-dialog' ).dialog( 'open' );
            return false;
        } );

    var newPanelId = 0;

    /**
     * Create a new panel
     *
     * @param type
     * @param data
     *
     * @return {*}
     */
    window.panels.createPanel = function ( type, data ) {
        var $$;
        if ( typeof type == 'string' ) $$ = $( '#panels-dialog .panel-type[data-class="' + type + '"]' );
        else $$ = type;
        
        if($$.length == 0) return null;

        var panel = $( '<div class="panel new-panel"><div class="panel-wrapper"><h4></h4><small class="description"></small><div class="form"></div></div></div>' );
        var dialog;
        
        var formHtml = $$.attr( 'data-form' );
        formHtml = formHtml.replace( /\{\$id\}/g, newPanelId++ );

        panel
            .data( {
                // We need this data to update the title
                'title-field': $$.attr( 'data-title-field' ),
                'title':       $$.attr( 'data-title' )
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
            if ( confirm( panelsLoc.messages['confirmDeleteWidget'] ) ) {
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
                    f.prop( "checked", $( this ).is( ':checked' ) );
                }
                else f.val( $( this ).val() );
            } );

            // Change the title of the panel
            setPanelTitle( panel );

            dialog.dialog( 'close' );
        }

        dialog = $( '<div class="dialog-form"></div>' )
            .html( formHtml )
            .dialog( {
                dialogClass: 'panels-admin-dialog',
                autoOpen:    false,
                modal:       true,
                title:       ('Edit %s Panel').replace( '%s', $$.attr( 'data-title' ) ),
                minWidth:    700,
                create:      function(event, ui){
                    $(this ).closest('.ui-dialog' ).find('.ui-dialog-buttonset button' ).eq(0 ).addClass('button-delete');
                },
                open:        function () {
                    // Transfer the values of the form to the dialog
                    panel.find( '.form *[name]' ).not( '[data-info-field]' ).each( function () {
                        var f = dialog.find( '*[name="' + $( this ).attr( 'name' ) + '"]' );

                        if ( f.attr( 'type' ) == 'checkbox' ) {
                            f.prop( "checked", $( this ).is( ':checked' ) )
                        }
                        else f.val( $( this ).val() );
                    } );

                    // This gives panel types a chance to influence the form
                    $( this ).trigger( 'panelsopen' );
                },
                buttons:     dialogButtons
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

    window.panels.addPanel = function(panel){
        $( '#panels-container .cell .panels-container' ).last().append( panel );
        $( '#panels-container .cell .panels-container' ).sortable( "refresh" ).trigger( 'refreshcells' );
        window.panels.resizeCells( $( '#panels-container .cell .panels-container' ).last().closest( '.grid-container' ) );
        $( '#panels-container .panel.new-panel' ).hide().fadeIn( 'slow' ).removeClass( 'new-panel' );
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

    // Handle filtering in the panels dialog
    $( '#panels-text-filter-input' )
        .keyup( function () {
            var value = $( this ).val();
            // Filter the panels
            $( '#panels-dialog .panel-type-list .panel-type' )
                .show()
                .each( function () {
                    if ( value == '' ) return;

                    if ( $( this ).find( 'h3' ).html().toLowerCase().indexOf( value ) == -1 ) {
                        $( this ).hide();
                    }
                } )
        } )
        .click( function () {
            $( this ).keyup()
        } );

    // Handle adding a new panel
    $( '#panels-dialog .panel-type' ).click( function () {
        var panel = window.panels.createPanel( $( this ) );
        
        window.panels.addPanel(panel);
        
        // Close the add panel dialog
        $( '#panels-dialog' ).dialog( 'close' );
    } );


    /**
     * Loads panel data
     * 
     * @param data
     */
    window.panels.loadPanels = function(data){
        window.panels.clearGrids();
        
        // Create all the content
        for ( var gi in data.grids ) {
            var cellWeights = [];

            // Get the cell weights
            for ( var ci in data.grid_cells ) {
                if ( Number( data.grid_cells[ci]['grid'] ) == gi ) {
                    cellWeights[cellWeights.length] = Number( data.grid_cells[ci].weight );
                }
            }

            // Create the grids
            var grid = window.panels.createGrid( Number( data.grids[gi]['cells'] ), cellWeights );
            window.panels.setupGrid( grid );

            // Add panels to the grid cells
            for ( var pi in data.widgets ) {

                if ( Number( data.widgets[pi]['info']['grid'] ) == gi ) {
                    var pd = data.widgets[pi];
                    var panel = window.panels.createPanel( pd['info']['class'], pd );
                    grid
                        .find( '.panels-container' ).eq( Number( data.widgets[pi]['info']['cell'] ) )
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
            window.panels.resizeCells( $( this ) );
        } );
    }
    
    // Either setup an initial grid or load one from the panels data
    if ( typeof panelsData != 'undefined' ) window.panels.loadPanels(panelsData);
    else window.panels.setupGrid( window.panels.createGrid( 1 ) );
    
    $( window ).resize( function () {
        // When the window is resized, we want to center any panels-admin-dialog dialogs
        $( '.panels-admin-dialog' ).filter( ':data(dialog)' ).dialog( 'option', 'position', 'center' );
    } );
    
    // This is the part where we move the panels box into a tab of the content editor
    $( '#wp-content-editor-tools' )
        .find( '.wp-switch-editor' )
        .click(function () {
            $( '#wp-content-editor-container, #post-status-info' ).show();
            $( '#so-panels-panels' ).hide();
            $( '#content-panels' ).removeClass( 'panels-tab-active' );
            
            var self = this;
            
            // Double toggling resets the content editor to make sure panels isn't being displayed
            switchEditors.go('content', 'toggle');
            switchEditors.switchto(self);
            setTimeout(function(){
                // This is to reset the change.
                switchEditors.go('content', 'toggle');
                switchEditors.switchto(self);
            }, 100);
            return false;
        } ).end()
        .prepend(
            $( '<a id="content-panels" class="hide-if-no-js wp-switch-editor switch-panels">' + $( '#so-panels-panels h3.hndle span' ).html() + '</a>' )
                .click( function () {
                    var $$ = $( this );
                    // This is so the inactive tabs don't show as active
                    $( '#wp-content-wrap' ).removeClass( 'tmce-active html-active' );

                    // Hide all the standard content editor stuff
                    $( '#wp-content-editor-container, #post-status-info' ).hide();

                    $( '#so-panels-panels' ).show();
                    $( '#content-panels' ).addClass( 'panels-tab-active' );
                    
                    $( window ).resize();
                } )
        )

    if ( typeof panelsData != 'undefined' ) {
        setTimeout( function () {
            $( '#content-panels' ).click();
        }, 50 );
    }
    
    // Prevent minimizing the panels display
    setTimeout(function(){
        $('#so-panels-panels .hndle' ).unbind('click');
    }, 500);

    // Reposition the panels box
    $( '#so-panels-panels' )
        .insertAfter( '#wp-content-editor-container' )
        .addClass( 'wp-editor-container' )
        .hide()
        .find( '.handlediv' ).remove();
} );