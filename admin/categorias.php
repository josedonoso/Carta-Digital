<?php
require_once "../includes/auth.php";
require_once "../includes/conexion.php";
require_once "../includes/helpers.php";

$mensaje = "";

/* AGREGAR CATEGORÍA */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["agregar"])) {
    $nombre = trim($_POST["nombre"] ?? "");
    $tipo = $_POST["tipo"] ?? "";
    $orden = (int) ($_POST["orden"] ?? 1);

    if ($nombre !== "" && in_array($tipo, ["restaurante", "cafeteria"])) {
        $stmt = $pdo->prepare("
            INSERT INTO categorias (nombre, tipo, orden, activo)
            VALUES (?, ?, ?, 1)
        ");
        $stmt->execute([$nombre, $tipo, $orden]);

        $mensaje = "Categoría agregada correctamente.";
    } else {
        $mensaje = "Debes completar los datos correctamente.";
    }
}

/* ACTIVAR / OCULTAR */
if (isset($_GET["cambiar_estado"])) {
    $id = (int) $_GET["cambiar_estado"];

    $stmt = $pdo->prepare("
        UPDATE categorias
        SET activo = IF(activo = 1, 0, 1)
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    redireccionar("categorias.php");
}

/* ELIMINAR */
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM productos 
        WHERE categoria_id = ?
    ");
    $stmt->execute([$id]);
    $totalProductos = (int) $stmt->fetchColumn();

    if ($totalProductos > 0) {
        $mensaje = "No se puede eliminar porque tiene productos asociados. Mejor ocúltala.";
    } else {
        $stmt = $pdo->prepare("
            DELETE FROM categorias
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        redireccionar("categorias.php");
    }
}

/* CARGAR CATEGORÍAS */
$categorias = $pdo->query("
    SELECT *
    FROM categorias
    ORDER BY tipo ASC, orden ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Categorías</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/categorias.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a href="dashboard.php" class="navbar-brand">← Panel</a>

        <a href="logout.php" class="btn btn-outline-light btn-sm">
            Cerrar sesión
        </a>
    </div>
</nav>

<div class="container py-4">

    <h2>Categorías</h2>
    <p class="text-muted">Administra las secciones de restaurante y cafetería.</p>

    <?php if ($mensaje): ?>
        <div class="alert alert-info">
            <?= limpiarTexto($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Agregar categoría</h5>

            <form method="POST" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="cafeteria">Cafetería</option>
                        <option value="restaurante">Restaurante</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Orden</label>
                    <input type="number" name="orden" class="form-control" value="1" min="1">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="agregar" class="btn btn-warning w-100">
                        Agregar
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Listado de categorías</h5>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= limpiarTexto($categoria["nombre"]) ?></td>

                                <td>
                                    <?= $categoria["tipo"] === "cafeteria" ? "Cafetería" : "Restaurante" ?>
                                </td>

                                <td><?= (int) $categoria["orden"] ?></td>

                                <td>
                                    <?php if ($categoria["activo"] == 1): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Oculta</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">

                                    <a href="editar_categorias.php?id=<?= (int) $categoria["id"] ?>"
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>

                                    <?php if ($categoria["activo"] == 1): ?>
                                        <a href="categorias.php?cambiar_estado=<?= (int) $categoria["id"] ?>"
                                           class="btn btn-sm btn-secondary"
                                           onclick="return confirm('¿Deseas ocultar esta categoría?')">
                                            Ocultar
                                        </a>
                                    <?php else: ?>
                                        <a href="categorias.php?cambiar_estado=<?= (int) $categoria["id"] ?>"
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('¿Deseas activar esta categoría?')">
                                            Activar
                                        </a>
                                    <?php endif; ?>

                                    <a href="categorias.php?eliminar=<?= (int) $categoria["id"] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                        Eliminar
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($categorias)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No hay categorías registradas.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>