<?php
/*
Plugin Name: Certificate Generator
Description: .
Version:     1.0.0
Author:      dimacros
Author URI:  https://dimacros.net/
Text Domain: certificate-generator
Domain Path: /languages
License: Proprietary
*/

defined('ABSPATH') or die('No script kiddies please!');
define('VIEW_DIR', __DIR__ . '/resources/views');
define('TEMP_DIR', __DIR__ . '/resources/tmp');

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/functions.php');
register_activation_hook (__FILE__, 'Dimacros\Schedule::call');
register_deactivation_hook(__FILE__, 'Dimacros\Schedule::destroy');

WPEloquent\Core\Laravel::connect([
    'global' => true,

    'config' => [
        'database' => [
            'user'     => DB_USER,
            'password' => DB_PASSWORD,
            'name'     => DB_NAME,
            'host'     => DB_HOST
        ],

        'prefix' => $GLOBALS['wpdb']->prefix,
    ],

    // enable events
    'events' => false,

    // enable query log
    'log'    => true
]);

$app = new Dimacros\App('Certificate Generator'); 

$app->handle(function(){

    add_action('init', function(){
        Dimacros\EventManager::registerPostStatus();
        Dimacros\EventResource::registerPostType();
        Dimacros\CertificateManager::registerPostType();
    });

    add_action('admin_enqueue_scripts', 'Dimacros\EnqueueScripts::call');
    add_action('admin_menu', 'Dimacros\CertificateManager::addPage');
    add_action('admin_post_send_certificate', 'Dimacros\CertificateManager::doManually');
    add_filter('single_template', 'Dimacros\SinglePost::template');
    add_action('template_redirect', 'Dimacros\SinglePost::authCheck');
    add_action('notify_attendees_after_event', 'Dimacros\Jobs\SendCertificatesToAttendees::execute');
    //add_action('publish_event_resource', 'Dimacros\EventResource::notifyAttendees');
    add_action('admin_post_notify_attendees', 'Dimacros\EventResource::notifyAttendeesManually');
    add_filter('post_row_actions', 'Dimacros\EventResource::notify_attendees_action_link', 10, 2);
    add_action('admin_post_send_certificates', function(){
        check_admin_referer('send_certificates');
        $event_id = filter_input(INPUT_GET, 'event_id', FILTER_SANITIZE_NUMBER_INT);
        Dimacros\Listeners\SendCertificateNotification::handle(
            Dimacros\Models\Event::find($event_id)
        );
    });
    add_filter('post_row_actions', function ($actions, $post) {

        if( $post->post_type !== 'tc_events' ) return $actions;

        $security = wp_nonce_url(
            admin_url("admin-post.php?action=send_certificates&event_id={$post->ID}"),
            'send_certificates'
        );

        $actions['send_certificates'] = sprintf(
            '<a href="%s">Enviar certificados</a>', $security
        );

        return $actions;
    }, 10, 2);

})->withDependencies([
    'advanced-custom-fields/acf.php', 'tickera/tickera.php', 'woocommerce/woocommerce.php'
]);

try {
    $app->start();
}
catch(Dimacros\Exception\NotFoundDependencyException $e) {
    add_action('admin_notices', [$e, 'renderMessage']);
}