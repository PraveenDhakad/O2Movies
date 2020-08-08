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
// Header
get_header();
// Glossary
doo_glossary();
// Modules
$default = array(
	'slider'        => false,
	'featured-post' => false,
	'movies'        => false,
	'ads'           => false,
	'tvshows'       => false,
	'seasons'       => false,
	'episodes'      => false,
	'top-imdb'      => false,
	'blog'          => false
);
// Options
$fullwid = cs_get_option('homefullwidth');
$modules = cs_get_option('homepage');
$modules = (isset($modules['enabled'])) ? $modules['enabled'] : $default;
$hoclass = ($fullwid == true) ? ' full_width_layout' : false;
// Print home
echo '<div class="module">';
echo '<div class="content'.$hoclass.'">';
if(!empty($modules)){
	// Get template
	foreach($modules as $template => $template_name) {
		get_template_part('inc/parts/modules/'. $template );
	}
}
echo '</div>';
// Sidebar
if(!$fullwid) {
	echo '<div class="sidebar scrolling"><div class="fixed-sidebar-blank">';
	dynamic_sidebar('sidebar-home');
	echo '</div></div>';
}
echo '</div>';
// Footer
get_footer();
