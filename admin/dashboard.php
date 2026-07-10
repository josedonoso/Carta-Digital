<?php
require_once "../includes/auth.php";
require_once "../includes/helpers.php";

$adminNombre = $_SESSION["admin_nombre"] ?? "Administrador";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span class="navbar-brand">Carta Digital</span>

            <a href="logout.php" class="btn btn-outline-light btn-sm">
                Cerrar sesión
            </a>
        </div>
    </nav>

    <div class="container py-4">

        <h2>Hola, <?= limpiarTexto($adminNombre) ?></h2>
        <p class="text-muted">Panel de administración de la carta.</p>

        <div class="row g-3 mt-3">

            <div class="col-md-6">
                <a href="categorias.php" class="text-decoration-none text-dark">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4>Categorías</h4>

                            <p class="text-muted mb-0">
                                Agregar o editar secciones de la carta.
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="productos.php" class="text-decoration-none text-dark">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4>Productos</h4>

                            <p class="text-muted mb-0">
                                Agregar platos, cafés, precios y disponibilidad.
                            </p>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="mt-4">
            <a href="../carta.php" target="_blank" class="btn btn-warning">
                Ver carta pública
            </a>
        </div>

    </div>

</body>

</html>