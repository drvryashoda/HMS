<?php
session_start();
session_destroy();
header('Location: /cygnetclinics.com/login.php');
exit;
