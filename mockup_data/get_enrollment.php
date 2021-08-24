<?php
namespace matriculation\mockup_data;

require_once('./Enrollments.php');

header('Access-Control-Allow-Origin: *');
header ('Content-type: application/json; charset=utf-8'); 

$enrollmentUuid = $_GET['uuid'];

$enrollments = new Enrollments();
$enrollment = $enrollments->getEnrollment($enrollmentUuid);
if ($enrollment == null) {
    header('HTTP/ 400 Enrollment retrieved');
    echo json_encode(array('status' => 'error', 'message' => "Enrollment with uuid $enrollmentUuid not retrieved"));
    exit;
} 

header('HTTP/ 200 Enrollment retrieved');
echo json_encode(array('status' => 'ok', 'enrollment' => $enrollment));
?>