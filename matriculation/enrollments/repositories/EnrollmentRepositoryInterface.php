<?php
namespace matriculation\enrollments;

use GuzzleHttp\Promise\Promise; 

interface EnrollmentRepositoryInterface {
    // public function retrieveStudents(): Promise;
    // public function retrieveStudentByUuid(string $studentUuid): Promise;
    // function storeEnrollment(string $enrollmentUuid): Promise;
    // function retrieveEnrollments(): Promise;
    public function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise;
}
?>