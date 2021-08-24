<?php
namespace matriculation\mockup_buses;

use matriculation\common\QueryInterface;
use matriculation\common\QueryBusInterface;
use matriculation\common\QueryBusError;
use matriculation\common\QueryHandlerInterface;
use GuzzleHttp\Promise\Promise;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusError.php';

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

            $resultPromise = new Promise();

            $promiseHandler = $handler->handle($query);
            $promiseHandler->then(
                function($result) use ($resultPromise) {
                    echo '    onfullfilled3';
                    $resultPromise->resolve($result);
                },
                function($reason) use ($resultPromise) {
                    echo '    onreject3';
                    var_dump($reason);
                    $resultPromise->reject($reason);
                }
            );
            $promiseHandler->wait();
            echo '33333333333333333333333';

            return $resultPromise;
        } catch (ClientException $e) {
            echo '??????????????????????????????????catch';
        } catch (RequestException $e) {
            echo '??????????????????????????????????catch';
        } catch (Throwable $e) {
            echo '??????????????????????????????????catch';
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