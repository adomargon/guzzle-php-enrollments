<?php
namespace matriculation\mockup_data;

require_once('./Students.php');

header('Access-Control-Allow-Origin: *');
header ('Content-type: application/json; charset=utf-8'); 

$studentUuid = $_GET['uuid'];

$students = new Students();
$student = $students->getStudent($studentUuid);

header('HTTP/ 200 Student retrieved');
echo json_encode(array('status' => 'ok', 'student' => $student));
?>