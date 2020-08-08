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
get_header();
echo '<div class="module"><div class="content">';
echo '<header><h1>'. __d('Episodes'). '</h1><span>'.doo_total_count('episodes'). '</span></header>';
echo '<div id="archive-content" class="animation-2 items">';
if (have_posts()) {
    while (have_posts()) {
        the_post();
		get_template_part('inc/parts/item_ep');
	}
}
echo '</div>';
doo_pagination();
echo '</div>';
get_template_part('inc/parts/sidebar');
echo '</div>';
get_footer();
