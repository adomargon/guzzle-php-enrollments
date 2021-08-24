<?php
namespace matriculation\enrollments;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/entities/Student.php';

use matriculation\enrollments\Student;

class Enrollment {
    public string $uuid;
    public Student $student;

    public function __construct(string $enrollmentUuid, Student $student) {
        $this->uuid = $enrollmentUuid;
        $this->student = $student;
    }
}
?>