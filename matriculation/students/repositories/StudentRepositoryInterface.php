<?php
namespace matriculation\students;

use GuzzleHttp\Promise\Promise; 

interface StudentRepositoryInterface {
    public function retrieveStudents(): Promise;
    public function retrieveStudentByUuid(string $studentUuid): Promise;
    // function storeStudent(string $studentUuid): Promise;
    // function retrieveStudents(): Promise;
}
?>