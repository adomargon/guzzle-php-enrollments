<?php
namespace matriculation\enrollments;

use Error; 

class EnrollmentRepositoryException extends Error {
    public function __construct(string $message) {
        parent::__construct("<<< EnrollmentRepositoryException: $message >>>");
    }
}
?>