<?php

$messageTypes = ["success", "info", "warning", "danger", "dark"];

if (!isset($_SESSION["project_ksu_st_karolus"]["users"])) {
  if (isset($_SESSION["project_ksu_st_karolus"]["time_message"]) && (time() - $_SESSION["project_ksu_st_karolus"]["time_message"]) > 2) {
    foreach ($messageTypes as $type) {
      if (isset($_SESSION["project_ksu_st_karolus"]["message_$type"])) {
        unset($_SESSION["project_ksu_st_karolus"]["message_$type"]);
      }
    }
    unset($_SESSION["project_ksu_st_karolus"]["time_message"]);
  }
} else if (isset($_SESSION["project_ksu_st_karolus"]["users"])) {
  if (isset($_SESSION["project_ksu_st_karolus"]["users"]["time_message"]) && (time() - $_SESSION["project_ksu_st_karolus"]["users"]["time_message"]) > 2) {
    foreach ($messageTypes as $type) {
      if (isset($_SESSION["project_ksu_st_karolus"]["users"]["message_$type"])) {
        unset($_SESSION["project_ksu_st_karolus"]["users"]["message_$type"]);
      }
    }
    unset($_SESSION["project_ksu_st_karolus"]["users"]["time_message"]);
  }
}
