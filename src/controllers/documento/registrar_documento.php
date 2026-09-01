<?php
session_start();
require __DIR__ . "/conectar.php";
require __DIR__ . "/listar_cat_documentos.php";
require __DIR__ . "/listar_subcategorias.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo      = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $tipo_destino = $_POST["tipo_destino"] ?? ""; // "categoria" o "subcategoria"
    $id_categoria    = $_POST["id_categoria"] ?? "";
    $id_subcategoria = $_POST["id_subcategoria"] ?? "";
    $estado = trim($_POST["estado"] ?? "activo");

    // TEMPORAL: hasta que exista login, usamos un id fijo para pruebas.
    // TODO: reemplazar por $_SESSION["id_funcionario"] una vez armado el login.
    $id_funcionario_creador = 1;

    // Según lo que eligió el usuario, solo uno de los dos queda activo
    if ($tipo_destino === "categoria") {
        $id_categoria    = ($id_categoria === "") ? null : (int) $id_categoria;
        $id_subcategoria = null;
    } elseif ($tipo_destino === "subcategoria") {
        $id_subcategoria = ($id_subcategoria === "") ? null : (int) $id_subcategoria;
        $id_categoria    = null;
    } else {
        $id_categoria    = null;
        $id_subcategoria = null;
    }

    if ($titulo == "") {
        $mensaje = "Falta el título del documento";
    } elseif ($tipo_destino !== "categoria" && $tipo_destino !== "subcategoria") {
        $mensaje = "Debe indicar si el documento va en una categoría o subcategoría";
    } elseif ($id_categoria === null && $id_subcategoria === null) {
        $mensaje = "Debe seleccionar una categoría o subcategoría válida";
    } elseif (!isset($_FILES["archivo"]) || $_FILES["archivo"]["error"] !== UPLOAD_ERR_OK) {
        $mensaje = "Falta seleccionar el archivo del documento";
    } else {
        $archivo = $_FILES["archivo"];

        $tiposPermitidos = ["application/pdf", "image/jpeg", "image/png"];
        $tipoReal = mime_content_type($archivo["tmp_name"]);

        if (!in_array($tipoReal, $tiposPermitidos)) {
            $mensaje = "Tipo de archivo no permitido";
        } else {
            $extension     = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid("doc_") . "." . $extension;
            $rutaDestino   = __DIR__ . "/Documentos/" . $nombreArchivo;
            $rutaWeb       = "Documentos/" . $nombreArchivo;

            if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                $mensaje = "Error al guardar el archivo";
            } else {
                try {
                    $sql = "INSERT INTO documento 
                            (titulo, descripcion, archivo_url, fecha_creacion, estado, id_categoria, id_subcategoria, id_funcionario_creador) 
                            VALUES 
                            (:titulo, :descripcion, :archivo_url, NOW(), :estado, :id_categoria, :id_subcategoria, :id_funcionario_creador)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "titulo"                 => $titulo,
                        "descripcion"            => $descripcion,
                        "archivo_url"            => $rutaWeb,
                        "estado"                 => $estado,
                        "id_categoria"           => $id_categoria,
                        "id_subcategoria"        => $id_subcategoria,
                        "id_funcionario_creador" => $id_funcionario_creador
                    ]);
                    $mensaje = "Documento registrado correctamente";
                } catch (PDOException $e) {
                    if ($e->getCode() == "23000") {
                        $mensaje = "Error de integridad al registrar el documento";
                    } else {
                        $mensaje = "Error al registrar el documento";
                    }
                }
            }
        }
    }

    $_SESSION["mensaje_documento"] = $mensaje;
}

header("Location: Panel_Admin.php");
exit;