<?php
define('CLI_SCRIPT', true);
require('/home/tsp/moodle45/config.php');
restore_exception_handler();
restore_error_handler();
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "1. Config loaded\n";
require_once('/home/tsp/moodle45/question/type/questionbase.php');
echo "2. Questionbase loaded\n";
require_once('/home/tsp/moodle45/question/type/aisim/question.php');
echo "3. Aisim question loaded\n";
