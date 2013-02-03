window.panels = {};

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
            $( '#panels-container .panel.new-panel' ).hide().fadeIn( 1000 ).removeClass( 'new-panel' );
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
    window.panels.undoManager = new UndoManager();

    /**
     * A jQuery function to get panels data
     */
    $.fn.getPanelData = function(){
        var $$ = $(this);
        var data = {};
        
        $$.find( '.form *[name]' ).not( '[data-info-field]' ).each( function () {
            
            var name = /widgets\[[0-9]+\]\[([a-z0-9_]+)\]/.exec($(this).attr('name'));
            name = name[1];
            if ( $$.attr( 'type' ) == 'checkbox' ) data[name] = $( this ).is( ':checked' )
            else data[name] = $( this ).val();
        } );
        
        return data;
    }


    /**
     * Create and return a new panel
     *
     * @param type
     * @param data
     *
     * @return {*}
     */
    $.fn.panelsCreatePanel = function ( type, data ) {
        var dialogWrapper = $(this );
        var $$ = dialogWrapper.find('.panel-type[data-class="' + type + '"]' );
        
        if($$.length == 0) return null;
        
        // Hide the undo message
        $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });

        var panel = $( '<div class="panel new-panel"><div class="panel-wrapper"><div class="title"><h4></h4><span class="actions"></span></div><small class="description"></small><div class="form"></div></div></div>' ).attr('data-type', type);
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
                return false;
            } )
            .end().find( '.description' ).html( $$.find( '.description' ).html() )
            .end().find( '.form' ).html( formHtml );
        
        // Create the dialog buttons
        var dialogButtons = {};
        // The delete button
        var deleteFunction = function () {
            // Add an entry to the undo manager
            window.panels.undoManager.register(
                this,
                function(type, data, container, position){
                    // Readd the panel
                    var panel = $('#panels-dialog').panelsCreatePanel(type, data, container);
                    window.panels.addPanel(panel, container, position, true);
                },
                [panel.attr('data-type'), panel.getPanelData(), panel.closest('.panels-container'), panel.index()],
                'Remove Panel'
            );
            
            // Create the undo notification
            $('#panels-undo-message' ).remove();
            $('<div id="panels-undo-message" class="updated"><p>' + panelsLoc.messages.deleteWidget + ' - <a href="#" class="undo">' + panelsLoc.buttons.undo + '</a></p></div>' )
                .appendTo('body')
                .hide()
                .slideDown()
                .find('a.undo')
                .click(function(){
                    window.panels.undoManager.undo();
                    $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });
                    return false;
                })
            ;

            panel.slideUp( function () {
                $( this ).remove();
                $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
            } );
            dialog.dialog( 'close' );
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
            panel.panelsSetPanelTitle();

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
                    $(this ).closest('.ui-dialog' ).find('.show-in-panels' ).show();
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
                    
                    // This fixes a weird a focus issue
                    $(this ).closest('.ui-dialog' ).find('a' ).blur();
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

        // Add the action buttons
        panel.find('.title .actions')
            .append(
                $('<a>edit<a>' ).addClass('edit' ).click(function(){
                    dialog.dialog('open');
                    return false;
                })
            )
            .append(
                $('<a>delete<a>' ).addClass('delete').click(function(){
                    deleteFunction();
                    return false;
                })
            );

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

        panel.panelsSetPanelTitle();

        // This is to refresh the dialog positions
        $( window ).resize();
        return panel;
    }

    window.panels.addPanel = function(panel, container, position, animate){
        if(container == null) container = $( '#panels-container .cell.cell-selected .panels-container' ).eq(0);
        if(container.length == 0) container = $( '#panels-container .cell .panels-container' ).eq(0);
        if(container.length == 0) return;
        
        if (position == null) container.append( panel );
        else {
            var current = container.find('.panel' ).eq(position);
            if(current.length == 0) container.append( panel );
            else {
                panel.insertBefore(current);
            }
        }
        
        container.sortable( "refresh" ).trigger( 'refreshcells' );
        container.closest( '.grid-container' ).panelsResizeCells();
        if(animate) $( '#panels-container .panel.new-panel' ).hide().fadeIn( 1000 ).removeClass( 'new-panel' );
    }

    /**
     * Set the title of the panel
     */
    $.fn.panelsSetPanelTitle = function ( ) {
        return $(this ).each(function(){
            var titleField = $(this ).data( 'title-field' );
            var titleValue;

            if ( titleField != undefined ) {
                titleValue = $(this ).find( '*[name$="[' + titleField + ']"]' ).val();
            }

            if ( titleValue == '' || titleValue == undefined ) {
                $(this ).find( 'h4' ).html( $(this ).data( 'title' ) );
            }
            else {
                $(this ).find( 'h4' ).html( $(this ).data( 'title' ) + ': ' + titleValue );
            }
        });
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
        var panel = $('#panels-dialog').panelsCreatePanel( $( this ).attr('data-class') );
        
        window.panels.addPanel(panel, null, null, true);
        
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
                    var panel = $('#panels-dialog').panelsCreatePanel( pd['info']['class'], pd );
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
            $( this ).panelsResizeCells();
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
            var $$ = $(this);
            
            $( '#wp-content-editor-container, #post-status-info' ).show();
            $( '#so-panels-panels' ).hide();
            $( '#wp-content-wrap' ).removeClass('panels-active');
            
            $('#content-resize-handle' ).show();
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
                    $( '#wp-content-wrap' ).addClass( 'panels-active' );
                    
                    // Triggers full refresh
                    $( window ).resize();
                    $('#content-resize-handle' ).hide();
                    
                    return false;
                } )
        );

    $( '#wp-content-editor-tools .wp-switch-editor' ).click(function(){
        // This fixes an occasional tab switching glitch
        var $$ = $(this);
        var p = $$.attr('id' ).split('-');
        $( '#wp-content-wrap' ).addClass(p[1] + '-active');
    });

    // This is for the home page panel
    $('#panels-home-page #post-body' ).show();
    $('#panels-home-page #post-body-wrapper' ).css('background', 'none');

    // Reposition the panels box
    $( '#so-panels-panels' )
        .insertAfter( '#wp-content-editor-container' )
        .addClass( 'wp-editor-container' )
        .hide()
        .find( '.handlediv' ).remove()
        .end()
        .find( '.hndle' ).html('' ).append(
            $('#add-to-panels')
        );

    // When the content panels button is clicked, trigger a window resize to set up the columns
    $('#content-panels' ).click(function(){
        $(window ).resize();
    });

    if ( typeof panelsData != 'undefined' || $('#panels-home-page' ).length) $( '#content-panels' ).click();
    // Click again after the panels have been set up
    setTimeout(function(){
        if ( typeof panelsData != 'undefined' || $('#panels-home-page' ).length) $( '#content-panels' ).click();
        $('#so-panels-panels .hndle' ).unbind('click');
        $('#so-panels-panels .cell' ).eq(0 ).click();
    }, 150);
    
    if($('#panels-home-page' ).length){
        $('#content-tmce, #content-html' ).remove();
        $('#content-panels' ).hide();
    }
} );