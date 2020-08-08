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
// Link all data
$psid = $post->ID;
$gtpl = get_the_permalink($psid);
$stus = get_post_status();
$usid = get_the_author_meta('ID');
$prid = wp_get_post_parent_id($psid);
$ptit = get_the_title($prid);
$pprl = get_the_permalink($prid);
$murl = get_post_meta($psid, '_dool_url', true);
$type = get_post_meta($psid, '_dool_type', true);
$lang = get_post_meta($psid, '_dool_lang', true);
$qual = get_post_meta($psid, '_dool_quality', true);
$viws = get_post_meta($psid, 'dt_views_count', true);
$date = human_time_diff(get_the_time('U',$psid), current_time('timestamp',$psid));
$viws = ($viws) ? $viws : '0';
$fico = ($type == __d('Torrent')) ? 'utorrent.com' : doo_compose_domainname($murl);
$domn = ($type == __d('Torrent')) ? 'Torrent' : doo_compose_domainname($murl);
$fico = '<img src="'.DOO_GICO.$fico.'" />';

// Compose View
$out  = "<tr id='{$psid}'>";
$out .= "<td><a href='{$gtpl}' target='_blank'>{$fico} {$domn}</a></td>";
$out .= "<td><a href='{$pprl}' target='_blank'>{$ptit}</a></td>";
$out .= "<td class='views'>{$viws}</td>";
$out .= "<td class='views'>{$lang}</td>";
$out .= "<td class='views'><strong class='quality'>{$qual}</strong></td>";
$out .= "<td class='views'>{$date}</td>";
$out .= "<td class='metas status {$stus}'><i class='icon'></i></td>";
$out .= "<td class='status'>";
$out .= "<a href='#' class='edit_link' data-id='{$psid}'>".__d('Edit')."</a>";
if(current_user_can('administrator')){
    if($stus == 'publish'){
        $out .= " / <a href='#' class='control_link' data-user='{$usid}' data-id='{$psid}' data-status='pending'>".__d('Disable')."</a> / ";
    } else {
        $out .= " / <a href='#' class='control_link' data-user='{$usid}' data-id='{$psid}' data-status='publish'>".__d('Enable')."</a> / ";
    }
    $out .= "<a href='#' class='control_link' data-user='{$usid}' data-id='{$psid}' data-status='trash'>".__d('Delete')."</a>";
}
$out .= "</td></tr>";

// The view
echo $out;
