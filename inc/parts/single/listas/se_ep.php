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

$postid = $post->ID;
$tmdb   = get_post_meta($post->ID, "ids", $single = true);
$ic     = doo_season_of($tmdb);
if(!empty($ic)){ ?>
<div id="episodes" class="sbox fixidtab">
<h2><?php _d('Seasons and episodes'); ?></h2>
<div id="serie_contenido">
<?php
$seasons = $ic['temporada']['all'];
$episodes = $ic['capitulo']['all'];
if(!empty($seasons)) {
echo '<div id="seasons">';
}
$accountant = 0; foreach($seasons as $key_t=>$value_t) { ?>
<div class="se-c">
	<div class="se-q">
		<span class="se-t <?php if($accountant == 0){echo "se-o";} ?>"><?php echo $value_t['season']; ?></span>
		<span class="title"><?php _d('Season'); ?> <?php echo doo_isset($value_t,'season'); ?> <i><?php doo_date_compose(doo_data_of('air_date',doo_isset($value_t,'id'))); ?></i>
		<?php $dato = doo_data_of('_starstruck_avg',doo_isset($value_t,'id')); if($dato >= '1') { ?><div class="se_rating"><?php echo $dato; ?></div><?php } ?>
		</span>
	</div>
	<div  class="se-a" <?php if($accountant == 0){echo "style='display:block'";} ?>>
		<ul class="episodios">
	<?php foreach($episodes as $key_c=>$value_c) { if(doo_isset($value_t,'season') == doo_isset($value_c,'season')) { ?>
			<li>
				<div class="imagen"><a href="<?php echo get_permalink(doo_isset($value_c,'id')); ?>"><img src="<?php if($thumb_id = get_post_thumbnail_id(doo_isset($value_c,'id'))) { $thumb_url = wp_get_attachment_image_src($thumb_id,'dt_episode_a', true); echo $thumb_url[0]; } else { doo_compose_image('dt_backdrop',doo_isset($value_c,'id'), 'w154'); } ?>"></a></div>
				<div class="numerando"><?php echo doo_isset($value_t,'season'); ?> - <?php echo doo_data_of('episodio',$value_c['id']); ?></div>
				<div class="episodiotitle">
					<a href="<?php echo get_permalink(doo_isset($value_c,'id')); ?>"><?php if(doo_data_of('episode_name',doo_isset($value_c,'id')) != __d('no data')) { echo doo_data_of('episode_name',doo_isset($value_c,'id')); } else { echo '<i class="icon-update"></i> ' . __d('Coming soon'); } ?></a>
					<span class="date"><?php doo_date_compose(doo_data_of('air_date',doo_isset($value_c,'id'))); ?></span>
				</div>
			</li>
	<?php } } ?>
	</ul>
	</div>
</div>
<?php
$accountant++;
}
if(!empty($seasons)){echo '</div>';
}
?>
</div>
</div>
<?php } else { global $user_ID; if( $user_ID ) : if( current_user_can('level_10') ) :
if(!doo_get_postmeta('clgnrt')) { ?>
<div class="sbox">
 <a class="button main dtload" href="<?php echo wp_nonce_url( admin_url('admin-ajax.php?action=seasons_ajax','relative').'&se='.doo_get_postmeta('ids').'&link='.$id,'add_seasons','seasons_nonce'); ?>"><?php _d('Generate seasons'); ?></a>
</div>
<?php } endif; endif; } ?>
<script>
jQuery(document).ready(function($) {
	$(".se-q").click( function () {
	  var container = $(this).parents(".se-c");
	  var answer = container.find(".se-a");
	  var trigger = container.find(".se-t");
	  answer.slideToggle(200);
	  if (trigger.hasClass("se-o")) {
		trigger.removeClass("se-o");
	  }
	  else {
		trigger.addClass("se-o");
	  }
	})
});
</script>
