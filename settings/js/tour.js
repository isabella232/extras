jQuery(function($){
    var
        tourSetting,
        tourSettingPlaceholder,
        tourVideo,
        tourIndex = 0,
        tourModal = null;

    // Add the tour button
    var tourLink = $('<div id="start-theme-tour" class="screen-meta-toggle"><a href="#">' + siteoriginSettings.tour.buttonText + '</a></div>')
        .click(function(){
            startSettingsTour();
        });
    $('#screen-meta-links').append(tourLink);

    // Refresh the current tour frame
    var refreshTourFrame = function(){
        endTourFrame();

        var stepContent = siteoriginSettings.tour.content[tourIndex];

        tourModal.find('.step-title').html(stepContent.title);
        tourModal.find('.step-content').html(stepContent.content);

        // Lets add an image
        if(typeof stepContent.image != 'undefined') {
            tourModal.find('.step-image').attr('src', stepContent.image).show();
        }
        else {
            tourModal.find('.step-image').hide();
        }

        // Make the image clickable (display a video)
        if(typeof stepContent.video != 'undefined') {
            tourVideo = stepContent.video;
            tourModal.find('.play-video').show();
        }
        else {
            tourModal.find('.play-video').hide();
        }

        // Add a setting
        if(typeof stepContent.setting != 'undefined') {
            tourSetting = $('#siteorigin-settings-form div[data-field="' + stepContent.setting + '"]').closest('tr');

            tourSettingPlaceholder = tourSetting.clone();
            tourSetting.after( tourSettingPlaceholder );
            tourSettingPlaceholder.find('select').val( tourSetting.find('select').val() );

            // When any input value in the current tour setting changes, copy it back across to the placeholder
            tourSetting.find('input, select').change( function(){
                var newTourSettingPlaceholder = tourSetting.clone();
                tourSettingPlaceholder.after(newTourSettingPlaceholder);
                tourSettingPlaceholder.remove();
                tourSettingPlaceholder = newTourSettingPlaceholder;

                // If there's a select field, copy the value across
                tourSettingPlaceholder.find('select').val( tourSetting.find('select').val() );
            });


            tourModal.find('.siteorigin-settings-form tbody').append( tourSetting );

        }
        else {
            tourModal.find('.siteorigin-settings-form').hide();
        }

        // Hide/show the previous buttons
        if(tourIndex <= 0) {
            tourModal.find('.tour-previous').hide();
        }
        else {
            tourModal.find('.tour-previous').show();
        }

        // Choose the text we're going to display
        var nextButtonText = tourModal.find('.settings-form-buttons .tour-next span');
        nextButtonText.html(
            tourIndex ==  siteoriginSettings.tour.content.length - 1 ? nextButtonText.data('text-done') : nextButtonText.data('text-continue')
        );

    }

    // End the current tour frame
    var endTourFrame = function(){
        if( tourSetting != null && tourSettingPlaceholder != null ) {
            tourSetting.find('input, select').unbind('change');
            tourSettingPlaceholder.after(tourSetting).remove();
            tourSetting = null;
            tourSettingPlaceholder = null;
        }
    }

    var startSettingsTour = function(){
        tourIndex = 0;

        if( tourModal != null ) {
            tourModal.show();
            refreshTourFrame();
            return;

        }

        var template = $('#settings-tour-modal-template').html();

        tourModal = $(template).appendTo('body');

        // Lets set up the actions for the various buttons
        tourModal.find('.siteorigin-settings-preview').click(function(){
            $('#siteorigin-settings-form .siteorigin-settings-preview-button').click();
        });

        tourModal.find('.siteorigin-settings-save').click(function(){
            $('#siteorigin-settings-form .siteorigin-settings-submit-button').click();
        });

        // When the video player is clicked, open a popup with the Vimeo video
        tourModal.find('.play-video').click(function(e){
            e.preventDefault();
            // Open the Vimeo video in a new window
            window.open( 'https://player.vimeo.com/video/' + tourVideo + '?title=0&byline=0&portrait=0&autoplay=1', 'videowindow', 'width=640,height=362,resizeable,scrollbars');
        });

        // Handle clicking next and previous
        tourModal.find('.tour-next').click(function(e){
            e.preventDefault();
            if( tourIndex >= siteoriginSettings.tour.content.length - 1 ) {
                tourIndex = 0;
                endTourFrame();
                tourModal.hide();
            }
            else {
                tourIndex++;
                refreshTourFrame();
            }
            return false;

        });
        tourModal.find('.tour-previous').click(function(e){
            e.preventDefault();
            if(tourIndex > 0) {
                tourIndex--;
                refreshTourFrame();
            }
            return false;
        });

        // Close the tour when the background is clicked
        tourModal.find('#settings-tour-overlay, .siteorigin-settings-close').click(function(e){
            e.preventDefault();
            endTourFrame();
            tourModal.hide();
            tourIndex = 0;
        });

        refreshTourFrame();
    };

    // Start the tour if we have a #tour hash
    if( window.location.hash == '#tour' ) {
        startSettingsTour();
    }

} );