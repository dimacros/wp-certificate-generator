<?php 
namespace Dimacros;

use function Dimacros\Helpers\assets;

class EnqueueScripts 
{
    public static function call($hook) 
    {
        if( $hook === 'tc_events_page_certificate-manager' ) {
            wp_enqueue_style('certificate-generator', assets('css/style.css'), [], '1.0.0');
            wp_enqueue_script('certificate-generator', assets('js/selectize.min.js'), ['jquery'], '0.12.4', true);
            wp_add_inline_script('certificate-generator', '
                $("#post_id").selectize();
                $("#user_id").selectize();
            ');
        }
    }
}