<?php
namespace matriculation\mockup_data;

class Enrollment {
    public string $uuid;
    public string $studentUuid;

    public function __construct(string $enrollmentUuid, string $studentUuid) {
        $this->uuid = $enrollmentUuid;
        $this->studentUuid = $studentUuid;
    }
}
?>