/**
 * A jQuery mobile navigation.
 *
 * @author Greg Priday <greg@siteorigin.com>
 * @license Dual GPL, MIT - Which ever works for you.
 */
jQuery( function ( $ ) {
    $( 'nav.primary' ).each( function () {
        var $$ = $( this );
        var showSlide, activeSlide, frame;

        // Create the navigation button
        var button = $( '<a />' )
            .attr( 'href', '#' )
            .html( mobileNav.text.navigate )
            .addClass( 'mobilenav' )
            .insertAfter( $$ )
            .prepend('<span class="mobilenav-icon"></span>')
            .click( function () {
                // Store the initial scroll top
                var startScrollTop = $( window ).scrollTop();
                $( window ).scrollTop( 0 );

                if ( frame == null ) {
                    // Create the frame if we haven't already
                    frame = $( '<div class="mobile-nav-frame"><div class="title"><h3>' + mobileNav.text.navigate + '</h3></div><div class="slides"><div class="slides-container"></div></div></div>' ).appendTo( 'body' );
                    frame.find( '.title' )
                        .prepend( '<a href="#" class="back">Back</a><a href="#" class="close">Close</a>' )

                    // Create and insert the search form
                    $(
                        "<form method='get' action='" + mobileNav.search.url + "' class='search'>" +
                            "<input type='search' placeholder='" + mobileNav.search.placeholder + "' results='5' name='s' />" +
                            "<input type='submit' class='search-submit' /> " +
                            "</form>"
                    ).insertAfter( frame.find( '.title' ) );

                    frame.find( '.close' ).click( function () {
                        frame.fadeOut();
                        $( window ).scrollTop( startScrollTop );

                        return false;
                    } );

                    $( window ).resize( function () {
                        if ( !frame.is( ':visible' ) ) return;
                        frame.hide();
                        frame.height( $( document ).height() );
                        frame.width( $( document ).width() );
                        frame.show();
                    } );

                    $( 'body' ).bind( 'orientationchange', function () {
                        $( window ).resize();
                    } );

                    activeSlide = null;
                    showSlide = function ( i ) {
                        frame.find( '.slides-container .slide' ).hide();
                        activeSlide = frame.find( '.slides-container .slide' ).eq( i ).show();
                        if ( i == 0 ) frame.find( 'a.back' ).hide();
                        else frame.find( 'a.back' ).show();

                        // Change the title
                        if ( i != 0 ) {
                            frame.find( '.title h3' ).html( activeSlide.data( 'title' ) );
                        }
                        else {
                            frame.find( '.title h3' ).html( mobileNav.text.navigate );
                        }
                    }

                    frame.find( 'a.back' ).click( function () {
                        var parent = activeSlide.data( 'parent-slide' );
                        if ( parent != undefined ) {
                            showSlide( parent );
                        }

                        return false;
                    } );

                    var createMenu = function ( root ) {
                        var slide = $( '<div class="slide"><ul class="mobile"></ul></div>' ).appendTo( frame.find( '.slides-container' ) );

                        root.find( '> li' ).each( function () {
                            var $$ = $( this );
                            var ln = $( '<a></a>' )
                                .html( $$.find( '> a' ).html() )
                                .attr( 'href', $$.find( '> a' ).attr( 'href' ) )
                                .addClass( 'link' );
                            var li = $( '<li></li>' ).append( ln );

                            slide.find( 'ul' ).append( li );

                            if ( $$.find( '> ul' ).length > 0 ) {
                                var image = $( '<img />' )
                                    .attr( 'src', mobileNav.nextIconUrl + '?foo=car')
                                    .attr( 'width', 18 )
                                    .attr( 'height', 26 );

                                var next = $( '<a href="#" class="next"></a>' ).append( image );
                                li.prepend( next );

                                var child = $$.find( '> ul' ).eq( 0 );
                                var childSlide = createMenu( child );

                                childSlide.data( 'parent-slide', slide.index() );
                                childSlide.data( 'title', ln.html() );

                                li.find( 'a.next' ).click( function () {
                                    showSlide( childSlide.index() );
                                    return false;
                                } )
                            }
                        } );

                        return slide;
                    }


                    createMenu( $$.find( 'ul' ).eq( 0 ) );
                    showSlide( 0 );
                }

                $( window ).resize();
                frame.hide().fadeIn();
                showSlide( 0 );

                return false;
            } )
    } );
} );