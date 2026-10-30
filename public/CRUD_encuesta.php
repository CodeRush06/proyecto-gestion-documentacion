<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error"; // pasa a "ok" solo si la operación sale bien

$accion    = $_POST["accion"] ?? " ";

// Guardián: solo POST y solo acciones conocidas
if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    // ---------- Datos del formulario ----------
    $id                      = (int) ($_POST["id"] ?? 0);
    $titulo                  = trim($_POST["titulo"] ?? "");
    $descripcion             = trim($_POST["descripcion"] ?? "");
    $privado                 = isset($_POST["privado"]);   // true si tildó "Mantener privado"
    $estado                  = $privado ? 0 : 1;           // privado = no visible = desactivado
    $id_tipo_encuesta        = (int) ($_POST["id_tipo_encuesta"] ?? 0);
    $requiere_identificacion = isset($_POST["requiere_identificacion"]) ? 1 : 0;
    $preguntas               = $_POST["preguntas"] ?? [];

    // TEMPORAL hasta que exista login: id fijo para pruebas
    $id_funcionario_creador = 1;

    try {
        // =================================================
        // BORRAR: baja lógica (nunca DELETE)
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una encuesta";
            } else {
                $stmt = $pdo->prepare("UPDATE encuesta SET estado = 0
                                       WHERE id_encuesta = :id AND estado = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Encuesta eliminada correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró una encuesta activa para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($titulo == "") {
            $mensaje = "Falta el título";
        } elseif ($descripcion == "") {
            $mensaje = "Falta la descripción";
        } elseif ($id_tipo_encuesta <= 0) {
            $mensaje = "Debe seleccionar un tipo de encuesta";

        // =================================================
        // MODIFICAR: solo cambia los datos de la encuesta,
        // no las preguntas (para no romper respuestas ya guardadas)
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione una encuesta";
            } else {
                $sql = "UPDATE encuesta
                        SET titulo = :titulo, descripcion = :descripcion,
                            id_tipo_encuesta = :id_tipo_encuesta,
                            requiere_identificacion = :requiere_identificacion
                        WHERE id_encuesta = :id AND estado = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "titulo"                  => $titulo,
                    "descripcion"             => $descripcion,
                    "id_tipo_encuesta"        => $id_tipo_encuesta,
                    "requiere_identificacion" => $requiere_identificacion,
                    "id"                      => $id
                ]);
                $mensaje   = "Encuesta modificada correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR: acá sí se exigen y se guardan las preguntas
        // =================================================
        } elseif (empty($preguntas)) {
            $mensaje = "Agregue al menos una pregunta";
        } else {
            $pdo->beginTransaction();

            // 1) La encuesta
            $stmtEnc = $pdo->prepare(
                "INSERT INTO encuesta (titulo, descripcion, estado, id_tipo_encuesta, id_funcionario_creador, requiere_identificacion)
                 VALUES (:titulo, :descripcion, :estado, :id_tipo_encuesta, :id_funcionario_creador, :requiere_identificacion)"
            );
            $stmtEnc->execute([
                "titulo"                  => $titulo,
                "descripcion"             => $descripcion,
                "estado"                  => $estado,
                "id_tipo_encuesta"        => $id_tipo_encuesta,
                "id_funcionario_creador"  => $id_funcionario_creador,
                "requiere_identificacion" => $requiere_identificacion
            ]);
            $id_encuesta = $pdo->lastInsertId();

            // 2) Sentencias reutilizables para preguntas y opciones
            $stmtPreg = $pdo->prepare(
                "INSERT INTO pregunta (id_encuesta, texto_pregunta, tipo_pregunta, obligatoria, orden)
                 VALUES (:id_encuesta, :texto_pregunta, :tipo_pregunta, :obligatoria, :orden)"
            );
            $stmtOp = $pdo->prepare(
                "INSERT INTO opcion_respuesta (id_pregunta, texto_opcion, valor)
                 VALUES (:id_pregunta, :texto_opcion, :valor)"
            );

            $preguntas_guardadas = 0;

            // 3) Cada pregunta (y sus opciones si corresponde)
            foreach ($preguntas as $p) {
                $texto_pregunta = trim($p["texto_pregunta"] ?? "");
                $tipo_pregunta  = $p["tipo_pregunta"] ?? "texto";
                $obligatoria    = !empty($p["obligatoria"]) ? 1 : 0;
                $orden          = (int) ($p["orden"] ?? 0);

                if ($texto_pregunta == "") continue;
                if (!in_array($tipo_pregunta, ["texto", "opcion_multiple", "escala"], true)) {
                    $tipo_pregunta = "texto";
                }

                $stmtPreg->execute([
                    "id_encuesta"    => $id_encuesta,
                    "texto_pregunta" => $texto_pregunta,
                    "tipo_pregunta"  => $tipo_pregunta,
                    "obligatoria"    => $obligatoria,
                    "orden"          => $orden
                ]);
                $id_pregunta = $pdo->lastInsertId();
                $preguntas_guardadas++;

                if (in_array($tipo_pregunta, ["opcion_multiple", "escala"], true) && !empty($p["opciones"])) {
                    foreach ($p["opciones"] as $op) {
                        $texto_opcion = trim($op["texto_opcion"] ?? "");
                        $valor        = trim($op["valor"] ?? "");
                        if ($texto_opcion == "") continue;

                        $stmtOp->execute([
                            "id_pregunta"  => $id_pregunta,
                            "texto_opcion" => $texto_opcion,
                            "valor"        => $valor !== "" ? $valor : null
                        ]);
                    }
                }
            }

            // Si todas las preguntas venían vacías, se deshace todo
            if ($preguntas_guardadas == 0) {
                $pdo->rollBack();
                $mensaje = "Agregue al menos una pregunta con texto";
            } else {
                $pdo->commit();
                $mensaje   = "Encuesta registrada correctamente";
                $resultado = "ok";
            }
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($e->getCode() == "23000") {
            $mensaje = "Ya existe una encuesta con ese título";
        } else {
            error_log($e->getMessage()); // el detalle va al log, no a la pantalla
            $mensaje = "Error al procesar la solicitud";
        }
    }

    // Lo que lee Panel_Admin.php
    $_SESSION["mensaje_encuesta"]   = $mensaje;
    $_SESSION["accion_encuesta"]    = $accion;     // crear | modificar | borrar
    $_SESSION["resultado_encuesta"] = $resultado;  // ok | error
}

header("Location: Panel_Admin.php");
exit;
