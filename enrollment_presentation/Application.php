<?php
namespace matriculation\application;

require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/EnrollmentRepositoryInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/common/buses/QueryBusInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/mockup_buses/SynchronousQueryBusFactory.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/services/EnrollmentServiceInterface.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/services/EnrollmentService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/repositories/MockupEnrollmentRepositoryAdapter.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/matriculation/enrollments/services/EnrollmentServiceException.php');

use matriculation\common\QueryBusInterface;
use matriculation\enrollments\EnrollmentRepositoryInterface;
use matriculation\enrollments\EnrollmentServiceInterface;
use matriculation\enrollments\EnrollmentService;
use matriculation\mockup_buses\SynchronousQueryBusFactory;
use matriculation\mockup_data\MockupEnrollmentRepositoryAdapter;
use matriculation\enrollments\EnrollmentServiceException;

class Application {
    private QueryBusInterface $queryBus;
    private EnrollmentServiceInterface $enrollmentService;

    public function __construct(EnrollmentServiceInterface $enrollmentService) {
        $this->enrollmentService = $enrollmentService;
    }

    public function run(): void {
        header("Access-Control-Allow-Origin: *");
        header ("Content-type: application/json; charset=utf-8"); 
        try {
            $enrollmentUuid = '1111-1111';
            $enrollment = $this->enrollmentService->getEnrollmentByUuid($enrollmentUuid)->wait();
            header('HTTP/ 200 Enrollment retrieved');
            echo json_encode(array("status" => "ok", "enrollment" => $enrollment));
            exit();
        } catch (EnrollmentServiceException $e) {
            header('HTTP/ 400 Enrollment not retrieved.');
            echo json_encode(array("status" => "error", "message" => "Enrollment with uuid $enrollmentUuid not retrieved: $reason"));
            exit();
        } catch (Throwable $e) {
            header('HTTP/ 400 Enrollment not retrieved.');
            echo json_encode(array("status" => "error", "message" => "Enrollment with uuid $enrollmentUuid not retrieved: $reason"));
            exit();
        }
    }
}

$enrollmentRepository = new MockupEnrollmentRepositoryAdapter();

$queryBus = SynchronousQueryBusFactory::createQueryBus();

$enrollmentService = new EnrollmentService($queryBus, $queryBus, $enrollmentRepository);

$app = new Application($enrollmentService);
$app->run();
?>