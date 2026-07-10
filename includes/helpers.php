<?php

/* ==========================================
   LIMPIAR TEXTO PARA MOSTRAR EN HTML
========================================== */
function limpiarTexto($texto)
{
    return htmlspecialchars($texto ?? "", ENT_QUOTES, "UTF-8");
}

/* ==========================================
   FORMATO PRECIO CHILENO
========================================== */
function formatoPrecio($precio)
{
    return '$' . number_format((int) $precio, 0, ',', '.');
}

/* ==========================================
   DETECTAR SI UN PRODUCTO ES HELADO
========================================== */
function esHelado($nombre)
{
    $nombre = mb_strtolower($nombre ?? "", "UTF-8");

    return str_contains($nombre, "helado");
}

/* ==========================================
   REDIRECCIONAR
========================================== */
function redireccionar($url)
{
    header("Location: " . $url);
    exit;
}

/* ==========================================
   VALIDAR EXTENSIÓN DE IMAGEN
========================================== */
function esImagenPermitida($nombreArchivo)
{
    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
    $permitidas = ["jpg", "jpeg", "png", "webp"];

    return in_array($extension, $permitidas);
}

/* ==========================================
   SUBIR IMAGEN DE PRODUCTO
========================================== */
function subirImagenProducto($archivo)
{
    if (empty($archivo["name"])) {
        return null;
    }

    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!esImagenPermitida($archivo["name"])) {
        return null;
    }

    $carpetaDestino = __DIR__ . "/../uploads/productos/";

    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $nombreImagen = basename($archivo["name"]);
    $nombreImagen = strtolower(str_replace(" ", "_", $nombreImagen));

    $rutaFinal = $carpetaDestino . $nombreImagen;

    if (!file_exists($rutaFinal)) {
        if (!move_uploaded_file($archivo["tmp_name"], $rutaFinal)) {
            return null;
        }
    }

    return $nombreImagen;
}

function esJugoNatural($nombre)
{
    $nombre = mb_strtolower($nombre ?? "", "UTF-8");

    return str_contains($nombre, "jugo natural")
        || str_contains($nombre, "jugos naturales")
        || str_contains($nombre, "jugo");
}