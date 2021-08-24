<?php
namespace matriculation\mockup_data;

require_once('./Students.php');

header('Access-Control-Allow-Origin: *');
header ('Content-type: application/json; charset=utf-8'); 

$students = (new Students())->getArrayCopy();
header('HTTP/ 200 Students retrieved');
echo json_encode(array('status' => 'ok', 'students' => $students));
?>