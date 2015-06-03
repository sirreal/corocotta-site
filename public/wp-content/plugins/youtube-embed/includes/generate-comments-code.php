<?php
/**
* Create comments
*
* All functions relating to listing video comments
*
* @package	YouTube-Embed
* @since	3.2
*/

/**
* Get YouTube comments
*
* Generate output of video comments
*
* @since	3.2
*
* @uses     vye_extract_id              Extract the ID
* @uses		vye_get_file			    Get a file
* @uses     vye_set_general_defaults    Set default options
* @uses     vye_validate_id             Validate the ID
*
* @param	string	$id			        Video ID
* @param	string	$language			Language
* @return	string	$output		        Transcript output
*/

function vye_generate_comments( $paras ) {

	extract( $paras );
	if ( $id == '' ) { return vye_error( __( 'No YouTube ID was specified.', 'youtube-embed' ) ); }

	// Extract the ID if a full URL has been specified

	$id = vye_extract_id( $id );

	// Check what type of video it is and whether it's valid

	$embed_type = vye_validate_id( $id );
	if ( $embed_type != 'v' ) {
		if ( strlen( $embed_type ) > 1 ) {
			return vye_error( $embed_type );
		} else {
			return vye_error( sprintf( __( 'The YouTube ID of %s is invalid.', 'youtube-embed' ), $id ) );
		}
	}

	// Get general options

	$general = vye_set_general_defaults();

	// Get cache time

	if ( $cache == '' ) { $cache = $general[ 'comments_cache' ]; }

	// Check to see if cache is available

	if ( $cache != 0 ) {
		$cache_key = 'vye_comments_' . $id;
		$output = get_transient( $cache_key );
		$output = false;
		if ( $output !== false) { return $output; }
	}

	// Get transcript file

	$return = vye_get_file( 'https://gdata.youtube.com/feeds/api/videos/' . $id . '/comments?orderby=published' );
	$xml = $return[ 'file' ];
	$output = '';

	// If comments file exists process the XML and build the output

	if ( $return[ 'rc' ] == 0 ) {

		// Set default values

		$output = "<!-- Vixy YouTube Embed v" . youtube_embed_version . " -->\n" . '<div id="comments">' . "\n";
		$pos = 0;
		$eof = false;
		$number = 1;
		if ( !is_numeric( $avatar ) ) { $avatar = 32; }
		if ( !is_numeric( $limit ) ) { $limit = 10; }

		$tab = "\t";

		while ( !$eof ) {

			$entry = vye_extract_xml( $xml, 'entry' );
			if ( $entry != '' ) {

				// Extract field data from XML

				$published = human_time_diff( strtotime( vye_extract_xml( $entry, 'published' ) ) );
				$comment = vye_extract_xml( $entry, 'content' );
				$user = vye_extract_xml( $entry, 'name' );
				$profile_url = 'http://www.youtube.com/user/' . $user;
				$user_xml_url = vye_extract_xml( $entry, 'uri' );

				// Get user image URL from a seperate XML file

				$image_url = '';
				$user_xml = wp_remote_get( $user_xml_url );
				$start_pos = strpos( $user_xml[ 'body' ], '<media:thumbnail url=' );
				if ( $start_pos !== false ) {
					$start_pos = $start_pos + 22;
					$end_pos = strpos( $user_xml[ 'body' ], '/>', $start_pos );
					if ( $end_pos !== false ) {
						$end_pos = $end_pos - 2;
						$image_url = substr( $user_xml[ 'body' ], $start_pos, $end_pos - $start_pos + 1 );
					}
				}

				// Using the information fetched above built the comment output

				$output .= '<div id="comment-' . $number . '">' . "\n";
				$output .= $tag . '<div class="comment-author">' . "\n";
				if ( is_numeric( $avatar ) ) { $output .= $tab . $tab . '<img alt="" src="' . $image_url . '" class="avatar avatar-' . $avatar . '" height="' . $avatar . 'px" width="' . $avatar . 'px" />' . "\n"; }
				$output .= $tab . $tab . '<cite class="fn"><a href="' . $profile_url . '" rel="external nofollow" class="url">' . $user . '</a></cite>' . "\n";
				$output .= $tab . '</div>' . "\n";
				$output .= $tab . '<div class="comment-meta commentmetadata">' . $published . ' ' . __( 'ago', 'youtube-embed' ) . '</div>' . "\n";
				$output .= $tab . '<div class="comment-body"><p>' . $comment . '</p></div>' . "\n";
				$output .= '</div>' . "\n";

				$number++;
				if ( $number > $limit ) { $eof = true; }

			} else {
				$eof = true;
			}

			// Remove current record from the XML file

			$next_record = strpos( $xml, '</entry>' );
			if ( $next_record !== false ) { $xml = substr( $xml, $next_record + 8 ); }

		}

		$output .= "</div>\n<!-- End of Vixy YouTube Embed code -->\n";
	}

	// Save the cache

	if ( $cache != 0 ) { set_transient( $cache_key, $output, 60 * $cache );	}

	return $output;
}
?>