<?php 

namespace Dimacros\Models;

use WPEloquent\Model\Post;

class Event extends Post
{
    protected $post_type = 'tc_events';
    
    protected $fillable = ['post_status'];

    public function markAsCompleted() {
        return $this->update(['post_status' => 'completed']);
    }
}