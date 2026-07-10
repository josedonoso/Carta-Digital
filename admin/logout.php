<?php

/* ==========================================
   CERRAR SESIÓN
========================================== */

session_start();

/* Eliminar variables de sesión */
$_SESSION = [];

/* Destruir sesión */
session_destroy();

/* Redireccionar al login */
header("Location: login.php");
exit;
