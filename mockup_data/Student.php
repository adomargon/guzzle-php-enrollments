<?php
namespace matriculation\mockup_data;

class Student {
    public string $uuid;
    public string $firstName;
    public string $lastName;

    public function __construct(string $studentUuid, string $firstName, string $lastName) {
        $this->uuid = $studentUuid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
?>