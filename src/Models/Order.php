<?php

namespace Dimacros\Models;

use WPEloquent\Model\Post;

class Order extends Post
{
    protected $post_type = 'shop_order';

    public function ticket() 
    {
        return $this->hasOne(Ticket::class, 'post_parent', 'ID');
    }
    
    public function getDetailsAttribute()
    {
        return $this->meta->pluck('meta_value', 'meta_key');
    }
}