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

// Compose data MODULE
$sldr = doo_is_true('tvmodcontrol','slider');
$auto = doo_is_true('tvmodcontrol','autopl');
$orde = cs_get_option('tvmodorderby','date');
$ordr = cs_get_option('tvmodorder','DESC');
$pitm = cs_get_option('tvitems','10');
$titl = cs_get_option('tvtitle','TV Shows');
$pmlk = get_post_type_archive_link('tvshows');
$totl = doo_total_count('tvshows');
$eowl = ($sldr == true) ? 'id="dt-tvshows" ' : false;

// Compose Query
$query = array(
	'post_type' => array('tvshows'),
	'showposts' => $pitm,
	'orderby' 	=> $orde,
	'order' 	=> $ordr
);

// End Data
?>
<header>
	<h2><?=$titl;?></h2>
	<?php if($sldr == true && !$auto) { ?>
	<div class="nav_items_module">
	  <a class="btn prev4"><i class="icon-caret-left"></i></a>
	  <a class="btn next4"><i class="icon-caret-right"></i></a>
	</div>
	<?php } ?>
	<span><?=$totl;?> <a href="<?=$pmlk;?>" class="see-all"><?php _d('See all'); ?></a></span>
</header>
<div id="tvload" class="load_modules"><?php _d('Loading..');?></div>
<div <?=$eowl?>class="items">
	<?php query_posts($query); while(have_posts()){ the_post(); get_template_part('inc/parts/item'); } wp_reset_query(); ?>
</div>
