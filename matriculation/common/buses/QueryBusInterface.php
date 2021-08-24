<?php
namespace matriculation\common;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerInterface.php';

use GuzzleHttp\Promise\Promise;
use matriculation\common\QueryHandlerInterface;

interface QueryBusInterface {
    public function register(string $queryClassName, QueryHandlerInterface $handler): void;
    public function execute(QueryInterface $query): Promise;
}
?>