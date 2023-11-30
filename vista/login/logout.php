<?php
  require_once('../../modelo/load.php');
  if(!$session->logout()) {redirect("../../index.php");}
?>
