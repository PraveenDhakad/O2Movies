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


// Compose Module Data
$items = cs_get_option('itopimdb','50');
$layou = cs_get_option('topimdblayout','movtv');

// Query for Movies
$query_movies = array(
	'post_type'    => array('movies'),
	'showposts'    => $items,
	'meta_key'     => 'end_time',
	'meta_compare' => '>=',
	'meta_value'   => time(),
	'meta_key'     => 'imdbRating',
	'orderby'      => 'meta_value_num',
	'order'        => 'desc'
);

// Query for TV Shows
$query_tvshows = array(
	'post_type'    => array('tvshows'),
	'showposts'    => $items,
	'meta_key'     => 'end_time',
	'meta_compare' => '>=',
	'meta_value'   => time(),
	'meta_key' 	   => 'imdbRating',
	'orderby' 	   => 'meta_value_num',
	'order' 	   => 'desc'
);


// Compose Templates
switch($layou){

	case 'movtv':
		echo "<div class='top-imdb-list tleft'>";
		echo "<h3>".__d('Movies')."</h3>";
		query_posts($query_movies); $num = 1; { while(have_posts()){ the_post(); doo_topimdb_item($num); $num++; } } wp_reset_query();
		echo "</div><div class='top-imdb-list tright'>";
		echo "<h3>".__d('TVShows')."</h3>";
		query_posts($query_tvshows); $num = 1; { while(have_posts()){ the_post(); doo_topimdb_item($num); $num++; } } wp_reset_query();
		echo "</div>";
	break;

	case 'movie':
		echo "<div class='top-imdb-list fix-layout-top'>";
		echo "<h3>".__d('Movies')."</h3>";
		query_posts($query_movies); $num = 1; { while(have_posts()){ the_post(); doo_topimdb_item($num); $num++; } } wp_reset_query();
		echo "</div>";
	break;

	case 'tvsho':
		echo "<div class='top-imdb-list fix-layout-top'>";
		echo "<h3>".__d('TVShows')."</h3>";
		query_posts($query_tvshows); $num = 1; { while(have_posts()){ the_post(); doo_topimdb_item($num); $num++; } } wp_reset_query();
		echo "</div>";
	break;
}

// End Module TOP IMDb
