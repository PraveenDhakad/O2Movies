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

switch(cs_get_option('comments')){

    case 'wp':
        comments_template('', true);
        break;

    case 'fb':

        $fbcolor = cs_get_option('fbscheme','light');
        $fbnumps = cs_get_option('fbnumber','10');
        $postppl = get_the_permalink();
        $out  = "<div id='comments' class='extcom'>";
        $out .= "<div style='width:100%' class='fb-comments' data-href='{$postppl}' data-order-by='social' data-numposts='{$fbnumps}' data-colorscheme='{$fbcolor}' data-width='100%'></div>";
        $out .= "</div>";
        echo $out;
        break;

    case 'dq':
        $sname = cs_get_option('dqshortname');
        $dlink = "<a href='https://help.disqus.com/installation/whats-a-shortname' target='_blank'>".__d('more info')."</a>";

        $out = "<div id='comments' class='extcom'>";
        if($sname){
            $out .= "<div id='disqus_thread'></div>";
        } else{
            $out .= "<p>".sprintf( __d('<strong>Disqus:</strong> add shortname your comunity %s'), $dlink)."</p>";
        }
        $out .= "</div>";

        echo $out
        ;

        break;
}
