<?php
/*
* -------------------------------------------------------------------------------------
*
* DBmovies ( HELP Tabs ) for DooPlay
*
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.4
*
*/

/* HELP TABs
========================================================
*/
if( ! function_exists( 'dbmovies_help_tap' ) ) {
    function dbmovies_help_tap () {

    	// Elements
        $license		= get_current_screen();
    	$filter			= get_current_screen();
    	$search			= get_current_screen();
    	$keyboard		= get_current_screen();
    	$error			= get_current_screen();
    	$limits			= get_current_screen();

    	// Help tabs
        $license->add_help_tab( array(
            'id'		=> 'license',
            'title'		=> __d('Dbmovies license'),
            'content'	=> '<h2>'. __d('Dbmovies for DooPlay').'</h2>
    					<p>'. __d('It is important to have a valid product license, keep the file updated and access exclusive content.') .'</p>
    					<ul>
    					<li>'. __d('Access to all updates.') .'</li>
    					<li>'. __d('Access to dedicated support.') .'</li>
    					<li>'. __d('Access to all dbmovies tools.') .'</li>
    					<li>'. __d('Compatibility with our themes.') .'</li>
    					<li>'. __d('Security Guaranteed.') .'</li>
    					</ul>',
        ) );

    	$filter->add_help_tab( array(
            'id'		=> 'filter',
            'title'		=> __d('Filtering Content'),
            'content'	=> '<p>' . __d( 'You can filter content by year of release, if the field where text of the year is empty, will give you results with the most popular content, you can also add the filter by genres and sort by popularity order.' ) . '</p>',
        ) );

    	$search->add_help_tab( array(
            'id'		=> 'search',
            'title'		=> __d('Searching content'),
            'content'	=> '<p>' . __d( 'In the search field, you can add a specific title that you want to find, you will find results with the names of actors or directors, it is limited to search titles of movies or TV series.' ) . '</p>',
        ) );

    	$keyboard->add_help_tab( array(
            'id'		=> 'keyboard',
            'title'		=> __d('Keyboard Shortcuts'),
            'content'	=> '<p>' . __d( 'You can use some shortcuts with the keyboard to make the job easier.' ) . '</p>
    		<ul>
    		<li><span class="keyboard">Alt</span> '. __d('Access plugin settings.') .'</li>
    		<li><span class="keyboard">Esc</span> '. __d('It frees the screen of any popup window.') .'</li>
    		<li><span class="dashicons dashicons-arrow-right-alt keyboard"></span> '. __d('Go to next page in the results') .'</li>
    		<li><span class="dashicons dashicons-arrow-left-alt keyboard"></span> '. __d('Return to previous page in the results.') .'</li>
    		</ul>
    		',
        ) );

    	$error->add_help_tab( array(
            'id'		=> 'error',
            'title'		=> __d('Error codes'),
            'content'	=> '<p>
    		<lu>
    		<li><span class="keyboard">403</span> '. __d('Access denied.') .'</li>
    		<li><span class="keyboard">404</span> '. __d('API key not found.') .'</li>
    		<li><span class="keyboard">500</span> '. __d('Internal server error.') .'</li>
    		<li><span class="keyboard">201</span> '. __d('Application in maintenance.') .'</li>
    		<li><span class="keyboard">202</span> '. __d('Exceeded request limit.') .'</li>
    		</ul></p>
    		',
        ) );

    	$limits->add_help_tab( array(
            'id'		=> 'limits',
            'title'		=> __d('Request limits'),
            'content'	=> '<p>'.__d('We recommend that you do not exceed the established server request limits.').'</p>
    		<p>
    		<lu>
    		<li><span class="keyboard">1</span> '. __d('domain per license key.') .'</li>
    		<li><span class="keyboard">10</span> '. __d('requests per second.') .'</li>
    		<li><span class="keyboard">600</span> '. __d('requests per minute.') .'</li>
    		<li><span class="keyboard">36000</span> '. __d('requests per hour.') .'</li>
    		<li><span class="keyboard">864000</span> '. __d('requests per day.') .'</li>
    		</ul></p>
    		<p>'.__d('<strong>NOTE:</strong> Do not share your license key, remember that it can only be used for your domain.').'</p>
    		',
        ) );
    }
}
