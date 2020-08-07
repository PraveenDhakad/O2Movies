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

/* Register Metabox Seasons
========================================================
*/
if( ! function_exists( 'dt_metabox_seasons' ) ) {
	function dt_metabox_seasons() {
		add_meta_box('dt_metabox', __d('seasons Info'), 'dt_metabox_seasons_display','seasons','normal','high');
	}
	add_action('add_meta_boxes', 'dt_metabox_seasons');
}

/* Display Metabox TVShows
========================================================
*/
if( ! function_exists( 'dt_metabox_seasons_display' ) ) {
	function dt_metabox_seasons_display( $post) {

	    // Nonce security
	    wp_nonce_field('_seasons_nonce', 'seasons_nonce');

	    // Metabox options
	    $options = array(

	        array(
	            'id'           => 'ids',
	            'id2'          => 'temporada',
	            'type'         => 'generator',
	            'style'        => 'style="background: #f7f7f7"',
	            'class'        => 'extra-small-text',
	            'placeholder'  => '1402',
	            'placeholder2' => '1',
	            'label'        => __d('Generate data'),
	            'desc'         => __d('Generate data from <strong>themoviedb.org</strong>'),
	            'fdesc'        => __d('E.g. https://www.themoviedb.org/tv/<strong>1402</strong>-the-walking-dead/season/<strong>1</strong>/')
	        ),

	        // Field ( clgnrt )
	        array(
	            'id'     => 'clgnrt',
	            'type'   => 'checkbox',
	            'label'  => __d('Episodes control'),
	            'clabel' => __d('I generated episodes or add manually')
	        ),

	        // Field ( serie )
	        array(
	            'id'    => 'serie',
	            'type'  => 'text',
	            'class' => 'regular-text',
	            'label' => __d('Serie name')
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

	        // Field ( air_date )
	        array(
	            'id'    => 'air_date',
	            'type'  => 'date',
	            'label' => __d('Air date')
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

/* Save Metabox TVShows > Seasons @since 2.1.3
========================================================
*/
if( ! function_exists( 'dt_metabox_seasons_save' ) ) {
	function dt_metabox_seasons_save( $post_id ) {

        // Cache class
        $cache = new DooPlayCache;

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['seasons_nonce'] ) || ! wp_verify_nonce( $_POST['seasons_nonce'], '_seasons_nonce') ) return;
		if ( ! current_user_can('edit_post', $post_id ) ) return;

		// Array Post-meta
		$update_post_meta = array(
			'ids'		=> 'text',
			'temporada' => 'text',
			'dt_poster' => 'text',
			'serie'		=> 'text',
			'air_date'	=> 'text',
			'clgnrt'	=> 'checkbox',
		);

		// Generate POST @since 2.1.3
		foreach ($update_post_meta as $key => $type) {
			if ( isset( $_POST[$key] ) ) update_post_meta( $post_id, $key, esc_attr( $_POST[$key] ) ); else delete_post_meta( $post_id, $key );
		}

		// Insert Poster @since 2.1.3
		$posterurl = isset( $_POST['dt_poster'] ) ? 'https://image.tmdb.org/t/p/w500'. $_POST['dt_poster'] : null;
		if($posterurl != null AND has_post_thumbnail() == false ) dt_dbmovies_upload_image( $posterurl, $post_id, true, false );
        // Delete cache @since 2.1.4
        $cache->delete($post_id.'_postmeta');
	}
	add_action('save_post', 'dt_metabox_seasons_save');
}

if( ! function_exists( 'custom_admin_js_seasons' ) ) {
	function custom_admin_js_seasons() {
	global $post_type; if( $post_type == 'seasons') {	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	    jQuery(".dtload").click(function() {
	        var o = jQuery(this).attr("id");
	        1 == o ? (jQuery(".dtloadpage").hide(), jQuery(this).attr("id", "0")) : (jQuery(".dtloadpage").show(), jQuery(this).attr("id", "1"))
	    }), jQuery(".dtloadpage").mouseup(function() {
	        return !1
	    }), jQuery(".dtload").mouseup(function() {
	        return !1
	    }), jQuery(document).mouseup(function() {
	        jQuery(".dtloadpage").hide(), jQuery(".dtload").attr("id", "")
	    })
	})
	</script>
	<div class="dtloadpage">
		<div class="dtloadbox">
			<img src="<?php echo get_template_directory_uri().'/assets/img/'; ?>admin_load.gif">
			<span><?php _d('Generating episodes'); ?></span>
			<p><?php _d('not close this page to complete the upload'); ?></p>
		</div>
	</div>

	<?php
	  }
	}
	add_action('admin_footer', 'custom_admin_js_seasons');
}
