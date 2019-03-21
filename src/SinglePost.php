<?php 
namespace Dimacros;

use Dimacros\Helpers;
use Dimacros\Models\Attendant;

class SinglePost 
{

    public static function authCheck() {
     
        switch (get_post_type()) {
            case 'certificate':
                $redirectTo = get_permalink();
                Helpers\redirect_unless( is_user_logged_in(), wp_login_url($redirectTo) );
                break;
            case 'event_resource': 
                $redirectTo = is_post_type_archive() ? get_post_type_archive_link('event_resource') : get_permalink();
                Helpers\redirect_unless( is_user_logged_in(), wp_login_url($redirectTo) );
                break;
            default:
                # code...
                break;
        }

    }

    public static function template($filename) {
    
        switch (get_post_type()) {
            case 'certificate':
                $user = wp_get_current_user();
                $filename = VIEW_DIR . '/single-certificate.php';

                return in_array('administrator', $user->roles) ? $filename : get_404_template(); 
            
            case 'event_resource': 
                $user_id = get_current_user_id();
                $event_id = get_post_meta(get_the_ID(), 'event_id', true);
                $attendant = Attendant::findByUser($user_id);
                
                if( !is_null($attendant) && $attendant->details['event_id'] === $event_id ) {
                    return $filename;
                }
                
                return get_404_template();

            default:
                return $filename;
        }
        
    }
}