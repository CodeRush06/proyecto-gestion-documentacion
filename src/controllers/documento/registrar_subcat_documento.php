<?php
session_start();
require __DIR__ . "/conectar.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_subcategoria = trim($_POST["nombre_subcategoria"] ?? "");
    $descripcion         = trim($_POST["descripcion"] ?? "");
    $id_categoria        = $_POST["id_categoria"] ?? "";
    $privado             = isset($_POST["privado"]); // true si tildó "Mantener privado"
    $estado_activo       = $privado ? 0 : 1;

    // Normalizar: "" (select vacío) se trata como no seleccionado
    $id_categoria = ($id_categoria === "") ? null : (int) $id_categoria;

    if ($nombre_subcategoria == "") {
        $mensaje = "Falta el nombre de la subcategoría";
    } elseif ($descripcion == "") {
        $mensaje = "Añada una descripción";
    } elseif ($id_categoria === null) {
        $mensaje = "Debe seleccionar una categoría padre";
    } elseif (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
        $mensaje = "Falta seleccionar una imagen válida";
    } else {
        $archivo = $_FILES["logo"];

        $tiposPermitidos = ["image/jpeg", "image/png"];
        $tipoReal = mime_content_type($archivo["tmp_name"]);

        if (!in_array($tipoReal, $tiposPermitidos)) {
            $mensaje = "El archivo debe ser una imagen (jpg o png)";
        } else {
            $extension     = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid("subcat_") . "." . $extension;
            $rutaDestino   = __DIR__ . "/SubCategoria_Logo/" . $nombreArchivo;
            $rutaWeb       = "Categoria_Logo/" . $nombreArchivo;

            if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                $mensaje = "Error al guardar la imagen";
            } else {
                try {
                    $sql = "INSERT INTO subcategoria_documento (id_categoria, nombre_subcategoria, descripcion, archivo_url, estado_activo) 
                            VALUES (:id_categoria, :nombre_subcategoria, :descripcion, :archivo_url, :estado_activo)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "id_categoria"         => $id_categoria,
                        "nombre_subcategoria"  => $nombre_subcategoria,
                        "descripcion"          => $descripcion,
                        "archivo_url"          => $rutaWeb,
                        "estado_activo"        => $estado_activo
                    ]);
                    $mensaje = "Subcategoría registrada correctamente";
                } catch (PDOException $e) {
                    if ($e->getCode() == "23000") {
                        $mensaje = "No se pudo registrar: verifique que la categoría exista o que el nombre no esté repetido";
                    } else {
                        $mensaje = "Error al registrar la subcategoría" . $e->getMessage();
};
                    }
                }
            }
        }
    

    $_SESSION["mensaje_subcat_doc"] = $mensaje;
}

header("Location: Panel_Admin.php");
exit;