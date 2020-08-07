<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @aopyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.4
*
*/

// Data
$postmeta  = doo_postmeta_seasons($post->ID);
$thumb_id  = get_post_thumbnail_id();
$thumb_url = wp_get_attachment_image_src($thumb_id,'dt_poster_a', true);

// End PHP
?>
<article class="item se <?php echo get_post_type(); ?>" id="post-<?php the_id(); ?>">
	<div class="poster">
		<img src="<?php if($thumb_id) { echo doo_isset($thumb_url,0); } else { doo_compose_image('dt_poster', $post->ID, 'w185'); } ?>" alt="<?php the_title(); ?>">
		<?php if($values = doo_isset($postmeta,'temporada')) { ?>
		<div class="season_m animation-1">
			<a href="<?php the_permalink() ?>">
				<span class="a"><?php _d('season'); ?></span>
				<span class="b"><?php echo $values; ?></span>
				<span class="c"><?php echo doo_isset($postmeta,'serie'); ?></span>
			</a>
		</div>
		<?php } ?>
	</div>
	<div class="data">
		<h3><a href="<?php the_permalink() ?>"><?php _d('Season'); ?> <?php echo doo_isset($postmeta,'temporada'); ?></a></h3>
		<span><?php doo_date_compose(doo_isset($postmeta,'air_date')) ?></span>
	</div>
</article>
