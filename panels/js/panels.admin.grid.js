if(typeof window.panels == 'undefined') window.panels = {};

jQuery( function ( $ ) {
    // The button for adding a grid
    $( '#panels .grid-add' )
        .button( {
            icons: {primary: 'ui-icon-columns'},
            text:  false
        } )
        .click( function () {
            $( '#grid-add-dialog' ).dialog( 'open' );
            return false;
        } );
    
    /**
     * @param $$
     */
    window.panels.setupGrid = function ( $$ ) {
        // Hide the undo message
        $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });
        
        $$.panelsResizeCells();

        $$.find( '.grid .cell' ).not( '.first' ).each( function () {
            var sharedCellWidth, sharedCellLeft;

            $( this ).resizable( {
                handles:    'w',
                containment:'parent',
                start:      function ( event, ui ) {
                    sharedCellWidth = $( this ).prev().outerWidth();
                    sharedCellLeft = $( this ).prev().position().left;
                },
                stop:       function ( event, ui ) {
                    $$.find( '.grid .cell' ).not( '.first' ).resizable( 'disable' ).resizable( 'enable' );
                },
                resize:     function ( event, ui ) {
                    var c = $( this );
                    var p = $( this ).prev();

                    p.css( 'width', c.position().left - p.position().left - 12 );

                    var totalWidth = 0;
                    $$.find( '.grid .cell' )
                        .each( function () {
                            totalWidth += $( this ).width();
                        } )
                        .each( function () {
                            var percent = $( this ).width() / totalWidth;
                            $( this ).find( '.cell-width-value span' ).html( Math.round( percent * 1000 ) / 10 + '%' );
                            $( this ).attr( 'data-percent', percent ).find( 'input[name$="[weight]"]' ).val( percent );
                        } );

                    $$.panelsResizeCells(true);
                }
            } );
        } );

        // Enable double clicking on the resizer
        $$.find( '.grid .cell .ui-resizable-handle' ).dblclick( function () {
            var c1 = $( this ).closest( '.cell' );
            var c2 = c1.prev();
            var totalPercent = Number( c1.attr( 'data-percent' ) ) + Number( c2.attr( 'data-percent' ) );
            c1.attr( 'data-percent', totalPercent / 2 ).find( 'input[name$="[weight]"]' ).val( totalPercent / 2 );
            c2.attr( 'data-percent', totalPercent / 2 ).find( 'input[name$="[weight]"]' ).val( totalPercent / 2 );
            c1.add( c2 ).find( '.cell-width-value span' ).html( Math.round( totalPercent / 2 * 1000 ) / 10 + '%' );
            $$.panelsResizeCells();

            return false;
        } );

        $$.find( '.grid .cell' )
            .click(function(){
                $( '.grid .cell' ).removeClass('cell-selected');
                $(this ).addClass('cell-selected');
            })
            .each( function () {
                var percent = Number( $( this ).attr( 'data-percent' ) );
                $( this ).find( '.cell-width-value span' ).html( Math.round( percent * 1000 ) / 10 + '%' );
            } )
            .find( '.panels-container' )
            // This sortable handles the widgets inside the cell
            .sortable( {
                placeholder:"ui-state-highlight",
                connectWith:".panels-container",
                tolerance:  'pointer',
                change:     function (ui) {
                    var thisContainer = $('#panels-container .ui-state-highlight' ).closest('.cell' ).get(0);
                    if(typeof this.lastContainer != 'undefined' && this.lastContainer != thisContainer){
                        // Resize the new and the last containers
                        $(this.lastContainer ).closest('.grid-container').panelsResizeCells();
                        $(thisContainer).closest('.grid-container').panelsResizeCells();
                        thisContainer.click();
                    }
                    
                    // Refresh all the cell sizes after we stop sorting
                    this.lastContainer = thisContainer; 
                    
                },
                helper: function(e, el){
                    return el.clone().css('opacity', 0.9).addClass('panel-being-dragged');
                },
                stop:       function (ui, el) {
                    // Refresh all the cell sizes after we stop sorting
                    $( '#panels-container .grid-container' ).each( function () {
                        $(this).panelsResizeCells();
                    } );
                },
                receive:    function () {
                    $( this ).trigger( 'refreshcells' );
                }
            } )
            .bind( 'refreshcells', function () {
                // Set the cell for each panel
                // Refresh all the cell sizes after we stop sorting
                $( '#panels-container .grid-container' ).each( function () {
                    $(this).panelsResizeCells(true);
                } );

                $( '#panels-container .panel' ).each( function () {
                    var container = $( this ).closest( '.grid-container' );
                    $( this ).find( 'input[name$="[info][grid]"]' ).val( $( '#panels-container .grid-container' ).index( container ) );
                    $( this ).find( 'input[name$="[info][cell]"]' ).val( container.find( '.cell' ).index( $( this ).closest( '.cell' ) ) );
                } );

                $( '#panels-container .cell' ).each( function () {
                    $( this ).find( 'input[name$="[grid]"]' ).val( $( '#panels-container .grid-container' ).index( $( this ).closest( '.grid-container' ) ) );
                } );
            } )
            .disableSelection();
    }

    /**
     * Resize all the cells
     *
     * @param onlyHeight
     */
    $.fn.panelsResizeCells = function(){
        
        return $(this ).each(function(){
            var $$ = $(this);

            $$.find( '.grid, .grid .cell .cell-wrapper' ).css( 'height', 'auto' );
            var totalWidth = $$.find( '.grid' ).outerWidth();

            if ( $$.find( '.grid .cell' ).length > 1 ) {
                $$.find( '.grid .cell' ).each( function () {
                    if ( $( this ).is( '.first, .last' ) ) totalWidth -= 6;
                    else totalWidth -= 12;
                } );
            }
            
            var left = 0;
            var maxHeight = 0;
            $$.find( '.grid .cell' ).each( function () {
                maxHeight = Math.max( maxHeight, $( this ).height() );
                $( this )
                    .width( Math.floor( totalWidth * Number( $( this ).attr( 'data-percent' ) ) ) )
                    .css( 'left', left );
                left += $( this ).width() + 12;
            } );
            
            // Resize all the grids and cell wrappers
            $$.find( '.grid, .grid .cell .cell-wrapper' ).css( 'height', Math.max( maxHeight, 68 ) );
        })
    }

    var gridId = 0;
    var cellId = 0;

    /**
     * Create a new grid
     *
     * @param cells
     * @param weights
     * @param noSlide
     * @return {*}
     */
    window.panels.createGrid = function ( cells, weights ) {
        if ( weights == null || weights.length == 0 ) {
            weights = [];
            for ( var i = 0; i < cells; i++ ) {
                weights[i] = 1;
            }
        }
        
        var weightSum = weights.reduce( function ( a, b ) {
            return a + b;
        } );

        // Create a new grid container
        var container = $( '<div />' ).addClass( 'grid-container' ).appendTo( '#panels-container' );
        // Add the hidden field to store the grid order
        container.append( $( '<input type="hidden" name="grids[' + gridId + '][cells]" />' ).val( cells ) );

        container
            .append(
            $( '<div class="controls" />' )
                .append(
                $( '<button />' )
                    .button( {
                        icons:{primary:'ui-icon-remove'},
                        text: false
                    } )
                    .attr( 'data-tooltip', panelsLoc.buttons['delete'] )
                    .click( function () {
                        $( this ).removeTooltip();
                        
                        // Create an array that represents this grid
                        var containerData = [];
                        container.find('.cell' ).each(function(i, el){
                            containerData[i] = {
                                'weight' : Number($(this ).attr('data-percent')),
                                'widgets' : []
                            };
                            $(this ).find('.panel' ).each(function(j, el){
                                containerData[i]['widgets'][j] = {
                                    type : $(this ).attr('data-type'),
                                    data : $(this ).getPanelData()
                                }
                            })
                        });
                        
                        // Register this with the undo manager
                        window.panels.undoManager.register(
                            this,
                            function(containerData, position){
                                // Readd the grid
                                var weights = [];
                                for(var i = 0; i < containerData.length; i++){
                                    weights[i] = containerData[i].weight;
                                }
                                
                                var gridContainer = window.panels.createGrid( weights.length, weights );
                                window.panels.setupGrid( gridContainer );
                                
                                // Now, start adding the widgets
                                for(var i = 0; i < containerData.length; i++){
                                    for(var j = 0; j < containerData[i].widgets.length; j++){
                                        // Readd the panel
                                        var theWidget = containerData[i].widgets[j];
                                        var panel = $('#panels-dialog').panelsCreatePanel(theWidget.type, theWidget.data);
                                        window.panels.addPanel(panel, gridContainer.find('.panels-container' ).eq(i));
                                    }
                                }
                                
                                // Finally, reposition the gridContainer
                                if(position != gridContainer.index()){
                                    var current = $('#panels-container .grid-container' ).eq(position);
                                    if(current.length){
                                        gridContainer.insertBefore(current);
                                        $( '#panels-container' ).sortable( "refresh" )
                                        $( '#panels-container' ).find( '.cell' ).each( function () {
                                            // Store which grid this is in by finding the index of the closest .grid-container
                                            $( this ).find( 'input[name$="[grid]"]' ).val( $( '#panels-container .grid-container' ).index( $( this ).closest( '.grid-container' ) ) );
                                        } );

                                        $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
                                    }
                                }
                                
                                gridContainer.hide().slideDown();
                                
                                
                                
                            },
                            [containerData, container.index()],
                            'Remove Panel'
                        );
                        
                        // Create the undo notification
                        $('#panels-undo-message' ).remove();
                        $('<div id="panels-undo-message" class="updated"><p>' + panelsLoc.messages.deleteColumns + ' - <a href="#" class="undo">' + panelsLoc.buttons.undo + '</a></p></div>' )
                            .appendTo('body')
                            .hide()
                            .fadeIn()
                            .find('a.undo')
                            .click(function(){
                                window.panels.undoManager.undo();
                                $('#panels-undo-message' ).fadeOut(function(){ $(this ).remove() });
                                return false;
                            })
                        ;
                        
                        // Finally, remove the grid container
                        container.slideUp( function () {
                            container.remove();
                            $( '#panels-container' )
                                .sortable( "refresh" )
                                .find( '.panels-container' ).trigger( 'refreshcells' );
                        } );
                        
                        return false;
                    } )

            )
                .append(
                $( '<div class="ui-button ui-button-icon-only grid-handle"><div class="ui-icon ui-icon-move"></div></div>' )
            )
        );

        var grid = $( '<div />' ).addClass( 'grid' ).appendTo( container );

        for ( var i = 0; i < cells; i++ ) {
            var cell = $(
                '<div class="cell" data-percent="' + (weights[i] / weightSum) + '">' +
                    '<div class="cell-wrapper panels-container"></div>' +
                    '<div class="cell-width"><div class="cell-width-left"></div><div class="cell-width-right"></div><div class="cell-width-line"></div><div class="cell-width-value"><span></span></div></div>' +
                    '</div>'
            );
            if ( i == 0 ) cell.addClass( 'first' );
            if ( i == cells - 1 ) cell.addClass( 'last' );
            grid.append( cell );

            // Add the cell information fields
            cell
                .append( $( '<input type="hidden" name="grid_cells[' + cellId + '][weight]" />' ).val( weights[i] / weightSum ) )
                .append( $( '<input type="hidden" name="grid_cells[' + cellId + '][grid]" />' ).val( gridId ) )
                .data( 'cellId', cellId )

            cellId++;
        }
        grid.append( $( '<div />' ).addClass( 'clear' ) );
        gridId++;
        
        return container;
    }

    /**
     * Clears all the grids
     */
    window.panels.clearGrids = function(){
        $('#panels-container .grid-container' ).remove();
    }

    $( window ).bind( 'resize', function ( event ) {
        if ( $( event.target ).hasClass( 'ui-resizable' ) ) return;
        $( '#panels-container .grid-container' ).panelsResizeCells();
    } );

    // Create a sortable for the grids
    $( '#panels-container' ).sortable( {
        items:    '> .grid-container',
        handle:   '.grid-handle',
        tolerance:'pointer',
        stop:     function () {
            $( this ).find( '.cell' ).each( function () {
                // Store which grid this is in by finding the index of the closest .grid-container
                $( this ).find( 'input[name$="[grid]"]' ).val( $( '#panels-container .grid-container' ).index( $( this ).closest( '.grid-container' ) ) );
            } );

            $( '#panels-container .panels-container' ).trigger( 'refreshcells' );
        }
    } );

    // Create the add grid dialog
    var gridAddDialogButtons = {};
    gridAddDialogButtons[panelsLoc.buttons.add] = function () {
        var num = Number( $( '#grid-add-dialog' ).find( 'input' ).val() );
        
        if ( num == NaN ) {
            alert( 'Invalid Number' );
            return false;
        }

        num = Math.round( num );
        num = Math.max( 1, num );
        num = Math.min( 10, num );
        var gridContainer = window.panels.createGrid( num );
        window.panels.setupGrid( gridContainer );
        gridContainer.hide().slideDown();
        $( '#grid-add-dialog' ).dialog( 'close' );
    };
    
    $( '#grid-add-dialog' )
        .show()
        .dialog( {
            dialogClass: 'panels-admin-dialog',
            autoOpen:false,
            modal:   true,
            title:   $( '#grid-add-dialog' ).attr( 'data-title' ),
            open:    function () {
                $( this ).find( 'input' ).val( 2 ).select();
            },
            buttons: gridAddDialogButtons
        })
        .keypress(function(e) {
            if (e.keyCode == $.ui.keyCode.ENTER) {
                // This is the same as clicking the add button
                $(this ).closest('.ui-dialog').find('.ui-button:eq(0)').click();
            }
        });
    ;
    console.log('here');

    $( '#so-panels-panels .handlediv' ).click( function () {
        // Trigger the resize to reorganise the columns
        setTimeout( function () {
            $( window ).resize();
        }, 150 );
    } )
} );