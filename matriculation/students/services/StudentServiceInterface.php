<?php
namespace matriculation\students;

use GuzzleHttp\Promise\Promise; 

interface StudentServiceInterface {
    public function getAllStudents(): Promise;
    public function getStudentByUuid(string $uuid): Promise;
}
?>