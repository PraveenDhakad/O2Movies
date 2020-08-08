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

// Libraries and dynamic data
$google = new DooGdrive;
$source = urldecode(doo_isset($_GET,'source'));
$typeso = doo_isset($_GET,'type');
$postid = doo_isset($_GET,'id');
$images = get_post_meta($postid,'imagenes', true);
$jwpkey = cs_get_option('jwkey','IMtAJf5X9E17C1gol8B45QJL5vWOCxYUDyznpA==');
$jwplal = cs_get_option('jwlibrary','https://content.jwplatform.com/libraries/oqxCz8Dy.js');
$versio = cs_get_option('jwversion','jw8');

// Compose data for Json
$data = array(
    'file'  => ($typeso == 'gdrive') ? $google->get_data(doo_define_gdrive($source)) : $source,
    'image' => esc_url(doo_rand_images($images, 'original', true, true)),
    'link'  => esc_url(home_url()),
    'logo'  => doo_compose_image_option('jwlogo'),
    'auto'  => doo_is_true('playauto','jwp') ? 'true' : 'false',
    'text'  => cs_get_option('jwabout','DooPlay Theme WordPress'),
    'color' => cs_get_option('jwcolor','#0b7ef4'),
    'lposi' => cs_get_option('jwposition','top-right'),
    'flash' =>  DOO_URI.'/assets/jwplayer/jwplayer.flash.swf'
);

// Render JW Player
require_once(DOO_DIR.'/pages/sections/'.$versio.'.php');
