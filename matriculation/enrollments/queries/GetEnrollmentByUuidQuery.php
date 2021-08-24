<?php
namespace matriculation\enrollments;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryAbstract.php';

use matriculation\common\QueryAbstract;
use matriculation\common\QueryInterface;

class GetEnrollmentByUuidQuery /*extends QueryAbstract*/ implements QueryInterface { 
    private static string $className = 'GetEnrollmentByUuidQuery';
    
    public string $enrollmentUuid;

    public function __construct($enrollmentUuid) {
        $this->enrollmentUuid = $enrollmentUuid;
    }

    public static function getClassName(): string {
        return GetEnrollmentByUuidQuery::$className;
    }

    // public function getEnrollmentUuid(): string {
    //     return $this->enrollmentUuid;
    // } 

    // public function setEnrollmentUuid($enrollmentUuid): void {
    //     $this->enrollmentUuid = $enrollmentUuid;
    // }
}
?>