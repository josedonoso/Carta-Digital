<?php
session_start();

require_once "../includes/conexion.php";
require_once "../includes/helpers.php";

$error = "";

/* ==========================================
   PROCESAR LOGIN
========================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($usuario === "" || $password === "") {

        $error = "Debes ingresar usuario y contraseña.";

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM usuarios
            WHERE usuario = ?
            AND activo = 1
        ");

        $stmt->execute([$usuario]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {

            $_SESSION["admin_id"] = $user["id"];
            $_SESSION["admin_nombre"] = $user["nombre"];

            redireccionar("dashboard.php");
        }

        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Administrador</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/login.css" rel="stylesheet">

</head>

<body>

<div class="login-container">

    <div class="login-card shadow">

        <h2 class="mb-2">
            🍽️ Carta Digital
        </h2>

        <p class="text-muted mb-4">
            Panel de Administración
        </p>

        <?php if($error): ?>

            <div class="alert alert-danger">
                <?= limpiarTexto($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Usuario
                </label>

                <input
                    type="text"
                    name="usuario"
                    class="form-control"
                    placeholder="Ingrese su usuario"
                    required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Contraseña
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Ingrese su contraseña"
                    required>

            </div>

            <button class="btn btn-warning w-100 btn-lg">

                Ingresar

            </button>

        </form>

        <hr>

        <a href="../carta.php" class="btn btn-outline-light w-100">

            Ver Carta Digital

        </a>

    </div>

</div>

</body>

</html>