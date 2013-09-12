/**
 * (c) Greg Priday, freely distributable under the terms of the GPL 2.0 license.
 */

jQuery( function ( $ ) {

    /**
     * Add a new slide
     * @param data The current slide data
     */
    var addSlide = function ( data ) {
        var newSlide = $( '<li />' )
            .addClass( 'slide' )
            .html( $( '#slider-builder-slides-skeleton' ).html() )
            .appendTo( '#slider-builder-slides' );

        newSlide.find( '.slide-content' ).hide();
        $( '#slider-builder-slides' ).filter(':data(sortable)').sortable( "refresh" );
        updateMedia( newSlide );


        if ( data != undefined ) {
            // Add the data
            for ( field in data ) {
                newSlide.find( '*[data-field="' + field + '"]' ).val( data[field] ).trigger( 'change' );
            }

            if ( data.title != undefined && data.title != '' ) newSlide.find( '.slide-title strong' ).html( data.title );
        }

        $( '.siteorigin-media' ).change();
    }

    /**
     * Update all siteorigin media fields
     * @param parent
     */
    var updateMedia = function ( parent ) {
        if ( parent == undefined ) parent = $( 'body' );

        parent.find( 'select.siteorigin-media' ).each( function () {
            var $$ = $( this );
            var v = Number( $$.val() );

            if ( Object.keys( siteoriginSlider.images ).length ) {
                $$.find( 'option[value!="-1"]' ).remove();

                // siteoriginSlider is defined as a script localization
                for ( var i in siteoriginSlider.images ) {
                    $$.append(
                        $( '<option>' + siteoriginSlider.images[i]['title'] + '</option>' )
                            .attr( 'value', i )
                            .attr( 'data-url', siteoriginSlider.images[i]['url'] )
                    );
                }
            }

            if ( v == -1 ) for ( var i in siteoriginSlider.images ) {
                v = i;
                break;
            }
            $$.val( v );
        } );
    }

    // Update the thumbnail when a new slide images is chosen
    $( '.siteorigin-media' ).live( 'change', function () {
        var $$ = $( this );
        var s = $$.find( 'option:selected' );

        if ( Number( s.val() ) == -1 ) {
            $$.siblings( '.thumbnail-wrapper' ).slideUp();
        }
        else {
            $$.siblings( '.thumbnail-wrapper' ).slideDown().find( '.thumbnail' ).attr( 'src', s.attr( 'data-url' ) );
        }

    } )

    // Add all the initial slides
    if ( siteoriginSlider.slides.length ) {
        for ( var i = 0; i < siteoriginSlider.slides.length; i++ ) {
            addSlide( siteoriginSlider.slides[i] );
        }
    }

    // Update the title when it changes
    $( '#slider-builder-slides input.title-field' ).live( 'change', function () {
        var $$ = $( this );
        var title = $$.val();
        if ( title == '' ) title = '&nbsp'
        $$.closest( '.slide' ).find( '.slide-title strong' ).html( title );
    } );

    // Create the sortable
    $( '#slider-builder-slides' ).sortable( {
        placeholder:'state-highlight',
        handle:     '.slide-title'
    } );

    // Click the widget action button
    $( '#slider-builder .widget-action' ).live( 'click', function () {
        var $$ = $( this );
        var slide = $$.closest( '.slide' );
        slide.toggleClass( 'expanded' );
        slide.find( '.slide-content' ).slideToggle( 'fast' );
    } );

    // Add a new slide
    $( '#slider-builder-add' ).click( function () {
        addSlide();
    } );

    // When the media browser is closed, fetch all the images
    $( "#content-add_media" ).one( 'click', function () {
        var old_tb_remove = window.tb_remove;
        window.tb_remove = function () {
            old_tb_remove(); // calls the tb_remove() of the Thickbox plugin

            // Get a refreshed list of media
            $.get(
                ajaxurl,
                { post_ID:$( '#post_ID' ).val(), action:'siteorigin_slider_images'},
                function ( data ) {
                    // Update the siteoriginSlider value
                    siteoriginSlider.images = data;

                    // Update the media
                    updateMedia();
                }
            );
        };
    } );

    // Handle deleting a slide
    $( '#slider-builder a.delete' ).live( 'click', function () {
        var $$ = $( this );
        if ( confirm( $$.attr( 'data-confirm' ) ) ) {
            $$.closest( '#slider-builder-slides li' ).slideUp( function () {
                $( this ).remove();
            } );
        }
    } );

    // Remove the skeleton before submitting the form
    $( 'form#post' ).submit( function () {
        $( '#slider-builder-slides-skeleton' ).remove();
    } );
} );