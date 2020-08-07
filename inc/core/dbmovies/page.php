<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.4
*
*/

// Options
global $dbmvsoptions;

// Class
$dbmvclass	= new Dbmovies_for_dooplay_class();

// Plugin / Theme options
$popt01	= get_option('dooplay_license_key_status');
$popt02	= 'checked="checked"';
$popt03	= $dbmvsoptions['active'];
$popt04	= $dbmvsoptions['dbmv'];
$popt05	= $dbmvsoptions['tmdb'];
$popt06	= $dbmvsoptions['lang'];
$popt07	= $dbmvsoptions['upload'];
$popt08	= $dbmvsoptions['genres'];
$popt09	= $dbmvsoptions['release'];
$popt10	= ( $popt04 ) ? 'keyva' : 'red';
$popt11	= ( $popt05 ) ? 'cpltd' : 'red';
$popt12 = rand('1980', date('Y')+1 );

// End PHP

?>

<div class="wrap dbmovies" id="dbmovies-page">
	<header>
		<h1 class="title"><?php _d('Dbmovies for DooPlay'); ?></h1>
		<a class="page-title-action settings" id="show_dbmovies_settings"><?php _d('Settings'); ?></a>
				<a href="https://streamal.me" class="page-title-action settings">
                    <span class='online flashit'>Dbmovies is ON</span>
</a> 		
	</header>
	<div id="dbmovies_settings" class="settbox jump">
		<form id="dbmovies-save" data-action="settings">
			<fieldset class="pro perico">
				<span for="dbmv"><?php if($popt01 == 'valid' AND $popt10 == 'red') { echo '<a href="https://streamal.me" class="activate_dbmovies">License key</a>'; } ?></span>
				<input type="text" id="dbmv" name="dbmv" placeholder="<?php _d('Dbmovies API key'); ?>" value="streamal.me" class="<?php echo $popt10; ?>">

			</fieldset>
			<fieldset class="check perico">
					<article>
						<input type="checkbox" id="active" name="active" <?php echo ($popt03 == true) ? $popt02 : ''; ?>>
						<label for="active"><?php _d('Do you want to activate import tool?'); ?></label>
					</article>
			</fieldset>
			<fieldset class="setline perico">
				<label for="tmdb"><?php _d('Themoviedb API key'); ?></label>
				<input type="text" id="tmdb" name="tmdb" placeholder="<?php _d('API Key'); ?>" value="<?php echo $popt05; ?>" class="<?php echo $popt11; ?>">
			</fieldset>
			<fieldset class="setline perico">
				<label for="lang"><?php _d('Languague'); ?></label>
				<select id="lang" name="lang">
					<?php foreach ( $dbmvclass->languages() as $k => $v) { ?>
						<option value="<?php echo $k; ?>" <?php selected( $popt06, $k ); ?>><?php echo $v; ?></option>
					<?php } ?>
				</select>
			</fieldset>
			<fieldset class="check perico">
					<article>
						<input type="checkbox" id="upload" name="upload" <?php echo ($popt07 == true) ? $popt02 : ''; ?>>
						<label for="upload"><?php _d('Upload poster image to server?'); ?></label>
					</article>
					<article>
						<input type="checkbox" id="genres" name="genres" <?php echo ($popt08 == true) ? $popt02 : ''; ?>>
						<label for="genres"><?php _d('Do you want to autocomplete genres?'); ?></label>
					</article>
					<article>
						<input type="checkbox" id="release" name="release" <?php echo ($popt09 == true) ? $popt02 : ''; ?>>
						<label for="release"><?php _d('Publish content with the release date?'); ?></label>
					</article>
			</fieldset>
			<fieldset>
				<input id="save_sdbmvs" class="button button-primary" type="submit" value="<?php _d('Save'); ?>">
				<a id="hidde_dbmovies_settings" class="button button-secundary"><?php _d('Close'); ?></a>
				<input type="hidden" name="action" value="dt_dbmovies_save_options">
				<?php wp_nonce_field('dbmovies-save-options','dbmovies-save-options-nonce') ?>
			</fieldset>
		</form>
	</div>
	<div class="dbmovies_sett_modal"></div>
	<nav class="dbmovies_menu">
		<h2 class="nav-tab-wrapper">
			<a data-type="movie" class="nav-tab resetfil nav-tab-active"><?php _d('Movies'); ?></a>
			<a data-type="tv" class="resetfil nav-tab"><?php _d('TV Shows'); ?></a>
		</h2>
	</nav>
	<div class="toolbar dual">
		<form id="dbmovies-filter" data-action="filter">
			<div class="item">
				<input class="resetfil year" type="number" id="year" name="year" placeholder="<?php _d('Year'); ?>" min="1900" max="<?php echo date('Y')+1; ?>" value="<?php echo $popt12; ?>">
			</div>
			<div class="item">
				<select class="resetfil" id="order" name="order">
					<option value="popularity.desc"><?php _d('Popularity desc'); ?></option>
					<option value="popularity.asc"><?php _d('Popularity asc'); ?></option>
				</select>
			</div>
			<div id="gn_movie" class="item genres current_genres">
				<select class="resetfil" id="genre_movie" name="genre_movie">
					<?php foreach ( $dbmvclass->genres_movie() as $k => $v) { echo '<option value="'.$k.'">'. $v .'</option>'; } ?>
				</select>
			</div>
			<div id="gn_tv" class="item genres">
				<select class="resetfil" id="genre_tv" name="genre_tv">
					<?php foreach ( $dbmvclass->genres_tv() as $k => $v) { echo '<option value="'.$k.'">'. $v .'</option>'; } ?>
				</select>
			</div>
			<div class="item">
				<input style="width:76px;" type="number" id="page" name="page" placeholder="<?php _d('Page'); ?>" value="1">
			</div>
			<div class="item">
				<input type="hidden" name="action" value="dt_dbmovies_app_filter_content">
				<input type="hidden" name="type" value="movie" class="cctype">
				<?php wp_nonce_field('dbmovies-app-filter','dbmovies-app-filter-nonce') ?>
				<input id="dbmfilter" class="button button-secundary" type="submit" value="<?php _d('Filter'); ?>">
			</div>
			<div class="item">
				<input id="import_all" data-next="1" class="button button-primary" type="button" value="<?php _d('Bulk import'); ?>" style="display:none">
			</div>
		</form>
		<form id="dbmovies-search" data-action="search">
			<div class="item right">
				<input class="search resetfil" type="text" id="term" name="term" placeholder="<?php _d('Search...'); ?>">
				<input id="dbmsearch" class="button button-secundary" type="submit" value="<?php _d('Search'); ?>">
				<input type="hidden" id="saction" name="action" value="dt_dbmovies_app_search_content">
				<input type="hidden" id="stype" name="type" value="movie" class="cctype">
				<input type="hidden" id="spage" name="page" value="1">
				<input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce('dbmovies-app-search'); ?>">
			</div>
		</form>
	</div>
	<div id="dbmvpage">
		<div id="dbmovies_response_history" class="history fadeInDown" style="display:none">
			<div class="box">
				<ul>
					<div id="welcm"></div>
				</ul>
			</div>
		</div>
		<div id="dbmovies_response">
			<p class="no-data"><?php _d('Get content now'); ?></p>
		</div>
	</div>
</div>
