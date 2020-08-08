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
<div id="page">
	<div class="single-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1 class="head"><?php the_title(); ?></h1>
		<div class="wp-content">
			<?php the_content(); ?>
		</div>
		<?php if(cs_get_option('commentspage') == true) { get_template_part('inc/parts/comments'); } ?>
	<?php endwhile; ?>
	</div>
</div>
<?php get_footer(); ?>
