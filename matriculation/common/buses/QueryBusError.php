<?php
namespace matriculation\common;

use Exception;

class QueryBusError extends Exception {
    public function __constructor(string $message) {
        parent::__constructor("<<< QueryBusError: $message >>>");
    }
}
?>