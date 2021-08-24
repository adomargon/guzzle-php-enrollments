<?php
namespace matriculation\students;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryAbstract.php';

use matriculation\common\QueryAbstract;
use matriculation\common\QueryInterface;

class GetStudentByUuidQuery /*extends QueryAbstract*/ implements QueryInterface { 
    private static string $className = 'GetStudentByUuidQuery';
    
    private string $studentUuid;

    public function __construct($studentUuid) {
        $this->studentUuid = $studentUuid;
    }

    public static function getClassName(): string {
        return GetStudentByUuidQuery::$className;
    }

    public function getStudentUuid(): string {
        return $this->studentUuid;
    }
}
?>