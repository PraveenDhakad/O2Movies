<?php
/*
* ----------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* ----------------------------------------------------
*
* @since 2.1.4
*
*/

/* Register Metabox TVShows
========================================================
*/
if( ! function_exists( 'dt_metabox_tvshows' ) ) {
	function dt_metabox_tvshows() {
		add_meta_box('dt_metabox', __d('TVShows Info'),'dt_metabox_tvshows_display','tvshows','normal','high');
	}
	add_action('add_meta_boxes', 'dt_metabox_tvshows');
}

/* Display Metabox TVShows
========================================================
*/
if( ! function_exists( 'dt_metabox_tvshows_display' ) ) {

	function dt_metabox_tvshows_display( $post) {
	    // Nonce security
	    wp_nonce_field('_tvshows_nonce', 'tvshows_nonce');

		// Metabox options
	    $options = array(

	        array(
	            'id'          => 'ids',
	            'type'        => 'generator',
	            'style'       => 'style="background: #f7f7f7"',
	            'class'       => 'regular-text',
	            'placeholder' => '1402',
	            'label'       => __d('Generate data'),
	            'desc'        => __d('Generate data from <strong>themoviedb.org</strong>'),
	            'fdesc'       => __d('E.g. https://www.themoviedb.org/tv/<strong>1402</strong>-the-walking-dead')
	        ),

	        // Field ( clgnrt )
	        array(
	            'id'     => 'clgnrt',
	            'type'   => 'checkbox',
	            'label'  => __d('Seasons control'),
	            'clabel' => __d('I have generated seasons or I will manually')
	        ),

	        // Field ( dt_featured_post )
	        array(
	            'id'     => 'dt_featured_post',
	            'type'   => 'checkbox',
	            'label'  => __d('Featured Title'),
	            'clabel' => __d('Do you want to mark this title as a featured item?')
	        ),

	        // Heading ==============================
	        array(
	            'type'    => 'heading',
	            'colspan' => 2,
	            'text'    => __d('Images and trailer')
	        ),

	        // Field ( dt_poster )
	        array(
	            'id'    => 'dt_poster',
	            'type'  => 'upload',
	            'label' => __d('Poster'),
	            'desc'  => __d('Add url image'),
	            'aid'   => 'up_images_poster',
	            'ajax'  => false
	        ),

	        // Field ( dt_backdrop )
	        array(
	            'id'      => 'dt_backdrop',
	            'type'    => 'upload',
	            'label'   => __d('Main Backdrop'),
	            'desc'    => __d('Add url image'),
	            'aid'     => 'up_images_backdrop',
	            'prelink' => 'https://image.tmdb.org/t/p/w500',
	            'ajax'    => true
	        ),

	        // Field ( imagenes )
	        array(
	            'id'     => 'imagenes',
	            'type'   => 'textarea',
	            'rows'   => 5,
	            'upload' => true,
	            'aid'    => 'up_images_images',
	            'label'  => __d('Backdrops'),
	            'desc'   => __d('Place each image url below another')
	        ),

	        // Field ( youtube_id )
	        array(
	            'id'    => 'youtube_id',
	            'type'  => 'text',
	            'class' => 'small-text',
	            'label' => __d('Video trailer'),
	            'desc'  => __d('Add id Youtube video'),
	            'fdesc' => '[id_video_youtube]'
	        ),

	        // Heading ==============================
	        array(
	            'type'    => 'heading',
	            'colspan' => 2,
	            'text'    => __d('More data')
	        ),

	        // Field ( original_name )
	        array(
	            'id'    => 'original_name',
	            'type'  => 'text',
	            'class' => 'small-text',
	            'label' => __d('Original Name')
	        ),

	        // Field ( release_date )
	        array(
	            'id'    => 'first_air_date',
	            'type'  => 'date',
	            'label' => __d('Firt air date')
	        ),

	        // Field ( release_date )
	        array(
	            'id'    => 'last_air_date',
	            'type'  => 'date',
	            'label' => __d('Last air date')
	        ),

	        // Field ( number_of_seasons / number_of_episodes )
	        array(
	            'double' => true,
	            'id'     => 'number_of_seasons',
	            'id2'    => 'number_of_episodes',
	            'type'   => 'text',
	            'label'  => __d('Content total posted'),
	            'desc'   => __d('Seasons / Episodes')
	        ),

	        // Field ( imdbRating / imdbVotes )
	        array(
	            'double' => true,
	            'id'     => 'imdbRating',
	            'id2'    => 'imdbVotes',
	            'type'   => 'text',
	            'label'  => __d('Rating TMDb'),
	            'desc'   => __d('Average / votes')
	        ),

	        // Field ( episode_run_time )
	        array(
	            'id'    => 'episode_run_time',
	            'type'  => 'text',
	            'class' => 'small-text',
	            'label' => __d('Episode runtime')
	        ),

	        // Field ( dt_cast )
	        array(
	            'id' => 'dt_cast',
	            'type' => 'textarea',
	            'rows' => 5,
	            'label' => __d('Cast')
	        ),

	        // Field ( dt_creator )
	        array(
	            'id'    => 'dt_creator',
	            'type'  => 'text',
	            'label' => __d('Creator')
	        )
	    );

		// Start HTML
	    echo '<div id="loading_api"></div>';
	    echo '<div id="api_table"><table class="options-table-responsive dt-options-table"><tbody>';

	    	new Doofields($options);

	    echo '</tbody></table></div>';
	    // End HTML
	}
}

/* Save Metabox TVShows @since 2.1.3
========================================================
*/
if(!function_exists('dt_metabox_tvshows_save')){
	function dt_metabox_tvshows_save($post_id) {

        // Cache class
        $cache = new DooPlayCache;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if (!isset( $_POST['tvshows_nonce'] ) || ! wp_verify_nonce( $_POST['tvshows_nonce'], '_tvshows_nonce') ) return;
		if (!current_user_can('edit_post', $post_id ) ) return;

		// Array Post-meta
		$update_post_meta = array(
			'ids'					=> 'text',
			'dt_poster'				=> 'text',
			'dt_backdrop'			=> 'text',
			'imagenes'				=> 'textarea',
			'youtube_id'			=> 'text',
			'number_of_episodes'	=> 'text',
			'number_of_seasons'		=> 'text',
			'original_name'			=> 'text',
			'imdbRating'			=> 'text',
			'imdbVotes'				=> 'text',
			'episode_run_time'		=> 'text',
			'first_air_date'		=> 'date',
			'last_air_date'			=> 'date',
			'dt_cast'				=> 'textarea',
			'dt_creator'			=> 'text',
			'clgnrt'				=> 'checkbox',
			'dt_featured_post'		=> 'checkbox',
		);

		// Generate POST @since 2.1.3
		foreach ($update_post_meta as $key => $type) {
			if ( isset( $_POST[$key] ) ) update_post_meta( $post_id, $key, esc_attr( $_POST[$key] ) ); else delete_post_meta( $post_id, $key );
		}

		// Insert Poster @since 2.1.3
		$posterurl = isset($_POST['dt_poster']) ? 'https://image.tmdb.org/t/p/w500'. $_POST['dt_poster'] : null;
		if ( $posterurl != null AND has_post_thumbnail() == false ) dt_dbmovies_upload_image( $posterurl, $post_id, true, false );

		// Insert genres @since 2.1.3
		dt_dbmovies_insert_genres($post_id, 'tv');
        // Delete cache @since 2.1.4
        $cache->delete($post_id.'_postmeta');
	}
	add_action('save_post', 'dt_metabox_tvshows_save');
}

if( ! function_exists( 'custom_admin_tvshows_js' ) ) {
	function custom_admin_tvshows_js() {
	global $post_type; if( $post_type == 'tvshows') {	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".dtload").click(function() {
				var o = $(this).attr("id");
				1 == o ? (
						$(".dtloadpage").hide(),
						$(this).attr("id", "0")
					) : (
						$(".dtloadpage").show(),
						$(this).attr("id", "1")
					)
			}),
			$(".dtloadpage").mouseup(function() { return !1 }),
			$(".dtload").mouseup(function() { return !1 }),
			$(document).mouseup(function() {
				$(".dtloadpage").hide(),
				$(".dtload").attr("id", "")
			})
		})
	</script>
	<div class="dtloadpage">
		<div class="dtloadbox">
			<img src="<?php echo get_template_directory_uri().'/assets/img/'; ?>admin_load.gif">
			<span><?php _d('Generating seasons'); ?></span>
			<p><?php _d('not close this page to complete the upload'); ?></p>
		</div>
	</div>
	<?php
	  } }
	add_action('admin_footer', 'custom_admin_tvshows_js');
}
