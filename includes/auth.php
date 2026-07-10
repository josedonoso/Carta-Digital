<?php

require_once __DIR__ . "/config.php";

/* ==========================================
   INICIAR SESIÓN
========================================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ==========================================
   VALIDAR AUTENTICACIÓN
========================================== */
if (!isset($_SESSION["admin_id"])) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}
