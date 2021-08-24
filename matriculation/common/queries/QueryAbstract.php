<?php
namespace matriculation\common;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';

use matriculation\common\QueryInterface;

abstract class QueryAbstract implements QueryInterface {
    public static function getClassName(): string {
        return QueryAbstract::$className;
    }
}
?>