<?php
if (!isset($_SESSION["project_ksu_st_karolus"]["users"])) {
  header("Location: ../../auth/");
  exit;
}
