<?php

require_once __DIR__ . "/config.php";

/* ==========================================
   CONEXIÓN A LA BASE DE DATOS
========================================== */

try {

    $dsn = "mysql:host=" . DB_HOST .
        ";dbname=" . DB_NAME .
        ";charset=utf8mb4";

    $pdo = new PDO(
        $dsn,
        DB_USER,
        DB_PASS
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    $pdo->setAttribute(
        PDO::ATTR_EMULATE_PREPARES,
        false
    );
} catch (PDOException $e) {

    die("
        <h2>Error de conexión</h2>
        <p>No fue posible conectar con la base de datos.</p>
    ");
}
