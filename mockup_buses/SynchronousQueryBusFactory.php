<?php
namespace matriculation\mockup_buses;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusError.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerException.php');

use matriculation\common\QueryInterface;
use matriculation\common\QueryBusInterface;
use matriculation\common\QueryBusError;
use matriculation\common\QueryHandlerInterface;
use matriculation\common\QueryHandlerException;
use GuzzleHttp\Promise\Promise;

class SynchronousQueryBus implements QueryBusInterface {
    private $handlers;

    public function __construct() {
        $this->handlers = array();
    }

    public function register(string $queryClassName, QueryHandlerInterface $handler): void {
        $this->handlers[$queryClassName] = $handler;
    }

    public function execute(QueryInterface $query): Promise {
        try {
            $className = $query->getClassName();
            if (!isset($className) || $className == '') {
                throw new QueryBusError(`Query class name is needed`);
            }

            $handler = $this->handlers[$className];
            if (!isset($handler)) {
                throw new QueryBusError("Query $className is not registered");
            }

            $resultPromise = new Promise(
                function() use (&$resultPromise, $handler, $query) {
                    try {
                        $result = $handler->handle($query)->wait();
                        $resultPromise->resolve($result);
                    } catch (QueryHandlerException $e) {
                        throw $e;
                    }
                }
            );

            return $resultPromise;
        } catch (Throwable $e) {
            throw new QueryBusError($e->getMessage());
        }
    }
}

class SynchronousQueryBusFactory {
    private static SynchronousQueryBus $queryBus; 

    public static function createQueryBus(): SynchronousQueryBus {
        if (!isset(SynchronousQueryBusFactory::$queryBus)) {
            SynchronousQueryBusFactory::$queryBus = new SynchronousQueryBus();
        }

        return SynchronousQueryBusFactory::$queryBus;
    }
}
?>