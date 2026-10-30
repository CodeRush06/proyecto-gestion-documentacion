<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error"; // pasa a "ok" solo si la operación sale bien

$accion    = $_POST["accion"] ?? " ";

// Guardián: solo POST y solo acciones conocidas
if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    // ---------- Datos del formulario ----------
    $id            = (int) ($_POST["id"] ?? 0);
    $nombre_tipo   = trim($_POST["nombre_tipo"] ?? "");
    $descripcion   = trim($_POST["descripcion"] ?? "");
    $privado       = isset($_POST["privado"]);   // true si tildó "Mantener privado"
    $estado_activo = $privado ? 0 : 1;           // privado = no visible = desactivado

    try {
        // =================================================
        // BORRAR: baja lógica (nunca DELETE)
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un tipo de encuesta";
            } else {
                $stmt = $pdo->prepare("UPDATE tipo_encuesta SET estado_activo = 0
                                       WHERE id_tipo = :id AND estado_activo = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Tipo de encuesta eliminado correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró un tipo de encuesta activo para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($nombre_tipo == "") {
            $mensaje = "Falta el nombre del tipo de encuesta";
        } elseif ($descripcion == "") {
            $mensaje = "Añada una descripción";

        // =================================================
        // MODIFICAR: cambia nombre y descripción
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un tipo de encuesta";
            } else {
                $sql = "UPDATE tipo_encuesta
                        SET nombre_tipo = :nombre_tipo, descripcion = :descripcion
                        WHERE id_tipo = :id AND estado_activo = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "nombre_tipo" => $nombre_tipo,
                    "descripcion" => $descripcion,
                    "id"          => $id
                ]);
                $mensaje   = "Tipo de encuesta modificado correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR
        // =================================================
        } else {
            $sql = "INSERT INTO tipo_encuesta (nombre_tipo, descripcion, estado_activo)
                    VALUES (:nombre_tipo, :descripcion, :estado_activo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "nombre_tipo"   => $nombre_tipo,
                "descripcion"   => $descripcion,
                "estado_activo" => $estado_activo
            ]);
            $mensaje   = "Tipo de encuesta registrado correctamente";
            $resultado = "ok";
        }
    } catch (PDOException $e) {
        if ($e->getCode() == "23000") {
            $mensaje = "Ya existe un tipo de encuesta con ese nombre";
        } else {
            error_log($e->getMessage()); // el detalle va al log, no a la pantalla
            $mensaje = "Error al procesar la solicitud";
        }
    }

    // Lo que lee Panel_Admin.php
    $_SESSION["mensaje_cat_enc"]   = $mensaje;
    $_SESSION["accion_cat_enc"]    = $accion;     // crear | modificar | borrar
    $_SESSION["resultado_cat_enc"] = $resultado;  // ok | error
}

header("Location: Panel_Admin.php");
exit;
