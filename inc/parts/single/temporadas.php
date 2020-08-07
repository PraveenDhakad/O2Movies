<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @aopyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.5
*
*/


// All Postmeta
$postmeta = doo_postmeta_seasons($post->ID);
$adsingle = doo_compose_ad('_dooplay_adsingle');
// Get User ID
global $user_ID;
// Main data
$ids    = doo_isset($postmeta,'ids');
$temp   = doo_isset($postmeta,'temporada');
$serie  = doo_isset($postmeta,'serie');
$clgnrt = doo_isset($postmeta,'clgnrt');
// Imagenes
$thumb_id   = get_post_thumbnail_id();
$thumb_url  = wp_get_attachment_image_src($thumb_id,'dt_poster_a', true);
$poster_url = ($thumb_id) ? doo_isset($thumb_url,0) : doo_compose_image('dt_poster', $post->ID, 'w185', true, true);
// Link generator
$addlink = wp_nonce_url( admin_url('admin-ajax.php?action=seasonsf_ajax','relative').'&se='.$ids.'&te='.$temp.'&link='.$post->ID ,'add_episodes', 'episodes_nonce');

// End PHP
?>

<!-- Start Single POST -->
<div id="single" class="dtsingle">


    <!-- Start Post -->
    <?php if (have_posts()) :while (have_posts()) : the_post(); doo_set_views($post->ID); ?>
    <div class="content">


        <!-- Heading Info Season -->
        <div class="sheader">
        	<div class="poster">
        		<a href="<?php doo_get_tvpermalink($ids); ?>">
        			<img src="<?php echo $poster_url; ?>" alt="<?php the_title(); ?>">
        		</a>
        	</div>
        	<div class="data">
        		<h1><?php the_title(); ?></h1>
        		<div class="extra">
        			<?php if($d = doo_isset($postmeta,'air_date')) echo '<span class="date">'.doo_date_compose($d,false).'</span>'; ?>
        		</div>
        		<?php echo do_shortcode('[starstruck_shortcode]'); ?>
        		<div class="sgeneros">
        			<a href="<?php doo_get_tvpermalink($ids); ?>"><?php echo $serie; ?></a>
        		</div>
        	</div>
        </div>

        <!-- Single Post Ad -->
        <?php if($adsingle) echo '<div class="module_single_ads">'.$adsingle.'</div>'; ?>

        <!-- Content and Episodes list -->
        <div class="sbox">
            <?php if(get_the_content()){ ?>
            <div class="wp-content" style="margin-bottom: 10px;">
        	    <?php the_content(); ?>
        	</div>
            <?php } ?>
            <h2><?php  echo ($clgnrt) ? __d('Episodes') : __d('No episodes to show'); ?></h2>
            <?php get_template_part('inc/parts/single/listas/se'); ?>
        </div>


        <?php if( $user_ID && current_user_can('level_10') && !$clgnrt)  { ?>
        <!-- Epidose generator -->
        <div class="sbox">
            <a class="button main dtload" href="<?php echo $addlink; ?>"><?php _d('Generate episodes'); ?></a>
        </div>
        <?php } ?>


        <!-- Season social links -->
    	<?php doo_social_sharelink($post->ID); ?>

        <!-- Season comments -->
        <?php get_template_part('inc/parts/comments'); ?>

    </div>
    <!-- End Post-->
    <?php endwhile; endif; ?>


    <!-- Season sidebar -->
    <div class="sidebar scrolling">
    	<?php dynamic_sidebar('sidebar-seasons'); ?>
    </div>


</div>
<!-- End Single -->
