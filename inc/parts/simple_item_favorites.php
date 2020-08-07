<article class="simple <?php echo get_post_type(); ?>" id="p<?php the_id(); ?>">
	<div class="poster">
		<img src="<?php if($thumb_id = get_post_thumbnail_id()) { $thumb_url = wp_get_attachment_image_src($thumb_id,'dt_poster_a', true); echo $thumb_url[0]; } else { doo_compose_image('dt_poster', $post->ID, 'w185'); } ?>" alt="<?php the_title(); ?>">
		<div class="profile_control animation-1">
			<span><a href="<?php the_permalink(); ?>"><?php _d('View'); ?></a></span>
			<span><a class="user_list_control buttom-control-<?php the_id(); ?>" data-nonce="<?php echo wp_create_nonce('dt-list-noce'); ?>" data-postid="<?php the_id(); ?>"><?php _d('Remove'); ?></a></span>
		</div>
	</div>
	<div class="data">
		<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
		<?php if($mostrar = $terms = strip_tags( $terms = get_the_term_list( $post->ID, 'dtyear'))) {  ?>
		<span><?php echo $mostrar; ?></span>
		<?php } else { ?>
		<span>&nbsp;</span>
		<?php } ?>
	</div>
</article>
