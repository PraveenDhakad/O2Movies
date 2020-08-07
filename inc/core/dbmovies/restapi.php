<?php
/*
* -------------------------------------------------------------------------------------
*
* DBmovies Rest API
*
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.3
*
*/


/* Request WP API
========================================================
*/
if( ! function_exists( 'dt_dbmovies_wpapi' ) ) {
	function dt_dbmovies_wpapi() {

		// Get Dbmovies Options
		global $dbmvsoptions, $wp_version;

		// Main Data
		$opt		= $dbmvsoptions;
		$subact		= isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;
		$dbmapi		= isset( $_REQUEST['key'] ) ? $_REQUEST['key'] : null;
		$doolicen	= get_option( DOO_THEME_SLUG. '_license_key');
		$doostatu	= get_option( DOO_THEME_SLUG. '_license_key_status');

		// Main conditional
		if( $dbmapi == $opt['dbmv'] ) {

			if($subact == 'reset') {

				// Reset Data
				delete_option('dbmovies_options');

				// Rest API ( Full reset )
				$rest = array('status'=> 200, 'message'=> 'full_reset');
			} else {

				// Rest API ( vip access )
				$rest = array(

					// Generated access
					'status'	=> 200,
					'message'	=> 'vip_access',
					'email'		=> get_option('admin_email'),

					// Theme Information
					'theme'			=> array(
						'name'		=> DOO_THEME,
						'version'	=> DOO_VERSION,
						'dbversion' => DOO_VERSION_DB
					),

					// Doothemes Access
					'doothemes'	=> array(
						'key'		=> $doolicen,
						'status'	=> $doostatu
					),

					// Dbmovies Settings
					'dbmovies'	=> array(
						'active'	=> $opt['active'],
						'dbmv'		=> $opt['dbmv'],
						'tmdb'		=> $opt['tmdb'],
						'lang'		=> $opt['lang'],
						'upload'	=> $opt['upload'],
						'genres'	=> $opt['genres'],
						'release'	=> $opt['release']
					),

					// WP Host information
					'wphost'	=> $_SERVER['SERVER_ADDR'],
					'wpversion'	=> $wp_version
				);
			}
		} else {

			// Rest API ( No access )
			$rest = array('status' => 403, 'message'=> 'no_access');
		}
		return $rest;
	}
}

/* Register WP API for dbmovies
========================================================
*/
if( ! function_exists( 'dt_dbmovies_rest_api' ) ) {
	function dt_dbmovies_rest_api() {

		// Parameters
		$data = array(
			'methods'     =>'GET',
			'callback'    =>'dt_dbmovies_wpapi'
		);

		// Register route
		register_rest_route('dbmovies', 'rest', $data );
	}
	add_action('rest_api_init', 'dt_dbmovies_rest_api');
}
