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
$orde = cs_get_option('seasonsmodorderby','date');
$ordr = cs_get_option('seasonsmodorder','DESC');
$auto = doo_is_true('seasonsmodcontrol','autopl');
$sldr = doo_is_true('seasonsmodcontrol','slider');
$pitm = cs_get_option('seasonsitems','10');
$titl = cs_get_option('seasonstitle','Seasons');
$pmlk = get_post_type_archive_link('seasons');
$totl = doo_total_count('seasons');

// Compose Query
$query = array(
	'post_type' => array('seasons'),
	'showposts' => $pitm,
	'orderby'   => $orde,
	'order'     => $ordr
);

// End Data
?>
<header>
	<h2><?=$titl;?></h2>
	<?php if($sldr == true && !$auto){ ?>
	<div class="nav_items_module">
	  <a class="btn prev2"><i class="icon-caret-left"></i></a>
	  <a class="btn next2"><i class="icon-caret-right"></i></a>
	</div>
	<?php } ?>
	<span><?=$totl;?> <a href="<?=$pmlk;?>" class="see-all"><?php _d('See all'); ?></a></span>
</header>
<div id="seaload" class="load_modules"><?php _d('Loading..');?></div>
<div <?php if($sldr == true) echo 'id="dt-seasons" '; ?>class="animation-2 items">
	<?php query_posts($query); while(have_posts()){ the_post(); get_template_part('inc/parts/item_se'); } wp_reset_query(); ?>
</div>
