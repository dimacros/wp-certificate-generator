<?php 
namespace Dimacros;

use Dimacros\Models\User;
use Dimacros\Utils\CertificateGenerator;
use Dimacros\Site;
use Mpdf\Output;
use WPEloquent\Model\Post;

class CertificateManager
{
    public static function registerPostType() {

        /**
         * Post Type: Certificados.
         */

        $labels = array(
            "name" => __( "Certificados", "dimacros" ),
            "singular_name" => __( "Certificado", "dimacros" ),
            "menu_name" => __( "Certificados", "dimacros" ),
            "all_items" => __( "Plantillas de Certificados", "dimacros" ),
            "add_new" => __( "Agregar nuevo", "dimacros" ),
            "add_new_item" => __( "Agregar nuevo certificado", "dimacros" ),
            "edit_item" => __( "Editar certificado", "dimacros" ),
            "new_item" => __( "Nuevo certificado", "dimacros" ),
            "view_item" => __( "Ver certificado", "dimacros" ),
            "view_items" => __( "Ver certificados", "dimacros" ),
            "search_items" => __( "Buscar certificado", "dimacros" ),
            "not_found" => __( "No se encontraron certificados", "dimacros" ),
            "not_found_in_trash" => __( "No se encontraron certificados en la papelera", "dimacros" ),
        );

        $args = array(
            "label" => __( "Certificados", "dimacros" ),
            "labels" => $labels,
            "description" => "Agregar una plantilla de certificado para cada evento.",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => false,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => 'edit.php?post_type=tc_events',
            "show_in_nav_menus" => false,
            "exclude_from_search" => true,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => array( "slug" => "certificados", "with_front" => true ),
            "query_var" => false,
            "supports" => array( "title", "editor", "thumbnail" ),
        );  

        register_post_type( "certificate", $args );
            
    }

    public static function addPage() {
    
        add_submenu_page(
            'edit.php?post_type=tc_events',
            'Certificar Manualmente',
            'Certificar Manualmente',
            'administrator',
            'certificate-manager',
            function() {
                print Helpers\view('admin/send_certificate-form', [
                    'templates' => Post::type('certificate')->published()->get(),
                    'users' => User::all()
                ]);
            }
        ); 

    }

    public static function doManually() {

        check_admin_referer('send_certificate');
        $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
        $event_id = get_field('event_id', $post_id);
        
        try {
            $body = 'Certificado enviado manualmente por asistir al evento ' . get_the_title($event_id);
            $user = User::find($user_id);
            $template = Helpers\get_certificate_template_by_event($event_id);
            $attachment = TEMP_DIR . '/' . uniqid('cert_') . '.pdf';
            $certificate = new CertificateGenerator($user, $template);
            $certificate->generate($attachment, Output\Destination::FILE);
            wp_mail(
                $user->user_email, $template->post_title, $body, [
                    "Content-Type: text/html; charset=UTF-8",
                    sprintf("From: %s <%s>", Site::title(), Site::adminEmail())
                ], $attachment
            );
            dump("Correo enviado a {$user->user_email} correctamente.");
        }
        catch(\Throwable $e) {
            dump('Ocurrió un error inesperado: ' . $e->getMessage());
        }
        finally {
            wp_die('Solicitud Finalizada.', 'Enviar Certificados a los Asistentes del evento.', [
                'back_link' => true
            ]);
        }      
    }
}