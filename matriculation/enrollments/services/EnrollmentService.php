<?php
namespace matriculation\enrollments;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/queries/GetEnrollmentByUuidQuery.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/queries/GetEnrollmentByUuidQueryHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/services/EnrollmentServiceInterface.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/services/EnrollmentServiceException.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerException.php');

use GuzzleHttp\Promise\Promise;
use matriculation\common\QueryBusInterface;
use matriculation\enrollments\GetEnrollmentByUuidQuery;
use matriculation\enrollments\GetEnrollmentByUuidQueryHandler;
use matriculation\enrollments\EnrollmentServiceInterface;
use matriculation\enrollments\EnrollmentServiceException;
use matriculation\enrollments\EnrollmentRepositoryInterface;
use matriculation\common\QueryHandlerException;

class EnrollmentService implements EnrollmentServiceInterface {
    private QueryBusInterface $commandBus;
    private QueryBusInterface $queryBus;

    public function __construct(
        QueryBusInterface $commandBus, 
        QueryBusInterface $queryBus,
        EnrollmentRepositoryInterface $enrollmentRepository
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->enrollmentRepository = $enrollmentRepository;

        $this->queryBus->register(
            GetEnrollmentByUuidQuery::getClassName(), 
            new GetEnrollmentByUuidQueryHandler($enrollmentRepository)
        );
    }

    public function getAllEnrollments(): Promise {
        $query = new GetAllEnrollmentsQuery();
        
        $resultEnrollmentsPromise = new Promise();

        $enrollmentsPromise = $this->queryBus.execute($query);
        $enrollmentsPromise->then(
            function($enrollments) use ($resultEnrollmentsPromise) {
                $resultEnrollmentsPromise->resolve($enrollments);
            },

            function($error) use ($resultEnrollmentsPromise) {
                $resultEnrollmentsPromise->reject($error);
            }
        );
        $enrollmentsPromise->wait();

        return $resultEnrollmentsPromise;
    }

    public function getEnrollmentByUuid(string $uuid): Promise {
        try {
            $query = new GetEnrollmentByUuidQuery($uuid);
            
            $resultPromise = new Promise(
                function() use (&$resultPromise, $query) {
                    try {
                        $enrollment = $this->queryBus->execute($query)->wait();
                        $resultPromise->resolve($enrollment);
                    } catch (QueryHandlerException $e) {
                        throw new EnrollmentServiceException($e->getMessage());
                    }
                }
            );

            return $resultPromise;
        } catch (Throwable $e) {
            throw new EnrollmentServiceException($e->getMessage());
        }
    }
}
?>