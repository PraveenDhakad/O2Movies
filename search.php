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

get_header();
doo_glossary();
?>
<div class="module">
	<div class="content csearch">
	<?php
    if(doo_isset($_GET,'letter') == 'true') {
		get_template_part('pages/letter');
	} else {
		get_template_part('pages/search');
	}
    doo_pagination();
    ?>
	</div>
	<?php get_template_part('inc/parts/sidebar'); ?>
</div>
<?php get_footer(); ?>
