<?php 

namespace Dimacros\Exception;

use Dimacros\Helpers;

class NotFoundDependencyException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function renderMessage() 
    {
        print Helpers\view('admin/error_message', ['message' => $this->getMessage()]);
    }
}