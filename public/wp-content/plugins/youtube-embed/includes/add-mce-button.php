<?php
/**
* TinyMCE Button Functions
*
* Add extra button(s) to TinyMCE interface
*
* @package	YouTube-Embed
*/

/**
* Set up TinyMCE button
*
* Add filters (assuming user is editing) for TinyMCE
*
* @uses     vye_set_general_defaults    Set default options
*
* @since 	2.0
*/

function youtube_embed_button() {

	// Ensure user is in rich editor and button option is switched on

	if ( get_user_option( 'rich_editing' ) == 'true' ) {

		$options = vye_set_general_defaults();
		if ( $options[ 'editor_button' ] != '' ) {

			// Ensure shortcode cookie is set

			$editor_sc = vye_set_editor_sc();

			// Add filters

			add_filter( 'mce_external_plugins', 'add_youtube_embed_mce_plugin' );
			add_filter( 'mce_buttons', 'register_youtube_embed_button' );
		}
	}
}
add_action( 'init', 'youtube_embed_button' );

/**
* Register new TinyMCE button
*
* Register details of new TinyMCE button
*
* @since	2.0
*
* @param	string	$buttons	TinyMCE button data
* @return	string				TinyMCE button data, but with new YouTube button added
*/

function register_youtube_embed_button( $buttons ) {

	global $wp_version;

	if ( ( float ) $wp_version < 3.9 ) {
		array_push( $buttons, '|', 'YouTube' );
	} else {
		array_push( $buttons, 'mce4_youtube_button' );
	}

	return $buttons;
}

/**
* Register TinyMCE Script
*
* Register JavaScript that will be actioned when the new TinyMCE button is used
*
* @since	2.0
*
* @param	string	$plugin_array	Array of MCE plugin data
* @return	string					Array of MCE plugin data, now with URL of MCE script
*/

function add_youtube_embed_mce_plugin( $plugin_array ) {
	
	global $wp_version;

	if ( ( float ) $wp_version < 3.9 ) {	
		$plugin_array[ 'YouTube' ] = plugins_url() . '/youtube-embed/js/mce-button.min.php';
	} else {
		$plugin_array[ 'mce4_youtube_button' ] = plugins_url() . '/youtube-embed/js/mce4-button.min.php';
	}

	return $plugin_array;
}
?>