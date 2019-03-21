<?php

namespace Dimacros;

class Site
{
    public static function title() 
    {
        return get_bloginfo('name');
    }

    public static function adminEmail()
    {
        return get_bloginfo('admin_email');
    }
}