jQuery( document ).ready(function(){

    "use strict";
 
    //This if statement checks if the color picker widget exists within jQuery UI

    //If it does exist then we initialize the WordPress color picker on our text input field

    if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){

        jQuery( '.color' ).wpColorPicker();

    }

    else {

        //We use farbtastic if the WordPress color picker widget doesn't exist

        jQuery( '.colorpicker' ).farbtastic( '#color' );

    }

    var file_frame;
 
  jQuery('#logo').live('click', function( event ){
 
    event.preventDefault();
 
    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }
 
    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });
 
    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      var attachment = file_frame.state().get('selection').first().toJSON();
 
      // Do something with attachment.id and/or attachment.url here
      jQuery.ajax({
        type: 'POST',   // Adding Post method
        url: logoSelector.ajaxurl, // Including ajax file
        data: {"action": "logo_save", "logo_url": attachment.url }, // Sending data dname to post_word_count function.
        success: function(data){ // Show returned data using the function.
            console.log(data);
        }
        });
      console.log(attachment.url);

    });
 
    // Finally, open the modal
    file_frame.open();
  });

  // Restore the main ID when the add media button is pressed
  jQuery('a.add_media').on('click', function() {
    wp.media.model.settings.post.id = wp_media_post_id;
  });

    

});

