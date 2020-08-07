<?php if(!defined('ABSPATH')) exit;
/**
 * @package Dbmovies Plugin WordPress
 * @author Doothemes (Erick Meza & Brendha Mayuri)
 * @since 1.0
 */

class DooGdrive{

    private $drive;
    private $extent;
    private $dfile;
    private $path;
    private $time;


    public function __construct(){
        $this->drive  = 'https://drive.google.com/uc?export=download&id=';
        $this->extent = '.cache';
        $this->path   = WP_CONTENT_DIR.'/cache/dooplay/';
        $this->time   = 900;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    public function get_data($driveID){
        if($cache = $this->get_cache($driveID))
            return $cache;
        else
            $data = $this->getlink($driveID);
            $this->set_cache($driveID, $data);
            return $data;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function getlink($driveID){
        $link = $this->drive.$driveID;
    	$ch   = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $link);
    	curl_setopt($ch, CURLOPT_HEADER, TRUE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_COOKIEJAR, DOO_DIR.'/assets/google.txt');
    	curl_setopt($ch, CURLOPT_COOKIEFILE,DOO_DIR.'/assets/google.txt');
    	$page = curl_exec($ch);
    	$get = $this->locheader($page);
        if(!$get){
    		$html    = $this->str_get_html($page);
    		$link    = urldecode(trim($html->find('a[id=uc-download-link]',0)->href));
    		$tmp     = explode("confirm=",$link);
    		$tmp2    = explode("&",doo_isset($tmp,1));
    		$confirm = doo_isset($tmp2,0);
    		$linkdowngoc = $this->drive."{$driveID}&confirm={$confirm}";
    		curl_setopt($ch, CURLOPT_URL, $linkdowngoc);
    		curl_setopt($ch, CURLOPT_HEADER, TRUE);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_COOKIEJAR, DOO_DIR.'/assets/google.txt');
    		curl_setopt($ch, CURLOPT_COOKIEFILE,DOO_DIR.'/assets/google.txt');
    		$page = curl_exec($ch);
    		$get  = $this->locheader($page);
    	}
    	curl_close($ch);
    	return $get;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function locheader($page){
    	$temp = explode("\r\n", $page);
    	foreach ($temp as $item) {
    		$temp2 = explode(": ", $item);
    		$infoheader[doo_isset($temp2,0)] = doo_isset($temp2,1);
    	}
    	$location = doo_isset($infoheader,'Location');
    	return $location;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function str_get_html($str, $lowercase=true) {
        $dom = new DooSimpleDom;
        $dom->load($str, $lowercase);
        return $dom;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function str_get_dom($str, $lowercase=true) {
        $dom = new DooSimpleDom;
        $dom->load($str, $lowercase);
        return $dom;
    }

    /**
     * @since 1.0
     * @version 1.0
     */
    private function get_cache($id){
        if($this->is_cached($id)){
            return file_get_contents($this->path.$this->safe_name($id).$this->extent);
        }
    }


    /**
     * @since 1.0
     * @version 1.0
     */
    private function set_cache($id, $data){
        file_put_contents($this->path.$this->safe_name($id).$this->extent,$data);
    }


    /**
     * @since 1.0
     * @version 1.0
     */
    private function is_cached($id){
        $file = $this->path.$this->safe_name($id).$this->extent;
        if(file_exists($file) && (filemtime($file) + $this->time >= time()))
            return true;
        else
            return false;
    }


    /**
     * @since 1.0
     * @version 1.0
     */
    private function safe_name($id){
        return md5('gd'.$id);
    }

}
