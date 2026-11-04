<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error";
$accion    = $_POST["accion"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    in_array($accion, ["crear", "modificar", "borrar"], true)) {

    $id              = (int) ($_POST["id"] ?? 0);
    $titulo          = trim($_POST["titulo"] ?? "");
    $descripcion     = trim($_POST["descripcion"] ?? "");
    $tipo_destino    = $_POST["tipo_destino"] ?? "categoria";
    $id_categoria    = (int) ($_POST["id_categoria"] ?? 0);
    $id_subcategoria = (int) ($_POST["id_subcategoria"] ?? 0);

    $cat_final = null;
    $sub_final = null;

    if ($tipo_destino === "subcategoria") {
        $sub_final = $id_subcategoria > 0 ? $id_subcategoria : null;
    } else {
        $cat_final = $id_categoria > 0 ? $id_categoria : null;
    }

    $rutaDestino = "";

    try {

        // BORRAR
        if ($accion === "borrar") {

            if ($id <= 0) {
                $mensaje = "Seleccione un documento";
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE DOCUMENTO SET estado = 0
                     WHERE id_documento = :id AND estado = 1"
                );

                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje = "Documento eliminado correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró un documento activo para eliminar";
                }
            }

        // VALIDACIONES
        } elseif ($titulo === "") {
            $mensaje = "Falta el título";

        } elseif ($descripcion === "") {
            $mensaje = "Añada una descripción";

        // MODIFICAR
        } elseif ($accion === "modificar") {

            if ($id <= 0) {
                $mensaje = "Seleccione un documento";
            } else {
                $sql = "UPDATE DOCUMENTO
                        SET titulo = :titulo, descripcion = :descripcion
                        WHERE id_documento = :id AND estado = 1";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "titulo" => $titulo,
                    "descripcion" => $descripcion,
                    "id" => $id
                ]);

                $mensaje = "Documento modificado correctamente";
                $resultado = "ok";
            }

        // CREAR
        } elseif ($cat_final === null && $sub_final === null) {

            $mensaje = "Seleccione una categoría o subcategoría";

        } elseif (
            !isset($_FILES["archivo"]) ||
            $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK
        ) {

            $mensaje = "No se pudo subir el archivo. ";

            if (!isset($_FILES["archivo"])) {
                $mensaje .= "No se recibió ningún archivo.";
            } else {
                switch ($_FILES["archivo"]["error"]) {
                    case UPLOAD_ERR_NO_FILE:
                        $mensaje .= "Debe seleccionar un archivo.";
                        break;

                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $mensaje .= "El archivo supera el tamaño permitido.";
                        break;

                    case UPLOAD_ERR_PARTIAL:
                        $mensaje .= "El archivo se subió parcialmente.";
                        break;

                    default:
                        $mensaje .= "Error de subida número " .
                                    $_FILES["archivo"]["error"];
                        break;
                }
            }

        } else {

            $archivo = $_FILES["archivo"];

            if (
                empty($archivo["tmp_name"]) ||
                !is_uploaded_file($archivo["tmp_name"])
            ) {
                $mensaje = "El archivo temporal no es válido.";

            } else {

                $tiposPermitidos = [
                    "application/pdf" => "pdf",
                    "image/jpeg" => "jpg",
                    "image/png" => "png"
                ];

                $tipoReal = mime_content_type($archivo["tmp_name"]);

                if (!isset($tiposPermitidos[$tipoReal])) {

                    $mensaje = "El archivo debe ser PDF, JPG o PNG.";

                } else {

                    $nombreArchivo = bin2hex(random_bytes(16)) .
                                     "." . $tiposPermitidos[$tipoReal];

                    $directorio = __DIR__ . "/Documentos/";

                    if (!is_dir($directorio)) {
                        throw new RuntimeException(
                            "No existe el directorio Documentos"
                        );
                    }

                    $rutaDestino = $directorio . $nombreArchivo;
                    $rutaWeb = "Documentos_Archivos/" . $nombreArchivo;

                    if (!move_uploaded_file(
                        $archivo["tmp_name"],
                        $rutaDestino
                    )) {
                        $mensaje = "Error al guardar el archivo.";
                        $rutaDestino = "";

                    } else {

                        $sql = "INSERT INTO DOCUMENTO
                                (titulo, descripcion, id_categoria,
                                 id_subcategoria, archivo_url, estado)
                                VALUES
                                (:titulo, :descripcion, :id_categoria,
                                 :id_subcategoria, :archivo_url, 1)";

                        $stmt = $pdo->prepare($sql);

                        $stmt->execute([
                            "titulo" => $titulo,
                            "descripcion" => $descripcion,
                            "id_categoria" => $cat_final,
                            "id_subcategoria" => $sub_final,
                            "archivo_url" => $rutaWeb
                        ]);

                        $mensaje = "Documento registrado correctamente";
                        $resultado = "ok";
                    }
                }
            }
        }

    } catch (Throwable $e) {

        if ($rutaDestino !== "" && is_file($rutaDestino)) {
            unlink($rutaDestino);
        }

        error_log($e->getMessage());

        if ($e instanceof PDOException) {
            $codigo = $e->errorInfo[1] ?? 0;

            if ($codigo == 1062) {
                $mensaje = "Ya existe un documento con ese título.";
            } elseif ($codigo == 1452) {
                $mensaje = "La categoría o subcategoría seleccionada no existe.";
            } else {
                $mensaje = "Error al procesar la solicitud. Revisa el log.";
            }
        } else {
            $mensaje = "Error al procesar el archivo. Revisa el log.";
        }
    }

    $_SESSION["mensaje_documento"] = $mensaje;
    $_SESSION["accion_documento"] = $accion;
    $_SESSION["resultado_documento"] = $resultado;
}

header("Location: Panel_Admin.php");
exit;
