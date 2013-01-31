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
        window.panels.resizeCells( $$ );

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
                    $$.find( '.grid .cell' ).resizable( 'disable' ).resizable( 'enable' );
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

                    window.panels.resizeCells( $$, true );
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
            window.panels.resizeCells( $$ );

            return false;
        } );

        $$.find( '.grid .cell' )
            .each( function () {
                var percent = Number( $( this ).attr( 'data-percent' ) );
                $( this ).find( '.cell-width-value span' ).html( Math.round( percent * 1000 ) / 10 + '%' );
            } )
            .find( '.panels-container' )
            .sortable( {
                placeholder:"ui-state-highlight",
                connectWith:".panels-container",
                tolerance:  'pointer',
                change:     function () {
                    window.panels.resizeCells( $$, true );
                },
                stop:       function () {
                    // Refresh all the cell sizes after we stop sorting
                    $( '#panels-container .grid-container' ).each( function () {
                        window.panels.resizeCells( $( this ), true );
                    } );
                },
                receive:    function () {
                    $( this ).trigger( 'refreshcells' );
                }
            } )
            .bind( 'refreshcells', function () {
                // Set the cell for each panel
                window.panels.resizeCells( $$ );

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
     * @param $$
     * @param onlyHeight
     */
    window.panels.resizeCells = function ( $$, onlyHeight ) {
        if ( onlyHeight == undefined ) onlyHeight = false;

        $$.find( '.grid .cell, .grid .cell-wrapper' ).css( 'height', 'auto' );
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
            maxHeight = Math.max( maxHeight, $( this ).outerHeight() );
            if ( !onlyHeight ) {
                $( this )
                    .width( Math.floor( totalWidth * Number( $( this ).attr( 'data-percent' ) ) ) )
                    .css( 'left', left );
                left += $( this ).width() + 12;
            }
        } );
        maxHeight = Math.max( maxHeight, 50 );

        $$.find( '.grid' ).height( maxHeight );
        $$.find( '.grid .cell .cell-wrapper' ).css( 'height', maxHeight );
    }

    var gridId = 0;
    var cellId = 0;

    /**
     * Create a new grid
     *
     * @param cells
     * @param weights
     * @return {*}
     */
    window.panels.createGrid = function ( cells, weights ) {
        if ( weights == undefined ) {
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
                        // Use this to indicate which grid we're going to remove
                        container.css( 'opacity', 0.25 );
                        if ( confirm( panelsLoc.messages['confirmDeleteColumns'] ) ) {
                            container.slideUp( function () {

                                container.remove();

                                $( '#panels-container' )
                                    .sortable( "refresh" )
                                    .find( '.panels-container' ).trigger( 'refreshcells' );
                            } );
                        }
                        else {
                            // Restore the container
                            container.animate( {opacity:1}, 'normal' );
                        }
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
                '<div class="cell" data-weight="1" data-percent="' + (weights[i] / weightSum) + '">' +
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

        $( '#panels-container .grid-container' ).each( function () {
            window.panels.resizeCells( $( this ) );
        } );
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
    $( '#grid-add-dialog' ).show().dialog( {
        dialogClass: 'panels-admin-dialog',
        autoOpen:false,
        modal:   true,
        title:   $( '#grid-add-dialog' ).attr( 'data-title' ),
        open:    function () {
            $( this ).find( 'input' ).val( 3 );
        },
        buttons: {
            'Add':function () {
                var num = Number( $( this ).find( 'input' ).val() );
                if ( num == NaN ) {
                    alert( 'Invalid Number' );
                    return false;
                }

                num = Math.round( num );
                num = Math.max( 1, num );
                num = Math.min( 10, num );
                window.panels.setupGrid( window.panels.createGrid( num ) );
                $( this ).dialog( 'close' );
            }
        }
    } );

    // Clicking on any of the grids in the grid add dialog
    $( '#grid-add-dialog .panel-grid' ).click( function () {
        var $$ = $( this );
        var cells = $$.attr( 'data-cells' ).split( '|' );
        cells = cells.map( Number );
        window.panels.setupGrid( window.panels.createGrid( cells.length, cells, $$.attr( 'data-type' ) ) );

        $( '#grid-add-dialog' ).dialog( 'close' );
    } );

    $( '#so-panels-panels .handlediv' ).click( function () {
        // Trigger the resize to reorganise the columns
        setTimeout( function () {
            $( window ).resize();
        }, 150 );
    } )
} );