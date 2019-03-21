<?php 
namespace Dimacros;
class Schedule 
{
    public static function call() {
        if ( !wp_next_scheduled ('notify_attendees_after_event') ) {
            wp_schedule_event(time(), 'daily', 'notify_attendees_after_event');
        }
    }

    public static function destroy() {
        wp_clear_scheduled_hook('notify_attendees_after_event');       
    }
}