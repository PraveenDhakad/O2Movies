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
$classlinks = new DooLinks;
$postmeta = doo_postmeta_movies($post->ID);
$adsingle = doo_compose_ad('_dooplay_adsingle');
// Movies Meta data
$trailer = doo_isset($postmeta,'youtube_id');
$pviews  = doo_isset($postmeta,'dt_views_count');
$player  = doo_isset($postmeta,'players');
$player  = maybe_unserialize($player);
$tviews  = ($pviews) ? sprintf( __d('%s Views'), $pviews) : __d('0 Views');
//  Image
$thumb_id   = get_post_thumbnail_id();
$thumb_url  = wp_get_attachment_image_src($thumb_id,'dt_poster_a', true);
$poster_url = ($thumb_id) ? doo_isset($thumb_url,0) : doo_compose_image('dt_poster', $post->ID, 'w185', true, true);
$dynamicbg  = esc_url(doo_rand_images(doo_isset($postmeta,'imagenes'),'original',true,true));

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
// Dynamic Background
if(cs_get_option('dynamicbg') == true) { ?>
<style>
#dt_contenedor {
    background-image: url(<?php echo $dynamicbg; ?>);
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
    background-position: 50% 0%;
}
</style>
<?php } ?>


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
        <?php DooPlayer::viewer($post->ID, 'movie', $player, $trailer, $player_wht, $tviews, $player_ads, $dynamicbg);?>

        <!-- Head movie Info -->
        <div class="sheader">
        	<div class="poster">
        		<img src="<?php echo $poster_url; ?>" alt="<?php the_title(); ?>">
        	</div>
        	<div class="data">
        		<h1><?php the_title(); ?></h1>
        		<div class="extra">
        		<?php
                // Movie Meta Info
                if($d = doo_isset($postmeta,'tagline')) echo "<span class='tagline'>{$d}</span>";
        		if($d = doo_isset($postmeta,'release_date')) echo "<span class='date'>".doo_date_compose($d,false)."</span>";
        		if($d = doo_isset($postmeta,'Country')) echo "<span class='country'>{$d}</span>";
        		if($d = doo_isset($postmeta,'runtime')) echo "<span class='runtime'>{$d} ".__d('Min.')."</span>";
        		if($d = doo_isset($postmeta,'Rated')) echo "<span class='C{$d} rated'>{$d}</span>";
                // end..
                ?>
        		</div>
        		<?php echo do_shortcode('[starstruck_shortcode]'); ?>
        		<div class="sgeneros">
        		<?php echo get_the_term_list($post->ID, 'genres', '', '', ''); ?>
        		</div>
        	</div>
        </div>

        <!-- Movie Tab single -->
        <div class="single_tabs">
            <?php if(is_user_logged_in() && doo_is_true('permits','eusr')){ ?>
        	<div class="user_control">
        		<?php dt_list_button($post->ID); dt_views_button($post->ID); ?>
        	</div>
            <?php } ?>
        	<ul id="section" class="smenu idTabs">
            	<li><a id="main_ali" href="#info"><?php _d('Info'); ?></a></li>
            	<?php if(doo_here_links($post->ID)) echo '<li><a href="#linksx">'.__d('Links').'</a></li>'; ?>
                <li><a href="#cast"><?php _d('Cast'); ?></a></li>
                <li id="report_li"><a href="#report"><?php _d('Report'); ?></a></li>
        	</ul>
        </div>

        <!-- Single Post Ad -->
        <?php if($adsingle) echo '<div class="module_single_ads">'.$adsingle.'</div>'; ?>

        <!-- Report video Error -->
        <div id="report" class="sbox">
            <?php get_template_part('inc/parts/single/report-video'); ?>
        </div>

        <!-- Movie more info -->
        <div id="info" class="sbox">

<!-- Single Movie Post Data -->
<h1 style="text-align: center;"><strong><span style="font-family: 'book antiqua', palatino, serif;"><span style="color: #ff0000; font-size: 18pt;">Download <span style="font-family: georgia, palatino, serif;"><?php the_title(); ?> </span></span><span style="color: #ff0000; font-size: 18pt;"> Full Movie In HD 1080p/720p</span></span></strong></h1>
            <p><span style='color: rgb(245, 62, 198); font-family: "Times New Roman", Times, serif; font-size: 17px;'><strong>Also Known As : <?php the_title(); ?> -</strong>
            </span><span style='font-family: "Times New Roman", Times, serif; font-size: 17px;'> You Can Always <strong> download <?php the_title(); ?> Movie</strong> in Full HD – Every film fast to your Own PC And Mobile. Latest Movie One <?php the_title(); ?>Download, <strong>Link Of The Download In Bottom In 720p & 1080p Quality</strong>.
           </span></p><br>
         
            <img class="aligncenter wp-image-36" alt="<?php the_title(); ?>" width="328" height="492" src="<?php echo get_the_post_thumbnail(); ?><br><p style="text-align: center;"><span style="font-size: 12pt; color: #ff0000; font-family: 'book antiqua', palatino, serif;"><strong>Share with Your Friends If you like our Website :-)</strong></span><br><span style="font-size: 12pt; color: #ff0000; font-family: 'book antiqua', palatino, serif;"><strong>Don&rsquo;t Forget to Bookmark Our Website :-)</strong></span> </p><br><br>
            <div style='font-family: "Times New Roman", Times, serif; font-size: 17px;' itemprop="description" class="wp-content">
            <span style="font-family: georgia, palatino, serif;"><span style="color: #ff0000;"></span> <?php the_content(); ?></span><hr>
                <p style="text-align: center;"><span style="font-family: georgia, palatino, serif; font-size: 14px;"><strong><span style="color: #3366ff;"><?php the_title(); ?> Movie Download</span>. I Hope You Like Our Website <a href="<?php echo site_url(); ?>"><?php echo site_url(); ?></a></strong></span></p><hr>
                
                <a href="//graizoah.com/afu.php?zoneid=2489978" target="_blank" rel="nofollow noopener"><img class="size-full wp-image-143 aligncenter" src="https://o2movies.site/wp-content/uploads/2020/01/info-image-min.png" alt="Movie Info" width="278" height="94" /></a>
                
                
                                <p style="text-align: left;"><span style="font-family: georgia, palatino, serif;"><strong><span style="font-size: 14px;">Title:</span></strong><span style="font-size: 14px;">&nbsp;<?php the_title(); ?></span></span><span style="font-size: 14px;"><br><span style="font-family: georgia, palatino, serif;"><strong>Genre:&nbsp;</strong><!-- Grnre List --><strong>&nbsp;</strong></span><br><span style="font-family: georgia, palatino, serif;"><strong>Release Date:</strong>&nbsp;<?php if($d = doo_isset($postmeta,'release_date')) echo "<span class='date'>".doo_date_compose($d,false)."</span>"; ?></span><br><span style="font-family: georgia, palatino, serif;"><strong>Trailer:</strong> Watch</span><br><span style="font-family: georgia, palatino, serif;"><strong>RUNTIME:&nbsp;Not Available </strong></span><br><span style="font-family: georgia, palatino, serif;"><strong>Category:&nbsp;</strong><a href="<?php echo site_url('/genre/hollywood-movie/') ?>" rel="noopener noreferrer" target="_blank">Not Available</a></span><br><span style="font-family: georgia, palatino, serif;"><strong>Language:&nbsp;</strong>Dual Audio</span><br><span style="font-family: georgia, palatino, serif;"><strong>Information Source:</strong> WIKIPEDIA</span><br><span style="font-family: georgia, palatino, serif;"><strong>IMDB:&nbsp;https://www.imdb.com/title/......../</strong></span><br><span style="font-family: georgia, palatino, serif;"><strong><span style="color: #00ff00;">Subtitles</span>:</strong> Download Movie Subtitles (Arabic, English, Hindi &hellip;..) <?php the_title(); ?> Subtitles For Free Here.</span><br><span style="font-family: georgia, palatino, serif;"><span style="color: #ff00ff;"><strong>Skip Directly To Download:</strong></span> <a href="<?php echo site_url(); ?>" rel="noopener noreferrer" target="_blank">Click Here</a></span>&nbsp;</span></p><hr>
                                
                                <a title="Click Here To Download" href="//graizoah.com/afu.php?zoneid=2489978" target="_blank" rel="nofollow noopener"><img class="aligncenter wp-image-138 size-full" src="https://o2movies.site/wp-content/uploads/2020/01/new-download-link-min.png" alt="Click Here TO Download" width="300" height="112" /></a>
                                
                                <p style="text-align: center;"><span style="font-family: georgia, palatino, serif;"><strong><a href="<?php echo site_url(); ?>"><span style="font-size: 16px;">Bollywood Movies, Hollywood Movies, Hindi Dubbed Movies, Punjabi Movies</span></a></strong></span></p>
<p style="text-align: center;"><span style='font-family: "book antiqua", palatino, serif; font-size: 24px;'><?php the_title(); ?> Trailer :</span></p>
<!-- Youtube Video Embed URL -->
<?php
$youtubeVideo = get_field('youtube_video_url');
if ( !empty ( $youtubeVideo) ):
         ?>
        <div class="embed-container">
    <?php the_field('youtube_video_url'); ?>
</div>
<?php endif; ?>
<style>
    .embed-container { 
        position: relative; 
        padding-bottom: 56.25%;
        overflow: hidden;
        max-width: 100%;
        height: auto;
       
    } 

    .embed-container iframe,
    .embed-container object,
    .embed-container embed { 
        position: absolute;
        
        top: 0;
        left: 9%;
        width: 85%;
        height: 85%;
    }
</style>

<p style="text-align: center;"><span style="font-family: georgia, palatino, serif; color: #ff00ff;"><strong><span style="font-size: 16px;">PLEASE Click ON Images For Original Screen Size</span></strong></span></p>

<?php 
$image = get_field('image');
if( !empty( $image ) ): ?>
    <img class="size-full wp-image-39681 aligncenter" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
<?php endif; ?>

<p style="text-align: center;"><span style='font-size: 16px; color: rgb(255, 0, 0); font-family: "book antiqua", palatino, serif;'><strong>Share with Your Friends If you like our Website :-)</strong></span><span style="font-size: 16px;"><br></span><span style='font-size: 16px; color: rgb(255, 0, 0); font-family: "book antiqua", palatino, serif;'><strong>Don&rsquo;t Forget to Bookmark Our Website :-)</strong></span></p>
<p style="text-align: center;"><span style="font-family: georgia, palatino, serif;"><span class="html-attribute-value" style="font-size: 16px;"><?php the_title(); ?> &ndash; Full Movie | FREE DOWNLOAD | TORRENT | HD 1080p | x264 | WEB-DL | DD5.1 | H264 | MP4 | 720p | DVD | Bluray.</span></span></p>
<p><br></p>

<a href="//graizoah.com/afu.php?zoneid=2489978" target="_blank" rel="nofollow noopener"><img class="aligncenter wp-image-39687 size-medium" src="https://o2movies.site/wp-content/uploads/2020/08/130-1300704_click-here-to-download-green-button-click-here-300x102.jpg" alt="Click Here To Download Movie" width="300" height="102" /></a>
<!-- Single Movie Post Data End Here -->
                
                <?php if($images = doo_isset($postmeta, 'imagenes')) { ?>
                <div id="dt_galery" class="galeria">
                	<?php doo_get_images("w300", $images); ?>
                </div>
                <?php } ?>
            </div>
            <?php if($d = doo_isset($postmeta, 'original_title')) { ?>
            <div class="custom_fields">
                <b class="variante"><?php _d('Original title'); ?></b>
                <span class="valor"><?php echo $d; ?></span>
            </div>
            <?php } if($d = doo_isset($postmeta, 'imdbRating')) { ?>
            <div class="custom_fields">
        	    <b class="variante"><?php _d('IMDb Rating'); ?></b>
        	    <span class="valor">
        		    <b id="repimdb"><?php echo '<strong>'.$d.'</strong> '; if($votes = doo_isset($postmeta, 'imdbVotes')) echo sprintf( __d('%s votes'), $votes ); ?></b>
        	        <?php if(current_user_can('administrator')) { ?><a data-id="<?php echo $post->ID; ?>" data-imdb="<?php echo doo_isset($postmeta, 'ids'); ?>" id="update_imdb_rating"><?php _d('Update Rating'); ?></a><?php } ?>
        	    </span>
            </div>
            <?php } if($d = doo_isset($postmeta, 'vote_average')) { ?>
            <div class="custom_fields">
                <b class="variante"><?php _d('TMDb Rating'); ?></b>
                <span class="valor"><?php echo '<strong>'.$d.'</strong> '; if($votes = doo_isset($postmeta, 'vote_count')) echo sprintf( __d('%s votes'), $votes ); ?></span>
            </div>
            <?php } ?>
        </div>

        <!-- Movie Cast -->
        <div id="cast" class="sbox fixidtab">
            <h2><?php _d('Director'); ?></h2>
            <div class="persons">
            	<?php doo_director(doo_isset($postmeta,'dt_dir'), "img", true); ?>
            </div>
            <h2><?php _d('Cast'); ?></h2>
            <div class="persons">
            	<?php doo_cast(doo_isset($postmeta,'dt_cast'), "img", true); ?>
            </div>
        </div>

        <!-- Movie Links -->
        <?php if(DOO_THEME_DOWNLOAD_MOD) get_template_part('inc/parts/single/links'); ?>

        <!-- Movie Social Links -->
        <?php doo_social_sharelink($post->ID); ?>

        <!-- Movie Related content -->
        <?php if(DOO_THEME_RELATED) get_template_part('inc/parts/single/relacionados'); ?>

        <!-- Movie comments -->
        <?php get_template_part('inc/parts/comments'); ?>

        <!-- Movie breadcrumb -->
        <?php doo_breadcrumb( $post->ID, 'movies', __d('Movies'), 'breadcrumb_bottom' ); ?>

    </div>
    <!-- End Post-->
    <?php endwhile; endif; ?>

    <!-- Movie Sidebar -->
    <div class="sidebar scrolling">
    	<?php dynamic_sidebar('sidebar-movies'); ?>
    </div>
    <!-- End Sidebar -->

</div>
<!-- End Single -->
