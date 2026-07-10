<?php
require_once "../includes/auth.php";
require_once "../includes/conexion.php";
require_once "../includes/helpers.php";

$mensaje = "";

/* ==========================================
   AGREGAR PRODUCTO
========================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["agregar"])) {
    $categoriaId = (int) ($_POST["categoria_id"] ?? 0);
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = (int) ($_POST["precio"] ?? 0);
    $destacado = isset($_POST["destacado"]) ? 1 : 0;
    $agotado = isset($_POST["agotado"]) ? 1 : 0;

    $imagenNombre = subirImagenProducto($_FILES["imagen"] ?? []);

    if ($categoriaId > 0 && $nombre !== "") {
        $sql = "INSERT INTO productos
                (categoria_id, nombre, descripcion, precio, imagen, destacado, agotado, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $categoriaId,
            $nombre,
            $descripcion,
            $precio,
            $imagenNombre,
            $destacado,
            $agotado
        ]);

        $mensaje = "Producto agregado correctamente.";
    } else {
        $mensaje = "Debes completar nombre y categoría.";
    }
}

/* ==========================================
   ACTIVAR / OCULTAR PRODUCTO
========================================== */
if (isset($_GET["cambiar_estado"])) {
    $id = (int) $_GET["cambiar_estado"];

    $stmt = $pdo->prepare("
        UPDATE productos
        SET activo = IF(activo = 1, 0, 1)
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    redireccionar("productos.php");
}

/* ==========================================
   CAMBIAR AGOTADO
========================================== */
if (isset($_GET["cambiar_agotado"])) {
    $id = (int) $_GET["cambiar_agotado"];

    $stmt = $pdo->prepare("
        UPDATE productos
        SET agotado = IF(agotado = 1, 0, 1)
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    redireccionar("productos.php");
}

/* ==========================================
   ELIMINAR PRODUCTO
========================================== */
if (isset($_GET["eliminar"])) {
    $id = (int) $_GET["eliminar"];

    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);

    redireccionar("productos.php");
}

/* ==========================================
   CARGAR CATEGORÍAS
========================================== */
$categorias = $pdo->query("
    SELECT *
    FROM categorias
    WHERE activo = 1
    ORDER BY tipo ASC, orden ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* ==========================================
   CARGAR PRODUCTOS
========================================== */
$productos = $pdo->query("
    SELECT p.*, c.nombre AS categoria, c.tipo
    FROM productos p
    INNER JOIN categorias c ON p.categoria_id = c.id
    ORDER BY c.tipo ASC, c.orden ASC, p.nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/productos.css" rel="stylesheet">
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

        <h2>Productos</h2>
        <p class="text-muted">Administra platos, cafés, pasteles y precios.</p>

        <?php if ($mensaje): ?>
            <div class="alert alert-info">
                <?= limpiarTexto($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5>Agregar producto</h5>

                <form method="POST" enctype="multipart/form-data" class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">Categoría</label>

                        <select name="categoria_id" class="form-select" required>
                            <option value="">Seleccione...</option>

                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria["id"] ?>">
                                    <?= $categoria["tipo"] === "cafeteria" ? "Cafetería" : "Restaurante" ?>
                                    -
                                    <?= limpiarTexto($categoria["nombre"]) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nombre producto</label>

                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Precio</label>

                        <input type="number" name="precio" class="form-control" value="0" min="0">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>

                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Imagen del producto</label>

                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="destacado" class="form-check-input" id="destacado">

                            <label class="form-check-label" for="destacado">
                                Destacado
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="agotado" class="form-check-input" id="agotado">

                            <label class="form-check-label" for="agotado">
                                Agotado
                            </label>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" name="agregar" class="btn btn-warning">
                            Agregar producto
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Listado de productos</h5>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Agotado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($producto["imagen"])): ?>
                                            <img src="../uploads/productos/<?= limpiarTexto($producto["imagen"]) ?>"
                                                class="producto-img-tabla" alt="Imagen del producto">
                                        <?php else: ?>
                                            <span class="text-muted">Sin imagen</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <strong><?= limpiarTexto($producto["nombre"]) ?></strong>

                                        <?php if (!empty($producto["descripcion"])): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= limpiarTexto($producto["descripcion"]) ?>
                                            </small>
                                        <?php endif; ?>

                                        <?php if ($producto["destacado"] == 1): ?>
                                            <br>
                                            <span class="badge bg-warning text-dark">
                                                Destacado
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= $producto["tipo"] === "cafeteria" ? "Cafetería" : "Restaurante" ?>

                                        <br>

                                        <small class="text-muted">
                                            <?= limpiarTexto($producto["categoria"]) ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?php if ($producto["precio"] > 0): ?>
                                            <?= formatoPrecio($producto["precio"]) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Consultar</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($producto["activo"] == 1): ?>
                                            <span class="badge bg-success">Visible</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Oculto</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($producto["agotado"] == 1): ?>
                                            <span class="badge bg-danger">Sí</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">No</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <a href="editar_productos.php?id=<?= (int) $producto["id"] ?>"
                                            class="btn btn-sm btn-outline-success">
                                            Editar
                                        </a>

                                        <a href="productos.php?cambiar_agotado=<?= (int) $producto["id"] ?>"
                                            class="btn btn-sm btn-outline-warning">
                                            Agotado
                                        </a>

                                        <a href="productos.php?cambiar_estado=<?= (int) $producto["id"] ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            Mostrar/Ocultar
                                        </a>

                                        <a href="productos.php?eliminar=<?= (int) $producto["id"] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Seguro que deseas eliminar este producto?')">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($productos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No hay productos registrados.
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