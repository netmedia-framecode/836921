<?php if (!isset($_SESSION[""])) {
  session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("Database.php");
require_once(__DIR__ . "/../controller/ketentuan-rat.php");
require_once(__DIR__ . "/../models/sql.php");
require_once(__DIR__ . "/../assets/vendor/autoload.php");
require_once(__DIR__ . "/../config/Mail.php");
require_once(__DIR__ . "/../controller/Functions.php");

// $baseURL = "https://$_SERVER[HTTP_HOST]/";
$baseURL = "http://$_SERVER[HTTP_HOST]/apps/website/tugas/ksu_st_karolus/";
$hostname = $_SERVER['HTTP_HOST'];
$port = $_SERVER['SERVER_PORT'];
if (strpos($hostname, 'apps.test') !== false && $port == 8080) {
  $baseURL = str_replace('/apps/', '/', $baseURL);
}
$name_website = "KSU St Karolus";

$select_auth = "SELECT * FROM auth";
$views_auth = mysqli_query($conn, $select_auth);
$data_auth = mysqli_fetch_assoc($views_auth);

$select_utilities = "SELECT * FROM utilities";
$views_utilities = mysqli_query($conn, $select_utilities);
$data_utilities = mysqli_fetch_assoc($views_utilities);
