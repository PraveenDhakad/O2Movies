<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @aopyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.3
*
*/

$postmeta  = doo_postmeta_episodes($post->ID);
$thumb_id  = get_post_thumbnail_id();
$thumb_url = wp_get_attachment_image_src($thumb_id,'dt_episode_a', true);
// End PHP
?>
<article class="item se <?php echo get_post_type(); ?>" id="post-<?php the_id(); ?>">
	<div class="poster">
		<img src="<?php if($thumb_id) { echo doo_isset($thumb_url,0); } else { doo_compose_image('dt_backdrop', $post->ID, 'w300'); } ?>" alt="<?php the_title(); ?>">
		<div class="season_m animation-1">
			<a href="<?php the_permalink() ?>">
				<span class="b"><?php echo doo_isset($postmeta,'temporada'); ?>x<?php echo doo_isset($postmeta,'episodio'); ?></span>
				<span class="a"><?php _d('season x episode'); ?></span>
				<span class="c"><?php echo doo_isset($postmeta,'serie'); ?></span>
			</a>
		</div>
		<?php doo_delete_post_link('<span class="icon-times-circle"></span>', '<i class="delete">', '</i>'); ?>
		<?php if($mostrar = get_the_term_list( $post->ID, 'dtquality')) {  ?><span class="quality"><?php echo strip_tags($mostrar); ?></span><?php } ?>
		<span class="serie"><?php echo doo_isset($postmeta,'serie'); ?>  ( <?php echo doo_isset($postmeta,'temporada'); ?> x <?php echo doo_isset($postmeta,'episodio'); ?> )</span>
	</div>
	<div class="data">
		<h3><?php echo doo_isset($postmeta,'episode_name'); ?></h3>
		<span><?php doo_date_compose(doo_isset($postmeta,'air_date')); ?></span>
	</div>
</article>
