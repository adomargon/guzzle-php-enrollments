<?php
namespace matriculation\mockup_data;

require_once('./Student.php');

use ArrayObject;

class Students extends ArrayObject {
    function __construct() {
        $this->append(new Student('1111-pepe', 'Pepe', 'López'));
        $this->append(new Student('2222-maria', 'María', 'Sánchez'));
        $this->append(new Student('3333-juan', 'Juan', 'Jiménez'));
    }

    function getStudent(string $studentUuid) {
        $filteredStudents = array_filter(
            $this->getArrayCopy(), 
            function ($student) use ($studentUuid) {
                return $student->uuid == $studentUuid;
            }
        );
        return $filteredStudents[0];
    }
}
?>