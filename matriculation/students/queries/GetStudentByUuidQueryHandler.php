<?php
namespace matriculation\students;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryHandlerInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/queries/GetStudentByUuidQuery.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/repositories/StudentRepositoryInterface.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/repositories/StudentRepositoryException.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/queries/QueryInterface.php';

use matriculation\common\QueryInterface;
use matriculation\common\QueryHandlerInterface;
use matriculation\students\GetStudentByUuidQuery;
use matriculation\students\StudentRepositoryInterface;
use matriculation\students\StudentRepositoryException;
use GuzzleHttp\Promise\Promise;

class GetStudentByUuidQueryHandler implements QueryHandlerInterface {
    private StudentRepositoryInterface $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository) {
        $this->studentRepository = $studentRepository;
    }

    public function handle(QueryInterface $query): Promise {
    //public function handle(GetStudentByUuidQuery $query): Promise {
        try {
            $resultPromise = new Promise();
            
            $studentPromise = $this->studentRepository->retrieveStudentByUuid($query->studentUuid);
            $studentPromise->then(
                function($student) use ($resultPromise) {
                    // echo "    onfullfilled2 ";
                    $resultPromise->resolve($student);
                },
                function($reason) use ($resultPromise) {
                    // echo '    onrejected2';
                    $resultPromise->reject($reason);
                }
            );

            return $resultPromise;
        } catch (Throwable $e) {
            throw new StudentRepositoryException($e->getMessage());
        }
    }
}
?>