<?php
namespace matriculation\mockup_data;

require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryInterface.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryException.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/entities/Student.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/entities/Enrollment.php';


use matriculation\enrollments\EnrollmentRepositoryInterface;
use matriculation\enrollments\EnrollmentRepositoryException;
use matriculation\enrollments\Student;
use matriculation\enrollments\Enrollment;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

class MockupEnrollmentRepositoryAdapter implements EnrollmentRepositoryInterface {

    function retrieveEnrollments(): Promise {
        $promiseEnrollments = new Promise();

        $client = new Client();
        $promise = $client->requestAsync('GET', 'http://127.0.0.1/mockup_data/get_enrollments.php');
        $promise->then(
            function ($response) use ($promiseEnrollments) {
                $enrollments = $response->getBody()->getContents();
                $promiseEnrollments->resolve($enrollments);
            },
            
            function ($reason) use ($promiseEnrollments) {
                $promiseEnrollments->reject($reason);
            }
        );
        $promise->wait();
        return $promiseEnrollments;
    }


    function retrieveDetailOfStudentByUuid(string $studentUuid): Promise {
        try {
            $promiseStudent = new Promise(
                function() use (&$promiseStudent, $studentUuid) {
                    $client = new Client(['http_errors' => false]);
                    $url = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
                    $response = $client->requestAsync('GET', $url)->wait();

                    if ($response->getStatusCode() >= 300) {
                        $message = $response->getStatusCode();
                        throw new EnrollmentRepositoryException($message);
                    } else {
                        $data = json_decode($response->getBody()->getContents());
                        $student = $data->student;
                        $promiseStudent->resolve($student);
                    }
                }
            );

            return $promiseStudent;
        } catch (Throwable $e) {
            throw new EnrollmentRepositoryException($e->getMessage());
        }
    }

    function retrieveDetailOfEnrollmentByUuid(string $enrollmentUuid): Promise {
        try {
            $promiseEnrollment = new Promise(
                function() use (&$promiseEnrollment, $enrollmentUuid) {
                    $client = new Client(['http_errors' => false]);
                    $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
                    $response = $client->requestAsync('GET', $url)->wait();
            
                    if ($response->getStatusCode() >= 300) {
                        $message = $response->getStatusCode();
                        throw new EnrollmentRepositoryException($message);
                    } else {
                        $data = json_decode($response->getBody()->getContents());
                        $enrollment = $data->enrollment;
    
                        $promiseEnrollment->resolve($enrollment);
                    }
                }
            );
        
            return $promiseEnrollment;
        } catch (Throwable $e) {
            throw new EnrollmentRepositoryException($e->getMessage());
        }
    }

    function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
        try {
            $promiseResult = new Promise(
                function() use (&$promiseResult, $enrollmentUuid) {
                    try {
                        $enrollment = $this->retrieveDetailOfEnrollmentByUuid($enrollmentUuid)->wait();

                        $studentUuid = $enrollment->studentUuid;                    
                        $student = $this->retrieveDetailOfStudentByUuid($studentUuid)->wait();
                        
                        $studentEntity = new Student($student->uuid, $student->firstName, $student->lastName);
                        $enrollmentEntity = new Enrollment($enrollment->uuid, $studentEntity);    
                        $promiseResult->resolve($enrollmentEntity);
                    } catch (EnrollmentRepositoryException $e) {
                        throw new EnrollmentRepositoryException($e->getMessage());
                    } catch (Throwable $e) {
                        throw new EnrollmentRepositoryException($e->getMessage());
                    }
                }
            );
            
            return $promiseResult;
        } catch (RejectionException $e) {
            throw new EnrollmentRepositoryException($e->getMessage());
        } catch (Throwable $e) {
                    echo 'ANCA';
            throw new EnrollmentRepositoryException($e->getMessage());
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////


    //////////////////////////////////////////////////////////////////////////////////

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseResult = new Promise(function() use (&$promiseResult, $enrollmentUuid) {
    //             $client = new Client(['http_errors' => false]);
    //             $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
    //             $promiseRequest = $client->requestAsync('GET', $url);

    //             $promiseRequest->then(
    //                 function($response) use (&$promiseResult) {
    //                     if ($response->getStatusCode() >= 300) {
    //                         $message = $response->getStatusCode();
    //                         return new RejectedPromise();
    //                     }
            
    //                     $data = json_decode($response->getBody()->getContents());
    //                     $enrollment = $data->enrollment;

                        
    //                     $client2 = new Client(['http_errors' => false]);
    //                     $studentUuid = $enrollment->studentUuid;        
    //                     echo  "<<<22 $studentUuid 22>>>";
    //                     $url2 = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
    //                     $promiseRequest2 = $client2->requestAsync('GET', $url2);

    //                     $promiseRequest2->then(
    //                         function ($response2) use (&$promiseResult, $enrollment) {
    //                             echo $url2 . '))))))))))))))))))';
    //                             echo $response2->getStatusCode() . '<<<<<<<<<<<<<<<<';
    //                             if ($response2->getStatusCode() >= 300) {
    //                                 $message2 = $response2->getStatusCode();
    //                                 return new RejectedPromise();
    //                             }
                    
    //                             $data2 = json_decode($response2->getBody()->getContents());
    //                             $student = $data2->student;
    //                             $enrollment->student = $student;
                    
    //                             $studentEntity = new Student($student->uuid, $student->firstName, $student->lastName);
    //                             $enrollmentEntity = new Enrollment($enrollment->uuid, $studentEntity);
                    
    //                             $promiseResult->resolve($enrollmentEntity);
    //                         }
    //                     );

    //                     $promiseRequest2->wait();
    //                 }
    //             );

    //             $promiseRequest->wait();
    //         });

    //         return $promiseResult;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////

    // function retrieveDetailOfStudentByUuid(string $studentUuid): Promise {
    //     $promiseStudent = new Promise();

    //     $client = new Client(['http_errors' => false]);
    //     $url = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
    //     $promiseRequest = $client->requestAsync('GET', $url);
    //     $promiseRequest->then(
    //         function ($response) use ($promiseStudent) {
    //             if ($response->getStatusCode() >= 300) {
    //                 $message = $response->getStatusCode();
    //                 $promiseStudent->reject($message);
    //             } else {
    //                 $data = json_decode($response->getBody()->getContents());
    //                 $student = $data->student;
    //                 $promiseStudent->resolve($student);
    //             }
    //         },

    //         function (RequestException $reason) use ($promiseStudent) {
    //             echo 'rejected';
    //             // throw new EnrollmentRepositoryException($reason->getMessage());
    //         }
    //     );
    //     $promiseRequest->wait();

    //     return $promiseStudent;
    // }

    // function retrieveDetailOfEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     $promiseEnrollment = new Promise();
        
    //     $client = new Client(['http_errors' => false]);
    //     $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
    //     $promiseRequest = $client->requestAsync('GET', $url);
    //     $promiseRequest->then(
    //         function ($response) use ($promiseEnrollment) {
    //             if ($response->getStatusCode() >= 300) {
    //                 $message = $response->getStatusCode();
    //                 $promiseEnrollment->reject($message);
    //             } else {
    //                 $data = json_decode($response->getBody()->getContents());
    //                 $enrollment = $data->enrollment;

    //                 $promiseEnrollment->resolve($enrollment);
    //                 // return $enrollment;
    //             }
    //         },

    //         function (RequestException $reason) use ($promiseEnrollment) {
    //             // throw new EnrollmentRepositoryException($reason->getMessage());
    //         }
    //     );
    //     $promiseRequest->wait();

    //     return $promiseEnrollment;
    // }

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseResult = new Promise();

    //         $promiseEnrollment = $this->retrieveDetailOfEnrollmentByUuid($enrollmentUuid);
    //         $promiseEnrollment->then(
    //             function ($enrollment) use ($promiseResult) {
    //                             echo '55555555555555555555555555555';
    //                 $studentUuid = $enrollment->studentUuid;

    //                 $promiseStudent = $this->retrieveDetailOfStudentByUuid($studentUuid);
    //                 $promiseStudent->then(
    //                     function($student) use ($promiseResult, $enrollment) {
    //                         $enrollment->student = $student;
    //                         $promiseResult->resolve($enrollment);
    //                     }
    //                 );
    //             }
    //         );

    //         return $promiseResult;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }

    /////////////////////////////////////////////////////////////////////////////////////////////////

    // function retrieveStudentByUuid(string $studentUuid): Promise {
    //     $promiseStudent = new Promise();

    //     $client = new Client(['http_errors' => false]);
    //     $url = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
    //     $promiseRequest = $client->requestAsync('GET', $url);
    //     $promiseRequest->then(
    //         function ($response) use ($promiseStudent) {
    //             if ($response->getStatusCode() >= 300) {
    //                 $message = $response->getStatusCode();
    //                 $promiseStudent->reject($message);
    //             } else {
    //                 $data = json_decode($response->getBody()->getContents());
    //                 $student = $data->student;
    //                 $promiseStudent->resolve($student);
    //             }
    //         },

    //         function (RequestException $reason) use ($promiseStudent) {
    //             echo 'rejected';
    //             // throw new EnrollmentRepositoryException($reason->getMessage());
    //         }
    //     );
    //     $promiseRequest->wait();

    //     return $promiseStudent;
    // }

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseEnrollment = new Promise();

    //         $client = new Client(['http_errors' => false]);
    //         $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
    //         $promiseRequest = $client->requestAsync('GET', $url);
    //         $promiseRequest->then(
    //             function ($response) use ($promiseEnrollment) {
    //                 if ($response->getStatusCode() >= 300) {
    //                     $message = $response->getStatusCode();
    //                     $promiseEnrollment->reject($message);
    //                 } else {
    //                     $data = json_decode($response->getBody()->getContents());
    //                     $enrollment = $data->enrollment;

    //                     $studentUuid = $enrollment->studentUuid;
    //                     $promiseStudent = $this->retrieveStudentByUuid($studentUuid);
    //                     $student = $promiseStudent->wait();
    //                     $enrollment->student = $student;

    //                     $promiseEnrollment->resolve($enrollment);
    //                     // return $enrollment;
    //                 }
    //             },

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 // throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         );
    //         $promiseRequest->wait();
    
    //         return $promiseEnrollment;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseEnrollment = new Promise();

    //         $onfullfilledGetStudent = function ($enrollment) use ($promiseEnrollment) {
    //             $studentUuid = $enrollment->studentUuid;

    //             $client2 = new Client(['http_errors' => false]);
    //             $url = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
    //             $promiseRequest2 = $client2->requestAsync('GET', $url);
    //             $promiseRequest2->then(
    //                 function ($response) use ($promiseEnrollment) {
    //                     if ($response->getStatusCode() >= 300) {
    //                         $message = $response->getStatusCode();
    //                         $promiseEnrollment->reject($message);
    //                     } else {
    //                         $data = json_decode($response->getBody()->getContents());
    //                         $student = $data->student;
    //                         $promiseEnrollment->resolve($student);
    //                     }
    //                 },

    //                 function (RequestException $reason) use ($promiseEnrollment) {
    //                     // throw new EnrollmentRepositoryException($reason->getMessage());
    //                 }
    //             );
    //             $promiseRequest2->wait();
    //         };

    //         $client = new Client(['http_errors' => false]);
    //         $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
    //         $promiseRequest = $client->requestAsync('GET', $url);
    //         $promiseRequest->then(
    //             function ($response) use ($promiseEnrollment) {
    //                 if ($response->getStatusCode() >= 300) {
    //                     $message = $response->getStatusCode();
    //                     $promiseEnrollment->reject($message);
    //                 } else {
    //                     $data = json_decode($response->getBody()->getContents());
    //                     $enrollment = $data->enrollment;
    //                     //$promiseEnrollment->resolve($enrollment);
    //                     return $enrollment;
    //                 }
    //             },

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 // throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         )->then(
    //             $onfullfilledGetStudent,

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 // throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         );
    //         $promiseRequest->wait();
    
    //         return $promiseEnrollment;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseEnrollment = new Promise();

    //         $client = new Client(['http_errors' => false]);
    //         $url = "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid";
    //         $promiseRequest = $client->requestAsync('GET', $url);
    //         $promiseRequest->then(
    //             function ($response) use ($promiseEnrollment) {
    //                 if ($response->getStatusCode() >= 300) {
    //                     $message = $response->getStatusCode();
    //                     $promiseEnrollment->reject($message);
    //                 } else {
    //                     $data = json_decode($response->getBody()->getContents());
    //                     $enrollment = $data->enrollment;
    //                     //$promiseEnrollment->resolve($enrollment);
    //                     return $enrollment;
    //                 }
    //             },

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 // throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         )->then(
    //             function ($enrollment) use ($promiseEnrollment) {
    //                 $studentUuid = $enrollment->studentUuid;

    //                 $client2 = new Client(['http_errors' => false]);
    //                 $url = "http://127.0.0.1/mockup_data/get_student.php?uuid=$studentUuid";
    //                 $promiseRequest2 = $client2->requestAsync('GET', $url);
    //                 $promiseRequest2->then(
    //                     function ($response) use ($promiseEnrollment) {
    //                         if ($response->getStatusCode() >= 300) {
    //                             $message = $response->getStatusCode();
    //                             $promiseEnrollment->reject($message);
    //                         } else {
    //                             $data = json_decode($response->getBody()->getContents());
    //                             $student = $data->student;
    //                             $promiseEnrollment->resolve($student);
    //                         }
    //                     },

    //                     function (RequestException $reason) use ($promiseEnrollment) {
    //                         // throw new EnrollmentRepositoryException($reason->getMessage());
    //                     }
    //                 );
    //                 $promiseRequest2->wait();
    //             },

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 // throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         );
    //         $promiseRequest->wait();
    
    //         return $promiseEnrollment;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////

    // function retrieveEnrollmentByUuid(string $enrollmentUuid): Promise {
    //     try {
    //         $promiseEnrollment = new Promise();

    //         $client = new Client(['http_errors' => false]);
    //         $promiseRequest = $client->requestAsync('GET', "http://127.0.0.1/mockup_data/get_enrollment.php?uuid=$enrollmentUuid");
    //         $promiseRequest->then(
    //             function ($response) use ($promiseEnrollment) {
    //                 if ($response->getStatusCode() >= 300) {
    //                     $message = $response->getStatusCode();
    //                     $promiseEnrollment->reject($message);
    //                 } else {
    //                     $data = json_decode($response->getBody()->getContents());
    //                     $enrollment = $data->enrollment;
    //                     $promiseEnrollment->resolve($enrollment);
    //                 }
    //             },

    //             function (RequestException $reason) use ($promiseEnrollment) {
    //                 throw new EnrollmentRepositoryException($reason->getMessage());
    //             }
    //         );
    //         $promiseRequest->wait();
    
    //         return $promiseEnrollment;
    //     } catch (Throwable $e) {
    //         throw new EnrollmentRepositoryException($e->getMessage());
    //     }
    // }
}
?>