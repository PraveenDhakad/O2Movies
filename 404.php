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
get_header(); ?>
<div class="module">
	<?php get_template_part('inc/parts/sidebar'); ?>
	<div class="content">
		<header><h1><?php _d('Page not found'); ?></h1></header>
		<div class="search-page">
			<div class="no-result animation-2">
				<h2><?php _d('ERROR'); ?> <span>404</span></h2>
				<strong><?php _d('Suggestions'); ?>:</strong>
				<ul>
					<li><?php _d('Verify that the link is correct.'); ?></li>
					<li><?php _d('Use the search box on the page.'); ?></li>
					<li><?php _d('Contact support page.'); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
