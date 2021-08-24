<?php
namespace matriculation\enrollments;

use matriculation\common\CommandAbstract;

class CreateEnrollmentCommand extends CommandAbstract { 
    public string $enrollmentUuid;
}
?>