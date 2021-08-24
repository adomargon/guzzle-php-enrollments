<?php
namespace matriculation\students;

use Exception; 

class StudentRepositoryException extends Exception {
    public function __construct(string $message) {
        parent::__construct("<<< StudentRepositoryException: $message >>>");
    }
}
?>