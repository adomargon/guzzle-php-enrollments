<?php
namespace matriculation\students;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/queries/GetStudentByUuidQuery.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/queries/GetStudentByUuidQueryHandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/services/StudentServiceInterface.php';

use GuzzleHttp\Promise\Promise;
use matriculation\common\QueryBusInterface;
use matriculation\students\GetStudentByUuidQuery;
use matriculation\students\GetStudentByUuidQueryHandler;
use matriculation\students\StudentServiceInterface;
use matriculation\students\StudentRepositoryInterface;

class StudentService implements StudentServiceInterface {
    private QueryBusInterface $commandBus;
    private QueryBusInterface $queryBus;

    public function __construct(
        QueryBusInterface $commandBus, 
        QueryBusInterface $queryBus,
        StudentRepositoryInterface $studentRepository
    ) {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->studentRepository = $studentRepository;

        $this->queryBus->register(GetStudentByUuidQuery::getClassName(), new GetStudentByUuidQueryHandler($studentRepository));
    }

    public function getAllStudents(): Promise {
        $query = new GetAllStudentsQuery();
        
        $resultStudentsPromise = new Promise();

        $studentsPromise = $this->queryBus.execute($query);
        $studentsPromise->then(
            function($students) use ($resultStudentsPromise) {
                $resultStudentsPromise->resolve($students);
            },

            function($error) use ($resultStudentsPromise) {
                $resultStudentsPromise->reject($error);
            }
        );
        $studentsPromise->wait();

        return $resultStudentsPromise;
    }

    public function getStudentByUuid(string $uuid): Promise {
        try {
            $query = new GetStudentByUuidQuery($uuid);
            
            $resultStudentPromise = new Promise();

            $studentPromise = $this->queryBus->execute($query);
            $studentPromise->then(
                function($student) use ($resultStudentPromise) {
                    $resultStudentPromise->resolve($student);
                },

                function($error) use ($resultStudentPromise) {
                    $resultStudentPromise->reject($error);
                }
            );

            return $resultStudentPromise;
        } catch (Throwable $e) {
            throw new StudentRepositoryException($e->getMessage());
        }
    }
}
?>