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

// Options
$oderby   = cs_get_option('featuredmodorderby','modified');
$oder     = cs_get_option('featuredmodorder','DESC');
$numitems = cs_get_option('featureditems','8');
$slider	  = doo_is_true('featuredcontrol','slider');
$autoplay = doo_is_true('featuredcontrol','autopl');
$addIdOWL = ($slider == true) ? 'id="featured-titles" ' : false;

// Generate Query
$query = array(
	'post_type'		=> array('tvshows'),
	'posts_per_page'=> $numitems,
	'meta_key'		=> 'dt_featured_post',
	'meta_value'	=> '1',
	'order'			=> $oder,
	'orderby'		=> $oderby
);

// Get Post
$featured = new WP_Query($query);
if ($featured->have_posts()) {
	echo '<header>';
	echo '<h2>'. __d('Featured TV Shows') .'</h2>';
	if($slider == 'true') {
		echo '<div class="nav_items_module">';
		echo '<a class="btn prevf"><i class="icon-caret-left"></i></a>';
		echo '<a class="btn nextf"><i class="icon-caret-right"></i></a>';
		echo '</div>';
	}
	echo '</header>';
	echo '<div id="featload" class="load_modules">'. __d('Loading..'). '</div>';
	echo '<div '.$addIdOWL.'class="items featured">';
	while($featured->have_posts()) {
		// Item data
		$featured->the_post();
		$thumb_id	= get_post_thumbnail_id();
		$thumb_url	= wp_get_attachment_image_src($thumb_id,'dt_poster_a', true);
		$rating		= doo_get_postmeta( DOO_MAIN_RATING );
		$imdb		= ( $a = doo_get_postmeta('imdbRating')) ? $a : '0';
		$thePoster	= ($thumb_id) ? $thumb_url[0] : doo_compose_image('dt_poster', $post->ID, 'w185', false, true );
		$theRating	= ($rating) ? $rating : $imdb;
		$theYear	= ($mostrar = $terms = strip_tags( $terms = get_the_term_list( $post->ID, 'dtyear') ) ) ? $mostrar : '&nbsp;';
		echo '<article id="post-featured-'. get_the_ID(). '" class="item '. get_post_type(). '">';
		echo '<div class="poster">';
		echo '<img src="'.$thePoster.'" alt="'.get_the_title().'">';
		echo '<div class="rating"><span class="icon-star2"></span> '.$theRating.'</div>';
		doo_delete_post_link('<span class="icon-times-circle"></span>', '<i class="delete">', '</i>');
		echo '<div class="featu">'.  __d('Featured'). '</div>';
		echo '<a href="'.get_the_permalink().'"><div class="see"></div></a>';
		echo '</div>';
		echo '<div class="data dfeatur">';
		echo '<div class="mark"><i class="icon-local_play"></i></div>';
		echo '<h3>';
		echo '<a href="'. get_the_permalink(). '">'. get_the_title() .'</a>';
		echo '</h3>';
		echo '<span>'. $theYear .'</span>';
		echo '</div></article>';
	}
	echo '</div>';
}
// Reset Query
wp_reset_query();
