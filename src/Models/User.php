<?php

namespace Dimacros\Models;

use WPEloquent\Model\User as UserModel;

class User extends UserModel
{
    protected $appends = ['details', 'full_name'];
    
    protected $visible = ['user_email', 'details', 'full_name'];

    public function getDetailsAttribute() 
    {
        return $this->meta->pluck('meta_value', 'meta_key');
    }

    public function getFullNameAttribute() 
    {
        return "{$this->details['first_name']} {$this->details['last_name']}";
    }
}