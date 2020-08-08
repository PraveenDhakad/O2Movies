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

$ads = doo_compose_ad('_dooplay_adhome');
echo ($ads) ? '<div class="module_home_ads">'.$ads.'</div>' : false;
