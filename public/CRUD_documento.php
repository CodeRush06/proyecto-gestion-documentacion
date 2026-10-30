<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error";
$accion    = $_POST["accion"] ?? " ";

if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    // ---------- Datos del formulario ----------
    $id              = (int) ($_POST["id"] ?? 0);
    $titulo          = trim($_POST["titulo"] ?? "");
    $descripcion     = trim($_POST["descripcion"] ?? "");
    $tipo_destino    = $_POST["tipo_destino"] ?? "categoria";   // categoria | subcategoria
    $id_categoria    = (int) ($_POST["id_categoria"] ?? 0);
    $id_subcategoria = (int) ($_POST["id_subcategoria"] ?? 0);

    // El documento va asociado a UNA categoría o a UNA subcategoría (la otra queda en NULL)
    $cat_final = null;
    $sub_final = null;
    if ($tipo_destino == "subcategoria") {
        $sub_final = $id_subcategoria > 0 ? $id_subcategoria : null;
    } else {
        $cat_final = $id_categoria > 0 ? $id_categoria : null;
    }

    $rutaDestino = ""; // si el INSERT falla, se borra el archivo ya subido

    try {
        // =================================================
        // BORRAR: baja lógica
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un documento";
            } else {
                $stmt = $pdo->prepare("UPDATE documento SET estado = 0
                                       WHERE id_documento = :id AND estado = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Documento eliminado correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró un documento activo para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($titulo == "") {
            $mensaje = "Falta el título";
        } elseif ($descripcion == "") {
            $mensaje = "Añada una descripción";

        // =================================================
        // MODIFICAR: solo título y descripción, no el archivo ni el destino
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un documento";
            } else {
                $sql = "UPDATE documento SET titulo = :titulo, descripcion = :descripcion
                        WHERE id_documento = :id AND estado = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(["titulo" => $titulo, "descripcion" => $descripcion, "id" => $id]);
                $mensaje   = "Documento modificado correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR: exige destino y archivo
        // =================================================
        } elseif ($cat_final === null && $sub_final === null) {
            $mensaje = "Seleccione una categoría o subcategoría";
        } elseif (!isset($_FILES["archivo"]) || $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
            $mensaje = "Falta seleccionar un archivo válido";
        } else {
            $archivo = $_FILES["archivo"];

            $tiposPermitidos = [
                "application/pdf" => "pdf",
                "image/jpeg"      => "jpg",
                "image/png"       => "png"
            ]; // tipo real => extensión
            $tipoReal = mime_content_type($archivo["tmp_name"]);

            if (!isset($tiposPermitidos[$tipoReal])) {
                $mensaje = "El archivo debe ser PDF, jpg o png";
            } else {
                $nombreArchivo = uniqid("doc_") . "." . $tiposPermitidos[$tipoReal];
                $rutaDestino   = __DIR__ . "/Documentos_Archivos/" . $nombreArchivo;
                $rutaWeb       = "Documentos_Archivos/" . $nombreArchivo;

                if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                    $mensaje     = "Error al guardar el archivo";
                    $rutaDestino = "";
                } else {
                    $sql = "INSERT INTO documento
                                (titulo, descripcion, id_categoria, id_subcategoria, archivo_url, estado)
                            VALUES (:titulo, :descripcion, :id_categoria, :id_subcategoria, :archivo_url, 1)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "titulo"          => $titulo,
                        "descripcion"     => $descripcion,
                        "id_categoria"    => $cat_final,
                        "id_subcategoria" => $sub_final,
                        "archivo_url"     => $rutaWeb
                    ]);
                    $mensaje   = "Documento registrado correctamente";
                    $resultado = "ok";
                }
            }
        }
    } catch (PDOException $e) {
        if ($rutaDestino != "" && is_file($rutaDestino)) {
            unlink($rutaDestino);
        }

        $codigo = $e->errorInfo[1] ?? 0;
        if ($codigo == 1062) {
            $mensaje = "Ya existe un documento con ese título";
        } elseif ($codigo == 1452) {
            $mensaje = "La categoría o subcategoría seleccionada no existe";
        } else {
            error_log($e->getMessage());
            $mensaje = "Error al procesar la solicitud";
        }
    }

    $_SESSION["mensaje_documento"]   = $mensaje;
    $_SESSION["accion_documento"]    = $accion;
    $_SESSION["resultado_documento"] = $resultado;
}

header("Location: Panel_Admin.php");
exit;
