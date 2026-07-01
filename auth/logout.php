<?php if (!isset($_SESSION)) {
  session_start();
}
require_once("../controller/auth.php");
if (isset($_SESSION["project_ksu_st_karolus"])) {
  unset($_SESSION["project_ksu_st_karolus"]);
  header("Location: ./");
  exit();
}
