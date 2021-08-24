<?php
namespace matriculation\enrollments;

class Student {
    public string $uuid;
    public string $firstName;
    public string $lastName;

    public function __construct(string $uuid, string $firstName, string $lastName) {
        $this->uuid = $uuid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
?>