<?php
require_once "../includes/auth.php";
require_once "../includes/conexion.php";
require_once "../includes/helpers.php";

/* ==========================================
   VALIDAR ID
========================================== */
if (!isset($_GET["id"])) {
    header("Location: productos.php");
    exit;
}

$id = (int) $_GET["id"];
$urlEditar = "editar_productos.php?id=" . $id;
$mensaje = "";

/* ==========================================
   OBTENER PRODUCTO
========================================== */
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: productos.php");
    exit;
}

$esHeladoArtesanal = esHelado($producto["nombre"]);
$esJugoNatural = esJugoNatural($producto["nombre"]);
$tieneSabores = $esHeladoArtesanal || $esJugoNatural;

/* ==========================================
   CAMBIAR ESTADO DEL SABOR
========================================== */
if (isset($_GET["cambiar_sabor"]) && $tieneSabores) {
    $saborId = (int) $_GET["cambiar_sabor"];

    $tablaSabores = $esHeladoArtesanal ? "sabores_helado" : "sabores_jugo";

    $stmt = $pdo->prepare("
        UPDATE {$tablaSabores}
        SET activo = IF(activo = 1, 0, 1)
        WHERE id = ?
    ");
    $stmt->execute([$saborId]);

    header("Location: $urlEditar");
    exit;
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
   CARGAR DATOS DE SABORES / PRECIOS
========================================== */
$preciosHelado = [];
$sabores = [];

if ($tieneSabores) {
    if ($esHeladoArtesanal) {
        $stmtPrecios = $pdo->prepare("
            SELECT nombre, precio
            FROM producto_precios
            WHERE producto_id = ?
        ");
        $stmtPrecios->execute([$id]);

        foreach ($stmtPrecios->fetchAll(PDO::FETCH_ASSOC) as $precioItem) {
            $preciosHelado[$precioItem["nombre"]] = $precioItem["precio"];
        }
    }

    $tablaSabores = $esHeladoArtesanal ? "sabores_helado" : "sabores_jugo";

    $sabores = $pdo->query("
        SELECT *
        FROM {$tablaSabores}
        ORDER BY activo DESC, orden ASC, nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

/* ==========================================
   PROCESAR FORMULARIO
========================================== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = $_POST["accion"] ?? "guardar";

    /* Agregar sabor */
    if ($accion === "agregar_sabor" && $tieneSabores) {
        $nuevoSabor = trim($_POST["nuevo_sabor"] ?? "");
        $tablaSabores = $esHeladoArtesanal ? "sabores_helado" : "sabores_jugo";

        if ($nuevoSabor !== "") {
            $stmt = $pdo->prepare("
                INSERT INTO {$tablaSabores} (nombre, orden, activo)
                VALUES (?, 0, 1)
            ");
            $stmt->execute([$nuevoSabor]);
        }

        header("Location: $urlEditar");
        exit;
    }

    /* Datos principales */
    $categoriaId = (int) ($_POST["categoria_id"] ?? 0);
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    $esHeladoPost = esHelado($nombre);
    $esJugoPost = esJugoNatural($nombre);

    $precio = ($esHeladoPost || $esJugoPost) ? 0 : (int) ($_POST["precio"] ?? 0);

    $destacado = isset($_POST["destacado"]) ? 1 : 0;
    $agotado = isset($_POST["agotado"]) ? 1 : 0;
    $activo = isset($_POST["activo"]) ? 1 : 0;

    $imagenNombre = $producto["imagen"];

    /* Subir imagen */
    if (!empty($_FILES["imagen"]["name"])) {
        $nuevaImagen = subirImagenProducto($_FILES["imagen"]);

        if ($nuevaImagen !== null) {
            $carpetaDestino = __DIR__ . "/../uploads/productos/";

            if (!empty($producto["imagen"]) && file_exists($carpetaDestino . $producto["imagen"])) {
                unlink($carpetaDestino . $producto["imagen"]);
            }

            $imagenNombre = $nuevaImagen;
        }
    }

    /* Guardar producto */
    if ($categoriaId > 0 && $nombre !== "") {
        $sql = "UPDATE productos
                SET categoria_id = ?,
                    nombre = ?,
                    descripcion = ?,
                    precio = ?,
                    imagen = ?,
                    destacado = ?,
                    agotado = ?,
                    activo = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $categoriaId,
            $nombre,
            $descripcion,
            $precio,
            $imagenNombre,
            $destacado,
            $agotado,
            $activo,
            $id
        ]);

        /* Guardar precios de helado */
        if ($esHeladoPost) {
            $simple = (int) ($_POST["precio_simple"] ?? 0);
            $doble = (int) ($_POST["precio_doble"] ?? 0);

            $pdo->prepare("
                DELETE FROM producto_precios
                WHERE producto_id = ?
            ")->execute([$id]);

            $stmtPrecio = $pdo->prepare("
                INSERT INTO producto_precios
                (producto_id, nombre, precio, orden, activo)
                VALUES (?, ?, ?, ?, 1)
            ");

            $stmtPrecio->execute([$id, "Simple", $simple, 1]);
            $stmtPrecio->execute([$id, "Doble", $doble, 2]);
        }

        header("Location: productos.php");
        exit;
    }

    $mensaje = "Debes completar nombre y categoría.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/editar_productos.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a href="productos.php" class="navbar-brand">← Productos</a>

        <a href="logout.php" class="btn btn-outline-light btn-sm">
            Cerrar sesión
        </a>
    </div>
</nav>

<div class="container py-4">

    <h2>Editar producto</h2>
    <p class="text-muted">Modifica los datos del producto seleccionado.</p>

    <?php if ($mensaje): ?>
        <div class="alert alert-warning">
            <?= limpiarTexto($mensaje) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" enctype="multipart/form-data" class="row g-3">

                <!-- INFORMACIÓN DEL PRODUCTO -->
                <div class="col-md-4">
                    <label class="form-label">Categoría</label>

                    <select name="categoria_id" class="form-select" required>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= (int) $categoria["id"] ?>"
                                <?= $producto["categoria_id"] == $categoria["id"] ? "selected" : "" ?>>
                                <?= $categoria["tipo"] === "cafeteria" ? "Cafetería" : "Restaurante" ?>
                                -
                                <?= limpiarTexto($categoria["nombre"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nombre producto</label>

                    <input type="text" name="nombre" class="form-control"
                        value="<?= limpiarTexto($producto["nombre"]) ?>" required>
                </div>

                <!-- PRECIOS -->
                <?php if (!$esHeladoArtesanal && !$esJugoNatural): ?>
                    <div class="col-md-4">
                        <label class="form-label">Precio</label>

                        <input type="number" name="precio" class="form-control" min="0"
                            value="<?= limpiarTexto($producto["precio"]) ?>">
                    </div>
                <?php elseif ($esHeladoArtesanal): ?>
                    <div class="col-md-2">
                        <label class="form-label">Precio simple</label>

                        <input type="number" name="precio_simple" class="form-control" min="0"
                            value="<?= limpiarTexto($preciosHelado["Simple"] ?? 0) ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Precio doble</label>

                        <input type="number" name="precio_doble" class="form-control" min="0"
                            value="<?= limpiarTexto($preciosHelado["Doble"] ?? 0) ?>">
                    </div>
                <?php else: ?>
                    <div class="col-md-4">
                        <label class="form-label">Precio</label>

                        <input type="number" class="form-control" value="0" disabled>

                        <small class="text-muted">
                            Los jugos naturales se administran por sabores.
                        </small>
                    </div>
                <?php endif; ?>

                <!-- DESCRIPCIÓN -->
                <div class="col-12">
                    <label class="form-label">Descripción</label>

                    <textarea name="descripcion" class="form-control"
                        rows="3"><?= limpiarTexto($producto["descripcion"]) ?></textarea>
                </div>

                <!-- SABORES -->
                <?php if ($tieneSabores): ?>
                    <div class="col-12">
                        <div class="card <?= $esHeladoArtesanal ? 'card-helados border-warning' : 'card-jugos border-success' ?>">
                            <div class="card-header <?= $esHeladoArtesanal ? 'bg-warning text-dark' : 'bg-success text-white' ?> fw-bold">
                                <?= $esHeladoArtesanal ? "Sabores del helado" : "Sabores del jugo natural" ?>
                            </div>

                            <div class="card-body">
                                <?php if (!empty($sabores)): ?>
                                    <div class="row g-2 mb-3">
                                        <?php foreach ($sabores as $sabor): ?>
                                            <div class="col-md-4">
                                                <div class="sabor-card <?= $sabor["activo"] == 1 ? "" : "sabor-oculto" ?>">
                                                    <span>
                                                        <?= $esHeladoArtesanal ? "🍦" : "🥤" ?>
                                                        <?= limpiarTexto($sabor["nombre"]) ?>
                                                    </span>

                                                    <a href="editar_productos.php?id=<?= $id ?>&cambiar_sabor=<?= (int) $sabor["id"] ?>"
                                                       class="btn-sabor">
                                                        <?= $sabor["activo"] == 1 ? "Ocultar" : "Mostrar" ?>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">
                                        No hay sabores registrados.
                                    </p>
                                <?php endif; ?>

                                <label class="form-label">Agregar nuevo sabor</label>

                                <div class="input-group">
                                    <input type="text" name="nuevo_sabor" class="form-control"
                                        placeholder="<?= $esHeladoArtesanal ? 'Ej: Chocolate, Vainilla, Frutilla' : 'Ej: Frutilla, Mango, Piña' ?>">

                                    <button type="submit" name="accion" value="agregar_sabor"
                                        class="btn <?= $esHeladoArtesanal ? 'btn-warning' : 'btn-success' ?>">
                                        + Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- IMAGEN -->
                <div class="col-md-6">
                    <label class="form-label">Imagen actual</label>
                    <br>

                    <?php if (!empty($producto["imagen"])): ?>
                        <img src="../uploads/productos/<?= limpiarTexto($producto["imagen"]) ?>"
                            class="producto-imagen-preview" alt="Imagen actual del producto">
                    <?php else: ?>
                        <p class="text-muted">Sin imagen</p>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cambiar imagen</label>

                    <input type="file" name="imagen" class="form-control" accept="image/*">

                    <small class="text-muted">
                        Si no seleccionas una nueva imagen, se mantiene la actual.
                    </small>
                </div>

                <!-- OPCIONES -->
                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" name="destacado" class="form-check-input" id="destacado"
                            <?= $producto["destacado"] == 1 ? "checked" : "" ?>>

                        <label class="form-check-label" for="destacado">
                            Destacado
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" name="agotado" class="form-check-input" id="agotado"
                            <?= $producto["agotado"] == 1 ? "checked" : "" ?>>

                        <label class="form-check-label" for="agotado">
                            Agotado
                        </label>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" name="activo" class="form-check-input" id="activo"
                            <?= $producto["activo"] == 1 ? "checked" : "" ?>>

                        <label class="form-check-label" for="activo">
                            Visible en carta
                        </label>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="col-12 text-end acciones-finales">
                    <a href="productos.php" class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" name="accion" value="guardar" class="btn btn-warning">
                        Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>

</html>