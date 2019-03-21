<?php

use Dimacros\Models\User;
use Dimacros\Utils\CertificateGenerator;

global $post; 

try {
    $user =  User::find(get_current_user_id());
    $certificate = new CertificateGenerator($user, $post);
    $certificate->generate($post->post_title . '.pdf', Mpdf\Output\Destination::INLINE);
}
catch(Exception $e) {
    wp_die('Ocurrió un error en el sistema. Message: ' . $e->getMessage(), 'Error');
}