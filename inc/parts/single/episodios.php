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

// All Postmeta
$postmeta = doo_postmeta_episodes($post->ID);
$adsingle = doo_compose_ad('_dooplay_adsingle');
$pviews   = doo_isset($postmeta,'dt_views_count');
$episode  = doo_isset($postmeta,'episodio');
$images   = doo_isset($postmeta, 'imagenes');
$player   = doo_isset($postmeta,'players');
$player   = maybe_unserialize($player);
$tviews   = ($pviews) ? sprintf( __d('%s Views'), $pviews) : __d('0 Views');
$dynamicbg  = esc_url(doo_rand_images($images,'original',true,true));
// Options
$player_ads = doo_compose_ad('_dooplay_adplayer');
$player_wht = cs_get_option('playsize','regular');
if ( ! empty( $player ) ) {
	$StreamalyPlayer = [
		count( $player ) + 1 =>
			[
				'name'   => 'Player',
				'select' => 'streamaly',
				'idioma' => '',
				'url'    => 'streamaly.me'
			]
	];

	$player = array_merge( $player, $StreamalyPlayer );
} else {
	$player = [
		0 =>
			[
				'name'   => 'Player',
				'select' => 'streamaly',
				'idioma' => '',
				'url'    => 'streamaly.me'
			]
	];
}
// End PHP
?>
<style>#seasons .se-c .se-a ul.episodios li.mark-<?php echo $episode; ?> {opacity: 0.2;}</style>


<!-- Big Player -->
<?php DooPlayer::viewer_big($player_wht, $player_ads, $dynamicbg); ?>


<!-- Start Single -->
<div id="single" class="dtsingle">


    <!-- Edit link response Ajax -->
    <div id="edit_link"></div>


    <!-- Start Post -->
    <?php if(have_posts()) :while (have_posts()) : the_post(); doo_set_views($post->ID); ?>
	<div class="content">


        <!-- Regular Player and Player Options -->
        <?php DooPlayer::viewer($post->ID, 'tv', $player, false, $player_wht, $tviews, $player_ads, $dynamicbg);?>


        <!-- Episodes paginator -->
		<?php get_template_part('inc/parts/single/listas/pag_episodes'); ?>


        <!-- Episode Info -->
		<div id="info" class="sbox">
			<h1 class="epih1"><?php echo doo_isset($postmeta,'serie'); ?> <?php echo doo_isset($postmeta,'temporada'); ?>x<?php echo doo_isset($postmeta,'episodio'); ?></h1>
			<div itemprop="description" class="wp-content">
				<h3 class="epih3"><?php echo doo_isset($postmeta,'episode_name'); ?></h3>
				<?php the_content(); ?>
				<?php if($images) { ?>
				<div id="dt_galery" class="galeria animation-2">
					<?php doo_get_images("w300",$images); ?>
				</div>
				<?php } ?>
			</div>
			<?php if($d = doo_isset($postmeta, 'air_date')) echo '<span class="date">'.doo_date_compose($d,false).'</span>'; ?>
		</div>

        <!-- Episode Social Links -->
		<?php doo_social_sharelink($post->ID); ?>

        <!-- Single Post Ad -->
        <?php if($adsingle) echo '<div class="module_single_ads">'.$adsingle.'</div>'; ?>

        <!-- Episode Links -->
		<?php if(DOO_THEME_DOWNLOAD_MOD) get_template_part('inc/parts/single/links'); ?>

        <!-- Season Episodes List -->
		<div class="sbox">
			<h2><?php echo doo_isset($postmeta,'serie'); ?> <?php _d('season'); ?> <?php echo doo_isset($postmeta,'temporada'); ?></h2>
			<?php get_template_part('inc/parts/single/listas/se'); ?>
		</div>

        <!-- Episode comments -->
		<?php get_template_part('inc/parts/comments'); ?>

	</div>
    <!-- End Post-->
	<?php endwhile; endif; ?>

    <!-- Episode Sidebar -->
    <div class="sidebar scrolling">
		<?php dynamic_sidebar('sidebar-tvshows'); ?>
	</div>
    <!-- End Sidebar -->


</div>
<!-- End Single -->
