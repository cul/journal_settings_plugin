<?php
/*
	Plugin Name: Custom Journal Settings
	Plugin URI: http://cdrs.columbia.edu
	Description: Adds a custom journals settings page to your dash
	Author: Megan O'Neill
	Version: 0.1-alpha
	Author URI: http://cdrs.columbia.edu
	Domain Path: /lang
 */
function my_admin_menu() {
    $page = add_theme_page( 'Journal Settings', 'Journal Settings', 'edit_theme_options', 'my-theme-options', 'my_theme_options' );
    add_action( 'admin_print_styles-' . $page, 'my_admin_scripts' );
}
add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_scripts() {
    // We'll put some javascript & css here later
    wp_enqueue_media();
    wp_enqueue_script('the-color-picker', plugins_url('journal_settings_plugin/the-color-picker.js'), array( 'jquery' ));
    wp_localize_script( 'the-color-picker', 'logoSelector', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_localize_script( 'the-color-picker', 'backgroundSelector', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_media();
}

add_action("wp_ajax_logo_save", "logo_save");
add_action("wp_ajax_background_image_save", "background_image_save");

function logo_save(){
   update_option('logo_url', $_POST['logo_url']);
}

function background_image_save(){
   update_option('background_image_url', $_POST['background_image_url']);
}

function my_theme_options() {
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32" ><br></div>
        <h2>Journal Settings</h2>

        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php wp_nonce_field( 'update-options' ); ?>
            <?php settings_fields( 'my-theme-options' ); ?>
            <?php do_settings_sections( 'my-theme-options' ); ?>
            <p class="submit">
                <input name="Submit" type="submit" class="button-primary" value="Save Changes" />
            </p >
        </form>
    </div>
    <?php
}

function my_admin_init() {
    register_setting( 'my-theme-options', 'my-theme-options', 'validate_setting' );
    add_settings_section( 'section_general', 'General Settings', 'my_section_general', 'my-theme-options' );
    add_settings_field('logo', 'Logo:', 'logo_setting', 'my-theme-options', 'section_general');
    add_settings_field('back_image', 'Background Image:', 'back_image', 'my-theme-options', 'section_general');
    add_settings_field( 'back_color', 'Background Color', 'my_background_color', 'my-theme-options', 'section_general' );
	add_settings_field( 'heading_color', 'Heading Color', 'my_heading_color', 'my-theme-options', 'section_general' );
	add_settings_field( 'text_color', 'Text Color', 'my_text_color', 'my-theme-options', 'section_general' );
	add_settings_field( 'link_color', 'Link Color', 'my_setting_color', 'my-theme-options', 'section_general' );
	
    
    register_setting( 'my-theme-options', 'my-footer-options');
    add_settings_section( 'footer_general', 'Footer Settings', 'my_footer_general', 'my-theme-options' );
    add_settings_field('copyright', 'Copyright License:', 'copyright_settings', 'my-theme-options', 'footer_general'); 
    add_settings_field('copyright_url', '', 'copyright_setting_url', 'my-theme-options', 'footer_general'); 
    add_settings_field('manual_copyright', 'Custom Copyright Statement:', 'custom_copyright', 'my-theme-options', 'footer_general'); 
    add_settings_field( 'issn_print', 'ISSN(print)', 'my_print_issn', 'my-theme-options', 'footer_general' );
    add_settings_field( 'issn_online', 'ISSN(online)', 'my_online_issn', 'my-theme-options', 'footer_general' );
    add_settings_field('ac_partner', 'Academic Commons Partner?', 'ac_partner_setting', 'my-theme-options', 'footer_general');
    add_settings_field('full_text_setting', 'This site displays: ', 'full_text_setting', 'my-theme-options', 'footer_general');
}
add_action( 'admin_init', 'my_admin_init' );

function my_section_general() {
    _e( 'Edit your settings' );
}

//logo uploader
function logo_setting() {
 	echo '<input id="logo" type="file" name="logo" />';
    $current_logo = get_option('logo_url');
    if($current_logo){
        echo '<img id="logo_init" src="'. $current_logo .'">';
    }



 }

 function back_image() {
 	echo '<input id="back_image" type="file" name="back_image" />';
    $current_back_image = get_option('background_image_url');
    if($current_back_image){
        echo '<img id="back_image_init" src="'. $current_back_image .'">';
    }

 }

//for the link color
function my_setting_color() {
    $options = get_option( 'my-theme-options' );
    $color = ( $options['link_color'] != "" ) ? sanitize_text_field( $options['link_color'] ) : '#3D9B0C';
    echo '<input id="link_color" class="color" name="my-theme-options[link_color]" type="text" value="' . $color .'" />';

}

//for the background color
function my_background_color() {
    $options = get_option( 'my-theme-options' );
    $color = ( $options['background_color'] != "" ) ? sanitize_text_field( $options['background_color'] ) : '#3D9B0C';
    echo '<input id="back_color" class="color" name="my-theme-options[background_color]" type="text" value="' . $color .'" />';

}

//for the h1s etc
function my_heading_color() {
    $options = get_option( 'my-theme-options' );
    $color = ( $options['heading_color'] != "" ) ? sanitize_text_field( $options['heading_color'] ) : '#3D9B0C';
    echo '<input id="heading_color" class="color" name="my-theme-options[heading_color]" type="text" value="' . $color .'" />';
}

//for the text color
function my_text_color() {
    $options = get_option( 'my-theme-options' );
    $color = ( $options['text_color'] != "" ) ? sanitize_text_field( $options['text_color'] ) : '#3D9B0C';
    echo '<input id="text_color" class="color" name="my-theme-options[text_color]" type="text" value="' . $color .'" />';

}


//validating uploaded images
function validate_setting($my_theme_options) {
	$keys = array_keys($_FILES); $i = 0; foreach ( $_FILES as $image ) {   // if a files was upload
		if ($image['size']) {     // if it is an image
			if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
				$override = array('test_form' => false);       // save the file, and store an array, containing its location in $file
				$file = wp_handle_upload( $image, $override );
				$my_theme_options[$keys[$i]] = $file['url'];
			} else {       // Not an image.
				$options = get_option('my-theme-options');
				$my_theme_options[$keys[$i]] = $options[$logo];       // Die and let the user know that they made a mistake.
				wp_die('No image was uploaded.');
			}
		}   // Else, the user didn't upload a file.   // Retain the image that's already on file.
		else {
	   		 $options = get_option('my-theme-options');
	   		 $my_theme_options[$keys[$i]] = $options[$keys[$i]];
	   		 }
	   	$i++;
	   } return $my_theme_options;
}


//calling the color picker scripts
add_action( 'admin_enqueue_scripts', 'wp_enqueue_color_picker' );
function wp_enqueue_color_picker( ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker-script', plugins_url('the-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

//Inserting the style into the head
function my_wp_head() {
    $options = get_option( 'my-theme-options' );
    $color = $options['link_color'];
    $background_color = $options['background_color'];
    $text_color = $options['text_color'];
    $heading_color = $options['heading_color'];
    echo "<style> a { color: $color; }
    	   body {background-color: $background_color;
    	   		 color: $text_color; }
    	   h1, h2, h3, h4 { color: $heading_color; }
    	 </style>";

}

add_action( 'wp_head', 'my_wp_head' );


//ADDING FOOTER ACTION TO THE PLUGIN
function my_footer_general() {
    _e( 'Edit your settings' );
}

function copyright_settings() {
    $options = get_option( 'my-footer-options' );
    $copyright = ( $options['copyright'] != "" ) ? sanitize_text_field( $options['copyright'] ) : '';
    echo '<input id="copyright"  placeholder="name" name="my-footer-options[copyright]" type="text" value="' . $copyright .'" />';

}

function copyright_setting_url() {
    $options = get_option( 'my-footer-options' );
    $copyright_url = ( $options['copyright_url'] != "" ) ? sanitize_text_field( $options['copyright_url'] ) : '';
    echo '<input id="copyright_url"  placeholder="url" name="my-footer-options[copyright_url]" type="text" value="' . $copyright_url .'" />';

}

//textbox for print issn
function my_print_issn() {
    $options = get_option( 'my-footer-options' );
    $print = ( $options['print_issn'] != "" ) ? sanitize_text_field( $options['print_issn'] ) : '';
    echo '<input id="issn_print" name="my-theme-options[print_issn]" type="text" value="' . $print .'" />';

}

//textbox for online issn
function my_online_issn() {
    $options = get_option( 'my-footer-options' );
    $print = ( $options['online_issn'] != "" ) ? sanitize_text_field( $options['online_issn'] ) : '';
    echo '<input id="issn_online" name="my-theme-options[online_issn]" type="text" value="' . $print .'" />';
}

function ac_partner_setting() {
    $options = get_option( 'my-footer-options' );

    // Get the value of this option.
    $checked = $options['ac_partner'];

    // The value to compare with (the value of the checkbox below).
    $current = 1;

    // True by default, just here to make things clear.
    $echo = true;

    ?>
    <input id="ac_partner"  type="checkbox" name="my-footer-options[ac_partner]" type="text" value="1" <?php if ( 1 == $options['ac_partner'] ) echo 'checked="checked"'; ?> />

    <?php
}

function custom_copyright(){
    $options = get_option( 'my-footer-options' );
    $custom_copyright = ( $options['custom_copyright'] != "" ) ? sanitize_text_field( $options['custom_copyright'] ) : '';
    echo 'OR</br>';
    echo '<input id="custom_copyright" name="my-footer-options[custom_copyright]" type="text" value="' . $custom_copyright .'" />';
}


function full_text_setting(){
    $options = get_option( 'my-footer-options' );
    ?>
    Abstract
    <input type="radio" name="my-footer-options[full_text_setting]" value="abstract"<?php checked( 'abstract' == $options['full_text_setting'] ); ?> />
    Full Text
    <input type="radio" name="my-footer-options[full_text_setting]" value="full_text"<?php checked( 'full_text' == $options['full_text_setting'] ); ?> />
    <?php
}
