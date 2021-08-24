<?php
namespace matriculation\enrollments;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/queries/GetEnrollmentByUuidQuery.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerException.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryException.php');

use matriculation\common\QueryInterface;
use matriculation\common\QueryHandlerInterface;
use matriculation\common\QueryHandlerException;
use matriculation\enrollments\GetEnrollmentByUuidQuery;
use matriculation\enrollments\EnrollmentRepositoryInterface;
use matriculation\enrollments\EnrollmentRepositoryException;
use GuzzleHttp\Promise\Promise;

class GetEnrollmentByUuidQueryHandler implements QueryHandlerInterface {
    private EnrollmentRepositoryInterface $enrollmentRepository;

    public function __construct(EnrollmentRepositoryInterface $enrollmentRepository) {
        $this->enrollmentRepository = $enrollmentRepository;
    }

    public function handle(QueryInterface $query): Promise {
    //public function handle(GetEnrollmentByUuidQuery $query): Promise {
        try {
            $resultPromise = new Promise(
                function() use (&$resultPromise, $query) {
                    try {
                        $enrollment = $this->enrollmentRepository->retrieveEnrollmentByUuid($query->enrollmentUuid)->wait();
                        $resultPromise->resolve($enrollment);
                    } catch (EnrollmentRepositoryException $e) {
                        throw new QueryHandlerException($e->getMessage());
                    }
                }
            );
            
            return $resultPromise;
        } catch (Throwable $e) {
            throw new QueryHandlerException($e->getMessage());
        } 
    }
}
?>