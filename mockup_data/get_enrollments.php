<?php
namespace matriculation\mockup_data;

require_once('./Enrollment.php');

header('Access-Control-Allow-Origin: *');
header ('Content-type: application/json; charset=utf-8'); 

$students = (new Enrollments())->getArrayCopy();
header('HTTP/ 200 Enrollments retrieved');
echo json_encode(array('status' => 'ok', 'enrollment' => $enrollments));
?>