<?php


/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class ApcWrapper {

    function add($key, $val) {
        return apc_store($key, $val);
    }

    function fetch($key) {
        return apc_fetch($key);
    }

    function delete($key) {
        return apc_delete($key);
    }

    /*
     * Storing views
     */
    
    function add_view($video_id) {
        $views = Cache::get_views();
        $views[$video_id]++;

        return Cache::add('flingtube_views', $views);
    }

    function get_views() {
        return Cache::fetch('flingtube_views');
    }

    function clear_views() {
        return Cache::delete('flingtube_views');
    }

    /*
     * Storing a/b testing views
     */

    function add_ab_thumbs($video_id, $thumb_id) {
        $ab = Cache::get_ab_thumbs();
        $ab[$video_id][$thumb_id]++;
   
        return Cache::add('flingtube_ab_thumbs', serialize($ab));
    }

    function get_ab_thumbs() {
        return unserialize(Cache::fetch('flingtube_ab_thumbs'));
    }

    function clear_thumbs() {
        return Cache::delete('flingtube_ab_thumbs');
    }
} 
