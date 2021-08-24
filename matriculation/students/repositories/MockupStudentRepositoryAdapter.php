<?php
namespace matriculation\mockup_data;

require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/repositories/StudentRepositoryInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/students/repositories/StudentRepositoryException.php');

use matriculation\students\StudentRepositoryInterface;
use matriculation\students\StudentRepositoryException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

class MockupStudentRepositoryAdapter implements StudentRepositoryInterface {

    function retrieveStudents(): Promise {
        $promiseStudents = new Promise();

        $client = new Client();
        $promise = $client->requestAsync('GET', 'http://127.0.0.1/mockup_data/get_students.php');
        $promise->then(
            function ($response) use ($promiseStudents) {
                $students = $response->getBody()->getContents();
                $promiseStudents->resolve($students);
            },
            
            function ($reason) use ($promiseStudents) {
                $promiseStudents->reject($reason);
            }
        );
        $promise->wait();
        return $promiseStudents;
    }


    function retrieveStudentByUuid(string $studentUuid): Promise {
        try {
            $promiseStudent = new Promise();

            $client = new Client(['http_errors' => false]);
            $promiseRequest = $client->requestAsync('GET', "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid");
            $promiseRequest->then(
                function ($response) use ($promiseStudent) {
                    if ($response->getStatusCode() >= 300) {
                        $message = $response->getStatusCode();
                        $promiseStudent->reject($message);
                    } else {
                        $data = json_decode($response->getBody()->getContents());
                        $student = $data->student;
                        $promiseStudent->resolve($student);
                    }
                },

                function (RequestException $reason) use ($promiseStudent) {
                    throw new StudentRepositoryException($reason->getMessage());
                }
            );
            $promiseRequest->wait();
    
            return $promiseStudent;
        } catch (Throwable $e) {
            throw new StudentRepositoryException($e->getMessage());
        }
    }
}
?>