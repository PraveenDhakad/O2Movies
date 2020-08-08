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

if( isset( $_GET[ 's' ] ) ) {  
	$s = $_GET[ 's' ];  
	} else {
		$s = '';
	}
?>
<ul class="abc">
	<li><a href="<?php echo esc_url( home_url('/') ); ?>?letter=true&s=title-09" <?php echo $s == 'title-09' ? 'class="select"' : ''; ?>>#</a></li>
	<?php for ($l="a";$l!="aa";$l++){?>
	<li><a href="<?php echo esc_url( home_url('/') ); ?>?letter=true&s=title-<?php echo $l; ?>" <?php echo $s == "title-$l" ? 'class="select"' : ''; ?>><?php echo strtoupper($l); ?></a></li> 
	<?php } ?>
</ul>