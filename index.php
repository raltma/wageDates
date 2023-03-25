<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');

// Imports
require_once 'objects/Error.php';
require_once 'objects/PayDateCalculator.php';
use spinTek\objects\Error;
use spinTek\objects\PayDateCalculator;

// Validate input
if(!isset($_GET['year'])) Error::showJsonError("The request did not contain year parameter");
if(!is_numeric($_GET['year'])) Error::showJsonError("The parameter year was not numeric");
if($_GET['year'] < 1800) Error::showJsonError("Are you trying to pay to your ancestors? Year must be larger than 1800");
if($_GET['year'] > 9999) Error::showJsonError("Year is too large! Year must be smaller than 10000");

// Calculate paying dates and return them as json
$year = $_GET['year'];
$calculator = new PayDateCalculator($year);
echo json_encode(
    [
        "year" => (int)$year,
        "dates" => $calculator->calculatePayDates()
    ]);
http_response_code(200);