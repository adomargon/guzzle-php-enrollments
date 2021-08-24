<?php
namespace matriculation\common;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';

use matriculation\common\QueryInterface;
use GuzzleHttp\Promise\Promise;

interface QueryHandlerInterface {
    public function handle(QueryInterface $query): Promise;    
}
?>