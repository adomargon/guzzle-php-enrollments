<?php
namespace matriculation\enrollments;

use GuzzleHttp\Promise\Promise; 

interface EnrollmentServiceInterface {
    public function getAllEnrollments(): Promise;
    public function getEnrollmentByUuid(string $uuid): Promise;
}
?>