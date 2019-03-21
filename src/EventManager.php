<?php 
namespace Dimacros;

use WPEloquent\Model\Post;

class EventManager extends Post
{
    public static function registerPostStatus() {

        register_post_status('completed', [
            'label'                     => 'Finalizado',
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 
                'Finalizado <span class="count">(%s)</span>', 
                'Finalizados <span class="count">(%s)</span>' 
            )
        ]);
        
    }

}