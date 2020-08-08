<?php
/*
* -------------------------------------------------------------------------------------
* @author: O2Movies
*.@ThemeURI: https://github.com/PraveenDhakad/O2Movies
* @authorURI: https://PraveenDhakad.com/
* @License: General Public License
* -------------------------------------------------------------------------------------
*
* @since 0.0.1
*
*/

// Compose data MODULE
$pitm = cs_get_option('slideritems','10');
$orde = cs_get_option('slidermodorderby','date');
$ordr = cs_get_option('slidermodorder','DESC');

// Compose Query
$query = array(
	'post_type' => array('movies'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr
);

// End Data
?>
<div id="slider-movies" class="animation-1 slider">
	<?php query_posts($query); while(have_posts()){ the_post(); get_template_part('inc/parts/item_b'); } wp_reset_query(); ?>
</div>
