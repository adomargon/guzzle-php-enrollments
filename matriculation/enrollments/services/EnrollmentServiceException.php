<?php
namespace matriculation\enrollments;

use Exception; 

class EnrollmentServiceException extends Exception {
    public function __construct(string $message) {
        parent::__construct("<<< EnrollmentServiceException: $message >>>");
    }
}
?>