<?php
require_once "includes/conexion.php";
require_once "includes/helpers.php";

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
    SELECT *
    FROM productos
    WHERE activo = 1
    ORDER BY destacado DESC, nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* ==========================================
   AGRUPAR PRODUCTOS POR CATEGORÍA
========================================== */
$productosPorCategoria = [];

foreach ($productos as $producto) {
    $productosPorCategoria[$producto["categoria_id"]][] = $producto;
}

/* ==========================================
   CARGAR PRECIOS VARIABLES
========================================== */
$precios = $pdo->query("
    SELECT *
    FROM producto_precios
    WHERE activo = 1
    ORDER BY orden ASC
")->fetchAll(PDO::FETCH_ASSOC);

$preciosPorProducto = [];

foreach ($precios as $precio) {
    $preciosPorProducto[$precio["producto_id"]][] = $precio;
}

/* ==========================================
   CARGAR SABORES ACTIVOS
========================================== */
$saboresHelado = $pdo->query("
    SELECT *
    FROM sabores_helado
    WHERE activo = 1
    ORDER BY orden ASC, nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);

$saboresJugo = $pdo->query("
    SELECT *
    FROM sabores_jugo
    WHERE activo = 1
    ORDER BY orden ASC, nombre ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carta Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/carta.css" rel="stylesheet">
</head>

<body>

<div class="container py-4">

    <div class="text-center mb-4">
        <h1 class="fw-bold text-warning">Carta Digital</h1>
        <p class="text-light mb-1">Restaurante & Cafetería</p>
        <p class="text-secondary">Pide al garzón y paga presencial con Klap.</p>
    </div>

    <div class="categoria-nav mb-4">
        <button class="btn btn-warning btn-sm filtro-categoria" data-categoria="todas">
            Todas
        </button>

        <?php foreach ($categorias as $categoria): ?>
            <button class="btn btn-outline-warning btn-sm filtro-categoria"
                data-categoria="<?= (int) $categoria["id"] ?>">
                <?= limpiarTexto($categoria["nombre"]) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($categorias as $categoria): ?>

        <section class="bloque-categoria" data-categoria="<?= (int) $categoria["id"] ?>">

            <h4 class="mt-4 mb-3 categoria-titulo">
                <?= limpiarTexto($categoria["nombre"]) ?>
            </h4>

            <?php if (!empty($productosPorCategoria[$categoria["id"]])): ?>

                <div class="row g-3">

                    <?php foreach ($productosPorCategoria[$categoria["id"]] as $producto): ?>
                        <?php
                        $productoId = (int) $producto["id"];
                        $esProductoHelado = esHelado($producto["nombre"]);
                        $esProductoJugo = esJugoNatural($producto["nombre"]);
                        ?>

                        <div class="col-12">
                            <div class="card producto-card shadow-sm">
                                <div class="card-body p-2">

                                    <div class="producto-horizontal">

                                        <div class="producto-foto">
                                            <?php if (!empty($producto["imagen"])): ?>
                                                <img src="uploads/productos/<?= limpiarTexto($producto["imagen"]) ?>"
                                                     alt="<?= limpiarTexto($producto["nombre"]) ?>">
                                            <?php else: ?>
                                                <div class="sin-foto">Sin foto</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="producto-info">

                                            <h5 class="card-title mb-1">
                                                <?= limpiarTexto($producto["nombre"]) ?>

                                                <?php if ($producto["destacado"] == 1): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        Destacado
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ($producto["agotado"] == 1): ?>
                                                    <span class="badge bg-danger">
                                                        Agotado
                                                    </span>
                                                <?php endif; ?>
                                            </h5>

                                            <p class="card-text mb-2">
                                                <?= limpiarTexto($producto["descripcion"]) ?>
                                            </p>

                                            <?php if ($esProductoHelado && !empty($saboresHelado)): ?>
                                                <div class="sabores-producto">
                                                    <strong>Sabores disponibles:</strong>

                                                    <div class="sabores-lista">
                                                        <?php foreach ($saboresHelado as $sabor): ?>
                                                            <span class="sabor-chip">
                                                                <?= limpiarTexto($sabor["nombre"]) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($esProductoJugo && !empty($saboresJugo)): ?>
                                                <div class="sabores-producto">
                                                    <strong>Sabores disponibles:</strong>

                                                    <div class="sabores-lista">
                                                        <?php foreach ($saboresJugo as $sabor): ?>
                                                            <span class="sabor-chip">
                                                                <?= limpiarTexto($sabor["nombre"]) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($producto["agotado"] == 1): ?>

                                                <strong class="text-danger fs-5">
                                                    No disponible
                                                </strong>

                                            <?php elseif (!empty($preciosPorProducto[$productoId])): ?>

                                                <div class="text-success fs-5">
                                                    <?php foreach ($preciosPorProducto[$productoId] as $variante): ?>
                                                        <div class="precio-variante">
                                                            <span><?= limpiarTexto($variante["nombre"]) ?></span>
                                                            <strong><?= formatoPrecio($variante["precio"]) ?></strong>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                            <?php elseif ($producto["precio"] > 0): ?>

                                                <strong class="text-success fs-5">
                                                    <?= formatoPrecio($producto["precio"]) ?>
                                                </strong>

                                            <?php else: ?>

                                                <strong class="text-secondary">
                                                    Consultar precio
                                                </strong>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="text-secondary">Próximamente.</p>

            <?php endif; ?>

        </section>

    <?php endforeach; ?>

    <div class="text-center mt-5 mb-3">
        <p class="text-secondary">Gracias por visitarnos.</p>
    </div>

</div>

<a href="#" class="volver-arriba">↑</a>

<script src="assets/js/carta.js"></script>

</body>

</html>