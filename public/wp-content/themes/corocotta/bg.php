<?php
/*	php for getting random image from sprite for site.
images are 940px x 198px, included with no white space in the main image*/
	$img = '/wp-content/corocotta-headers/bgs.jpg'; // the main image
	$total = 13 ; // the total number of images in main
	$selector = rand(1, $total) ; // randomize which img will be shown
	$selector -= 1 ; // 1st is 0
	$top = $selector * 198 ; // convert # to height
	$bottom = $top + 198 ;  

	echo "background-position: 0px -{$top}px;";
?>