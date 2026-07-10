<?php
require_once "../includes/auth.php";
require_once "../includes/conexion.php";
require_once "../includes/helpers.php";

if (!isset($_GET["id"])) {
    redireccionar("categorias.php");
}

$id = (int) $_GET["id"];
$mensaje = "";

$stmt = $pdo->prepare("
    SELECT *
    FROM categorias
    WHERE id = ?
");
$stmt->execute([$id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    redireccionar("categorias.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $tipo = $_POST["tipo"] ?? "";
    $orden = (int) ($_POST["orden"] ?? 1);
    $activo = isset($_POST["activo"]) ? 1 : 0;

    if ($nombre !== "" && in_array($tipo, ["restaurante", "cafeteria"])) {
        $stmt = $pdo->prepare("
            UPDATE categorias
            SET nombre = ?,
                tipo = ?,
                orden = ?,
                activo = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $nombre,
            $tipo,
            $orden,
            $activo,
            $id
        ]);

        redireccionar("categorias.php");
    } else {
        $mensaje = "Debes completar los datos correctamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar categoría</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/editar_categorias.css" rel="stylesheet">

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a href="categorias.php" class="navbar-brand">← Categorías</a>

        <a href="logout.php" class="btn btn-outline-light btn-sm">
            Cerrar sesión
        </a>
    </div>
</nav>

<div class="container py-4">

    <h2>Editar categoría</h2>
    <p class="text-muted">Modifica los datos de la categoría seleccionada.</p>

    <?php if ($mensaje): ?>
        <div class="alert alert-warning">
            <?= limpiarTexto($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" class="row g-3">

                <div class="col-md-5">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= limpiarTexto($categoria["nombre"]) ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="cafeteria" <?= $categoria["tipo"] === "cafeteria" ? "selected" : "" ?>>
                            Cafetería
                        </option>

                        <option value="restaurante" <?= $categoria["tipo"] === "restaurante" ? "selected" : "" ?>>
                            Restaurante
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Orden</label>
                    <input type="number" name="orden" class="form-control" min="1"
                           value="<?= (int) $categoria["orden"] ?>">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="activo" class="form-check-input" id="activo"
                               <?= $categoria["activo"] == 1 ? "checked" : "" ?>>

                        <label class="form-check-label" for="activo">
                            Activa
                        </label>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="categorias.php" class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-warning">
                        Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>