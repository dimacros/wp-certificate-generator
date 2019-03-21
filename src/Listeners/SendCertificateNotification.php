<?php 
namespace Dimacros\Listeners;

use Dimacros\Helpers;
use Dimacros\Models\Event;
use Dimacros\Models\Ticket;
use Dimacros\Utils\CertificateGenerator;
use Dimacros\Site;
use Mpdf\Output;

class SendCertificateNotification
{
    public static function handle(Event $event) 
    {  
        try {
            $body = 'Certificado enviado por asistir al evento ' . $event->post_title;  
            $template = Helpers\get_certificate_template_by_event($event->ID);
            $tickets = Ticket::byEvent($event->ID)->published()->get();
            $tickets->each(function ($ticket) use ($body, $template) {
                $attachment = TEMP_DIR . '/' . uniqid('cert_') . '.pdf';
                $certificate = new CertificateGenerator($ticket->user, $template);
                $certificate->generate($attachment, Output\Destination::FILE);
                wp_mail(
                    $ticket->user->user_email, $template->post_title, $body, [
                        "Content-Type: text/html; charset=UTF-8",
                        sprintf("From: %s <%s>", Site::title(), Site::adminEmail())
                    ], $attachment
                );
                dump("Correo enviado a {$ticket->user->user_email} correctamente.");
            });
            $event->markAsCompleted();
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

    public static function filterAttendants($attendants) 
    {
        return $attendants->filter(function($attendant) {
            
            $checkins = get_post_meta($attendant->ID, 'tc_checkins', true);

            if( empty($checkins) ) return false;

            $valid_checkins = array_filter($checkins, function($checkin){
                return ($checkin['status'] === 'Pass');
            });
            
            return count($valid_checkins);

        });
    }
}