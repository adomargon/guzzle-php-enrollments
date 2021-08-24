<?php
namespace matriculation\common;

use Exception; 

class QueryHandlerException extends Exception {
    public function __construct(string $message) {
        parent::__construct("<<< QueryHandlerException: $message >>>");
    }
}
?>