<div id="serie_contenido" style="padding-top:0">
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

$tmdb = get_post_meta($post->ID, "ids", $single = true);
$current_season = get_post_meta($post->ID, "temporada", $single = true);
$data = doo_season_of($tmdb);


if(!empty($data)){ ?>
<div id="seasons">
<div class="se-c">
<div  class="se-a" style='display:block'>
<ul class="episodios">
<?php
$temporada = $data['temporada']['all'];
$capitulos = $data['capitulo']['all'];
foreach($temporada as $key_t=>$value_t){

foreach($capitulos as $key_c=>$value_c){

	if(doo_isset($value_t,'season') == doo_isset($value_c,'season')){
	if(doo_isset($value_c,'season') == $current_season){
?>
<li class="mark-<?php echo doo_data_of('episodio',doo_isset($value_c,'id')); ?>">
	<div class="imagen"><a href="<?php echo get_permalink( doo_isset($value_c,'id') ); ?>"><img src="<?php if($thumb_id = get_post_thumbnail_id(doo_isset($value_c,'id'))) { $thumb_url = wp_get_attachment_image_src($thumb_id,'dt_episode_a', true); echo doo_isset($thumb_url,0); } else { doo_compose_image('dt_backdrop', doo_isset($value_c,'id'), 'w154'); } ?>"></a></div>
	<div class="numerando"><?php echo doo_isset($value_t,'season'); ?>-<?php echo doo_data_of('episodio',doo_isset($value_c,'id')); ?></div>
	<div class="episodiotitle">
	<a href="<?php echo get_permalink( doo_isset($value_c,'id') ); ?>"><?php if(doo_data_of('episode_name',doo_isset($value_c,'id')) != "N/A") { echo doo_data_of('episode_name',doo_isset($value_c,'id')); } ?></a>
	<span class="date"><?php doo_date_compose(doo_data_of('air_date',doo_isset($value_c,'id')) ); ?></span>
	</div>
</li>
<?php
}
}
}
}
echo '</ul></div></div></div>';
}
?>
</div>
