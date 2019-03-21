<?php 

namespace Dimacros;

use Dimacros\Models\Ticket;
use Dimacros\Site;

class EventResource
{
    public static function registerPostType() {

        /**
         * Post Type: Materiales de Evento.
         */

        $labels = array(
            "name" => __( "Materiales de Evento", "dimacros" ),
            "singular_name" => __( "Material de evento", "dimacros" ),
            "menu_name" => __( "Materiales de Evento", "dimacros" ),
            "all_items" => __( "Materiales de Evento", "dimacros" ),
            "add_new" => __( "Agregar nuevo", "dimacros" ),
            "add_new_item" => __( "Agregar nuevo Material de evento", "dimacros" ),
            "edit_item" => __( "Editar Material de evento", "dimacros" ),
            "new_item" => __( "Nuevo Material de evento", "dimacros" ),
            "view_item" => __( "Ver Material de evento", "dimacros" ),
            "view_items" => __( "Ver Materiales de Evento", "dimacros" ),
            "search_items" => __( "Buscar Material de evento", "dimacros" ),
            "not_found" => __( "No se encontraron Materiales de Evento", "dimacros" ),
            "not_found_in_trash" => __( "No se encontraron Materiales de Evento en la papelera", "dimacros" ),
        );

        $args = array(
            "label" => __( "Materiales de Evento", "dimacros" ),
            "labels" => $labels,
            "description" => "Agregar Material de evento.",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => false,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => true,
            "show_in_menu" => 'edit.php?post_type=tc_events',
            "show_in_nav_menus" => false,
            "exclude_from_search" => true,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => array( "slug" => "materiales-de-eventos", "with_front" => true ),
            "query_var" => false
        );  

        register_post_type( "event_resource", $args );
            
    }

    public static function notify_attendees_action_link($actions, $post) {

        if( $post->post_type !== 'event_resource' ) return $actions;

        $security = wp_nonce_url(
            admin_url("admin-post.php?action=notify_attendees&post_id={$post->ID}"),
            'notify_attendees'
        );

        $actions['notify_attendees'] = sprintf(
            '<a href="%s">Notificar a los asistentes</a>', $security
        );

        return $actions;
    }

    public static function notifyAttendeesManually() {

        check_admin_referer('notify_attendees');

        self::notify(
            filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT)
        );

        wp_die('Correo enviado con éxito.', 'Notificar al asistente.', [
            'response' => 200, 'back_link' => true
        ]);
    }

    private static function notify($post_id) 
    {
        if( is_null($post_id) ) {
            throw new \Exception('The post_id of Resource is required.');
        }

        $event_id = get_field('event_id', $post_id);
        $subject = get_the_title($post_id);
        $body = Helpers\view('emails/event_resource-body', [
            'event_title' => get_the_title($event_id),
            'link' => get_permalink($post_id)
        ]);

        $tickets = Ticket::byEvent($event_id)->get();
        $tickets->each(function($ticket) use ($subject, $body) {
            wp_mail(
                $ticket->user->user_email, $subject, $body, [
                    "Content-Type: text/html; charset=UTF-8",
                    sprintf("From: %s <%s>", Site::title(), Site::adminEmail())
                ]
            );
            dump("Correo enviado a {$ticket->user->user_email} correctamente.");
        });
    }
}