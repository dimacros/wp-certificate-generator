<?php

namespace Dimacros\Models;

use WPEloquent\Model\Post;
use Illuminate\Support\Collection;

class Ticket extends Post
{
    protected $post_type = 'tc_tickets_instances';

    protected $with = ['order', 'order.meta', 'user', 'user.meta'];

    public function order() 
    {
        return $this->belongsTo(Order::class, 'post_parent', 'ID');
    }

    public function user() 
    {
        return $this->belongsTo(User::class, 'post_author', 'ID')->withDefault($this->getDefaultUser());
    }

    private function getDefaultUser() 
    {
        return [
            'user_email' => $this->order->details['_billing_email'],
            'meta' => Collection::make([
                [
                    'meta_key' =>  'first_name',
                    'meta_value' => $this->order->details['_billing_first_name']
                ],
                [
                    'meta_key' =>  'last_name',
                    'meta_value' => $this->order->details['_billing_last_name']
                ],
            ])
        ];
    }
    
    public function scopeByEvent($query, $event_id)
    {
        $query->whereHas('meta', function($query) use ($event_id){
            $query->where([
                'meta_key' => 'event_id',
                'meta_value' => $event_id
            ]);
        });

        return $query;
    }
}