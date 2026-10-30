<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error"; // pasa a "ok" solo si la operación sale bien

$accion    = $_POST["accion"] ?? " ";

// Guardián: solo POST y solo acciones conocidas
if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    // ---------- Datos del formulario ----------
    $id               = (int) ($_POST["id"] ?? 0);
    $nombre_categoria = trim($_POST["nombre_categoria"] ?? "");
    $descripcion      = trim($_POST["descripcion"] ?? "");
    $privado          = isset($_POST["privado"]);   // true si tildó "Mantener privado"
    $estado_activo    = $privado ? 0 : 1;           // privado = no visible = desactivado

    try {
        // =================================================
        // BORRAR: baja lógica (nunca DELETE)
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una categoría";
            } else {
                $stmt = $pdo->prepare("UPDATE CATEGORIA_DOCUMENTO SET estado_activo = 0
                                       WHERE id_categoria = :id AND estado_activo = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Categoría eliminada correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró una categoría activa para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($nombre_categoria == "") {
            $mensaje = "Falta el nombre de la categoría";
        } elseif ($descripcion == "") {
            $mensaje = "Añada una descripción";

        // =================================================
        // MODIFICAR: solo cambia los datos, no la imagen
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una categoría";
            } else {
                $sql = "UPDATE CATEGORIA_DOCUMENTO
                        SET nombre_categoria = :nombre_categoria, descripcion = :descripcion
                        WHERE id_categoria = :id AND estado_activo = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "nombre_categoria" => $nombre_categoria,
                    "descripcion"      => $descripcion,
                    "id"               => $id
                ]);
                $mensaje   = "Categoría modificada correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR: acá sí se exige y se guarda la imagen
        // =================================================
        } elseif (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
            $mensaje = "Falta seleccionar una imagen válida";
        } else {
            $archivo = $_FILES["logo"];

            // Se mira el contenido real del archivo, no el nombre
            $tiposPermitidos = ["image/jpeg" => "jpg", "image/png" => "png"]; // tipo real => extensión
            $tipoReal = mime_content_type($archivo["tmp_name"]);

            if (!isset($tiposPermitidos[$tipoReal])) {
                $mensaje = "El archivo debe ser una imagen (jpg o png)";
            } else {
                $nombreArchivo = uniqid("cat_") . "." . $tiposPermitidos[$tipoReal];
                $rutaDestino   = __DIR__ . "/Categoria_Logo/" . $nombreArchivo;
                $rutaWeb       = "Categoria_Logo/" . $nombreArchivo;

                if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                    $mensaje = "Error al guardar la imagen";
                } else {
                    $sql = "INSERT INTO CATEGORIA_DOCUMENTO (nombre_categoria, descripcion, estado_activo, archivo_url)
                            VALUES (:nombre_categoria, :descripcion, :estado_activo, :archivo_url)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "nombre_categoria" => $nombre_categoria,
                        "descripcion"      => $descripcion,
                        "estado_activo"    => $estado_activo,
                        "archivo_url"      => $rutaWeb
                    ]);
                    $mensaje   = "Categoría registrada correctamente";
                    $resultado = "ok";
                }
            }
        }
    } catch (PDOException $e) {
        if ($e->getCode() == "23000") {
            $mensaje = "Ya existe una categoría con ese nombre";
        } else {
            error_log($e->getMessage()); // el detalle va al log, no a la pantalla
            $mensaje = "Error al procesar la solicitud";
        }
    }

    // Lo que lee Panel_Admin.php
    $_SESSION["mensaje_cat_doc"]   = $mensaje;
    $_SESSION["accion_cat_doc"]    = $accion;     // crear | modificar | borrar
    $_SESSION["resultado_cat_doc"] = $resultado;  // ok | error
}

header("Location: Panel_Admin.php");
exit;
