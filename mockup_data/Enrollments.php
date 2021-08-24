<?php
namespace matriculation\mockup_data;

require_once('./Enrollment.php');

use ArrayObject;

class Enrollments extends ArrayObject {
    function __construct() {
        $this->append(new Enrollment('1111-1111', '1111-pepe'));
        $this->append(new Enrollment('2222-2222', '2222-maria'));
        $this->append(new Enrollment('3333-3333', '3333-juan'));
    }

    function getEnrollment(string $enrollmentUuid) {
        $filteredEnrollments = array_filter(
            $this->getArrayCopy(), 
            function ($enrollment) use ($enrollmentUuid) {
                return $enrollment->uuid == $enrollmentUuid;
            }
        );

        if (count($filteredEnrollments) == 0) {
            return null;
        } else {
            return $filteredEnrollments[0];
        }
    }
}
?>