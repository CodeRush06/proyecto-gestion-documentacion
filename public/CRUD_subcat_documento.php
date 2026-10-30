<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error"; // pasa a "ok" solo si la operación sale bien
$accion    = $_POST["accion"] ?? " ";
// Guardián: solo POST y solo acciones conocidas
if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    // ---------- Datos del formulario ----------
    $id                  = (int) ($_POST["id"] ?? 0);
    $nombre_subcategoria = trim($_POST["nombre_subcategoria"] ?? "");
    $descripcion         = trim($_POST["descripcion"] ?? "");
    $id_categoria        = (int) ($_POST["id_categoria"] ?? 0);
    $privado             = isset($_POST["privado"]);
    $estado_activo       = $privado ? 0 : 1;   // privado = no visible = desactivado

    $rutaDestino = ""; // si el INSERT falla, se borra la imagen ya subida

    try {
        // =================================================
        // BORRAR: baja lógica
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una subcategoría";
            } else {
                $stmt = $pdo->prepare("UPDATE subcategoria_documento SET estado_activo = 0
                                       WHERE id_subcategoria = :id AND estado_activo = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Subcategoría eliminada correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró una subcategoría activa para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($nombre_subcategoria == "") {
            $mensaje = "Falta el nombre de la subcategoría";
        } elseif ($descripcion == "") {
            $mensaje = "Añada una descripción";
        } elseif ($id_categoria <= 0) {
            $mensaje = "Seleccione una categoría";

        // =================================================
        // MODIFICAR: solo datos, no la imagen
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una subcategoría";
            } else {
                $sql = "UPDATE subcategoria_documento
                        SET nombre_subcategoria = :nombre, descripcion = :descripcion, id_categoria = :id_categoria
                        WHERE id_subcategoria = :id AND estado_activo = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "nombre"       => $nombre_subcategoria,
                    "descripcion"  => $descripcion,
                    "id_categoria" => $id_categoria,
                    "id"           => $id
                ]);
                $mensaje   = "Subcategoría modificada correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR: acá sí se exige y se guarda la imagen
        // =================================================
        } elseif (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
            $mensaje = "Falta seleccionar una imagen válida";
        } else {
            $archivo = $_FILES["logo"];

            $tiposPermitidos = ["image/jpeg" => "jpg", "image/png" => "png"]; // tipo real => extensión
            $tipoReal = mime_content_type($archivo["tmp_name"]);

            if (!isset($tiposPermitidos[$tipoReal])) {
                $mensaje = "El archivo debe ser una imagen (jpg o png)";
            } else {
                $nombreArchivo = uniqid("subcat_") . "." . $tiposPermitidos[$tipoReal];
                $rutaDestino   = __DIR__ . "/SubCategoria_Logo/" . $nombreArchivo;
                $rutaWeb       = "SubCategoria_Logo/" . $nombreArchivo;

                if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                    $mensaje     = "Error al guardar la imagen";
                    $rutaDestino = "";
                } else {
                    $sql = "INSERT INTO subcategoria_documento
                                (nombre_subcategoria, descripcion, id_categoria, estado_activo, archivo_url)
                            VALUES (:nombre, :descripcion, :id_categoria, :estado_activo, :archivo_url)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "nombre"        => $nombre_subcategoria,
                        "descripcion"   => $descripcion,
                        "id_categoria"  => $id_categoria,
                        "estado_activo" => $estado_activo,
                        "archivo_url"   => $rutaWeb
                    ]);
                    $mensaje   = "Subcategoría registrada correctamente";
                    $resultado = "ok";
                }
            }
        }
    } catch (PDOException $e) {
        // Si falló la base, no se deja la imagen huérfana en el disco
        if ($rutaDestino != "" && is_file($rutaDestino)) {
            unlink($rutaDestino);
        }

        $codigo = $e->errorInfo[1] ?? 0; // código propio de MySQL
        if ($codigo == 1062) {
            $mensaje = "Ya existe una subcategoría con ese nombre";
        } elseif ($codigo == 1452) {
            $mensaje = "La categoría seleccionada no existe";
        } else {
            error_log($e->getMessage());
            $mensaje = "Error al procesar la solicitud";
        }
    }

    $_SESSION["mensaje_subcat_doc"]   = $mensaje;
    $_SESSION["accion_subcat_doc"]    = $accion;
    $_SESSION["resultado_subcat_doc"] = $resultado;
}

header("Location: Panel_Admin.php");
exit;
