<?php

namespace Dimacros\Helpers;

use Exception; 
use WP_Query;


function assets($name) {
    return plugins_url('resources/assets/' . $name, __FILE__);
}

function optional( $value ) 
{
    return is_null($value) ? (new class { 
        function __get($name) { return NULL; }
    }) : $value;
}

function redirect_if($condition, $url) 
{    
    if( $condition ) {
        wp_redirect( $url ); 
        exit;        
    } 
}

function redirect_unless($condition, $url) 
{
    return redirect_if( !$condition, $url );
}

function delete_file_if($condition, $file) 
{
    if( $condition ) {
        unlink($file);
        return true;
    }     

    return false;
}

function view($name, array $data = NULL) 
{
    ob_start();
        extract($data);
        include(VIEW_DIR . '/' . $name . '.php');
        $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

function get_post_by_meta(array $args, $post_type = NULL)
{
    if( is_null($post_type) ) {
        throw new Exception('Argument 2 is required. Write a post_type');
    }

    $query = new WP_Query([
        'post_type' => $post_type,
        'meta_query' => [ $args ],
        'posts_per_page' => 1
    ]);
    
    if( empty($query->posts) ) {
        throw new Exception("El WP_Post de {$post_type} no fue encontrado");
    }

    return $query->posts[0];
}

function get_certificate_template_by_event($event_id)
{
    return get_post_by_meta(['key' => 'event_id','value' => $event_id], 'certificate');
}