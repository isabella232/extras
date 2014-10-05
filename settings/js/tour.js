jQuery(function($){
    var
        tourSetting,
        tourSettingPlaceholder,
        tourVideo,
        tourIndex = 0,
        tourModal;

    // Add the tour button
    var tourLink = $('<div id="start-theme-tour" class="screen-meta-toggle"><a href="#">' + siteoriginSettings.tour.buttonText + '</a></div>')
        .click(function(){
            tourIndex = 0;
            startSettingsTour();
        });
    $('#screen-meta-links').append(tourLink);

    // Load a specific tour frame
    var refreshTourFrame = function(){
        endTourFrame();

        var stepContent = siteoriginSettings.tour.content[tourIndex];

        tourModal.find('.step-title').html(stepContent.title);
        tourModal.find('.step-content').html(stepContent.content);

        // Lets add an image
        if(typeof stepContent.image != 'undefined') {
            tourModal.find('.step-image').attr('src', stepContent.image);
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

            tourSetting.find('input').change( function(){
                // When any input value in the current tour setting changes, copy it back across to the placeholder
                var newTourSettingPlaceholder = tourSetting.clone();
                tourSettingPlaceholder.after(newTourSettingPlaceholder);
                tourSettingPlaceholder.remove();
                tourSettingPlaceholder = newTourSettingPlaceholder;
            }).change();


            tourModal.find('.siteorigin-settings-form tbody').append( tourSetting );

        }
        else {
            tourModal.find('.siteorigin-settings-form tbody').hide();
        }

        // Hide/show the previous buttons
        if(tourIndex <= 0) {
            tourModal.find('.tour-previous').hide();
        }
        else {
            tourModal.find('.tour-previous').show();
        }
    }

    // End the current tour frame
    var endTourFrame = function(){
        if( tourSetting != null && tourSettingPlaceholder != null ) {
            tourSetting.find('input').unbind('change');
            tourSettingPlaceholder.after(tourSetting).remove();
            tourSetting = null;
            tourSettingPlaceholder = null;
        }
    }

    var startSettingsTour = function(){
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
                endTourFrame();
                tourModal.remove();
                return;
            }
            tourIndex++;
            refreshTourFrame();
        });
        tourModal.find('.tour-previous').click(function(e){
            e.preventDefault();
            if(tourIndex <= 0) return;
            tourIndex--;
            refreshTourFrame();
        });

        // Close the tour when the background is clicked
        tourModal.find('#settings-tour-overlay').click(function(){
            endTourFrame();
            tourModal.remove();
        });

        refreshTourFrame();
    };

    // Start the tour if we have a #tour hash
    if( window.location.hash == '#tour' ) {
        tourIndex = 0;
        startSettingsTour();
    }

} );