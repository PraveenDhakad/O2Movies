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

// Register post_type
if(!function_exists('doo_requests')){
    function doo_requests() {
    	$labels = array(
    		'name'                => __d('Requests'),
    		'singular_name'       => __d('Requests'),
    		'menu_name'           => is_admin() ? __d('Requests %%PENDING_COUNT_REQUEST%%') : __d('Requests'),
    		'name_admin_bar'      => __d('Requests'),
    		'all_items'           => __d('Requests'),
    	);
    	$rewrite = array(
    		'slug'                => get_option('dt_requests_slug','requests'),
    		'with_front'          => true,
    		'pages'               => true,
    		'feeds'               => true,
    	);
    	$args = array(
    		'label'               => __d('Requests'),
    		'description'         => __d('Requests manage'),
    		'labels'              => $labels,
    		'supports'            => array('title','thumbnail'),
    		'taxonomies'          => array(),
    		'hierarchical'        => false,
    		'public'              => false,
    		'show_ui'             => true,
    		'show_in_menu'        => true,
    		'menu_position'       => 5,
    		'menu_icon'           => 'dashicons-welcome-add-page',
    		'show_in_admin_bar'   => true,
    		'show_in_nav_menus'   => false,
    		'can_export'          => true,
    		'has_archive'         => true,
    		'exclude_from_search' => true,
    		'publicly_queryable'  => true,
    		'rewrite'             => $rewrite,
    		'capability_type'     => 'post',
    	);
    	register_post_type('requests', $args );
    }
    add_action('init', 'doo_requests', 0 );
}

// Table head
if(!function_exists('requests_table_head')){
	function requests_table_head( $defaults ) {
        $defaults['tmdb']     = __d('TMDb ID');
	    $defaults['type']     = __d('Type');
		$defaults['controls'] = __d('Controls');
	    return $defaults;
	}
	add_filter('manage_requests_posts_columns','requests_table_head');
}

// Table content
if(!function_exists('requests_table_content')){
	function requests_table_content( $column_name, $post_id ) {
        $meta = get_post_meta($post_id,'_dbmv_requests_post', true);
        $tmdb = get_post_meta($post_id,'ids', true);
        $type = doo_isset($meta,'type');
        switch ($column_name) {
            case 'tmdb':
                echo $tmdb ? "<a href='https://www.themoviedb.org/{$type}/{$tmdb}' target='_blank'>{$tmdb}</a>" : false;
                break;

            case 'type':
                echo $type == 'movie' ? __d('Movie') : false;
                echo $type == 'tv' ? __d('TV Show') : false;
                break;

            case 'controls':
                $out  = "<a class='requestscontrol button button-primary' href='".admin_url("admin-ajax.php?action=dbmvrequestcontrol&dc=iad&ids={$tmdb}&type={$type}&ref={$post_id}")."' data-post='{$post_id}'>".__d('Import and delete')."</a> ";
                $out .= "<a class='requestscontrol button' href='".admin_url("admin-ajax.php?action=dbmvrequestcontrol&dc=oim&ids={$tmdb}&type={$type}&ref={$post_id}")."' data-post='{$post_id}'>".__d('Import')."</a> ";
                $out .= "<a class='requestscontrol button' href='".admin_url("admin-ajax.php?action=dbmvrequestcontrol&dc=odl&ref={$post_id}")."' data-post='{$post_id}'>".__d('Delete')."</a>";
                echo "<span id='request_post_{$post_id}'>".$out."</span>";
                break;
        }
    }
    add_action('manage_requests_posts_custom_column','requests_table_content', 10, 2 );
}

// Ajax Action
if(!function_exists('requests_ajax_action')){
    function requests_ajax_action(){
        if(is_user_logged_in() && !current_user_can('subscriber')){
            $ctrl = doo_isset($_GET,'dc');
            $type = doo_isset($_GET,'type');
            $tmdb = doo_isset($_GET,'ids');
            $post = doo_isset($_GET,'ref');
            switch($ctrl) {
                case 'iad':
                    if($type == 'movie'){
                        dbm_post_movie($tmdb);
                    }elseif($type == 'tv'){
                        dbm_post_tv($tmdb);
                    }
                    wp_delete_post($post);
                    break;

                case 'oim':
                    if($type == 'movie'){
                        dbm_post_movie($tmdb);
                    }elseif($type == 'tv'){
                        dbm_post_tv($tmdb);
                    }
                    break;

                case 'odl':
                    wp_delete_post($post);
                    break;
            }
            wp_redirect(esc_url(doo_isset($_SERVER,'HTTP_REFERER')),302); exit;
        }
    }
    add_action('wp_ajax_dbmvrequestcontrol', 'requests_ajax_action');
	add_action('wp_ajax_nopriv_dbmvrequestcontrol', 'requests_ajax_action');
}


// Filter count pending
if(!function_exists('requests_pending_count_filter')){
	function requests_pending_count_filter() {
		add_filter('attribute_escape', 'requests_remove_esc_attr_and_count', 20, 2);
	}
	add_action('auth_redirect', 'requests_pending_count_filter');
}

if(!function_exists('requests_esc_attr_restore')) {
	function requests_esc_attr_restore() {
		remove_filter('attribute_escape', 'requests_remove_esc_attr_and_count', 20, 2);
	}
	add_action('admin_menu','requests_esc_attr_restore');
}

if(!function_exists('requests_remove_esc_attr_and_count')) {
	function requests_remove_esc_attr_and_count( $safe_text = '', $text = '') {
		if( substr_count($text, '%%PENDING_COUNT_REQUEST%%')) {
			$text = trim( str_replace('%%PENDING_COUNT_REQUEST%%', '', $text) );
			remove_filter('attribute_escape', 'remove_esc_attr_and_count', 20, 2);
			$safe_text 	= esc_attr($text);
			$count 		= (int)wp_count_posts('requests','readable')->pending;
			if ( $count > 0 ) {
				$text = esc_attr($text) . '<span class="awaiting-mod count-' . $count . '" style="margin-left:7px;"><span class="pending-count">' . $count . '</span></span>';
				return $text;
			}
		}
		return $safe_text;
	}
}
