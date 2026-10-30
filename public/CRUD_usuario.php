<?php
session_start();
require __DIR__ . '/../src/config/database.php';

$mensaje   = "";
$resultado = "error";
$accion    = $_POST["accion"] ?? " ";
if ($_SERVER["REQUEST_METHOD"] == "POST" && in_array($accion, ["crear", "modificar", "borrar"], true)) {

    $id         = (int) ($_POST["id"] ?? 0);
    $nombre     = trim($_POST["nombre"] ?? "");
    $apellido   = trim($_POST["apellido"] ?? "");
    $email      = trim($_POST["email"] ?? "");
    $usuario    = trim($_POST["usuario"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";   // la contraseña no se recorta

    // es_medico llega como "0" o "1" desde un select obligatorio
    $es_medico_raw = $_POST["es_medico"] ?? "";
    $es_medico     = $es_medico_raw === "1" ? 1 : 0;

    try {
        // =================================================
        // BORRAR: baja lógica, nunca el último administrador activo
        // =================================================
        if ($accion == "borrar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un administrador";
            } elseif ($pdo->query("SELECT COUNT(*) FROM ADMINISTRATIVO WHERE estado_activo = 1")->fetchColumn() <= 1) {
                $mensaje = "No se puede eliminar al último administrador";
            } else {
                $stmt = $pdo->prepare("UPDATE ADMINISTRATIVO SET estado_activo = 0
                                       WHERE id_usuario = :id AND estado_activo = 1");
                $stmt->execute(["id" => $id]);

                if ($stmt->rowCount() > 0) {
                    $mensaje   = "Administrador eliminado correctamente";
                    $resultado = "ok";
                } else {
                    $mensaje = "No se encontró un administrador activo para eliminar";
                }
            }

        // =================================================
        // Validaciones comunes a CREAR y MODIFICAR
        // =================================================
        } elseif ($nombre == "") {
            $mensaje = "Falta el nombre";
        } elseif ($apellido == "") {
            $mensaje = "Falta el apellido";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "Ingrese un email válido";
        } elseif ($usuario == "") {
            $mensaje = "Falta el nombre de usuario";
        } elseif (!in_array($es_medico_raw, ["0", "1"], true)) {
            $mensaje = "Indique si es médico";

        // =================================================
        // MODIFICAR: solo datos (la contraseña no se cambia acá)
        // =================================================
        } elseif ($accion == "modificar") {
            if ($id <= 0) {
                $mensaje = "Seleccione un administrador";
            } else {
                $sql = "UPDATE ADMINISTRATIVO
                        SET nombre = :nombre, apellido = :apellido, email = :email,
                            usuario = :usuario, es_medico = :es_medico
                        WHERE id_usuario = :id AND estado_activo = 1";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    "nombre"    => $nombre,
                    "apellido"  => $apellido,
                    "email"     => $email,
                    "usuario"   => $usuario,
                    "es_medico" => $es_medico,
                    "id"        => $id
                ]);
                $mensaje   = "Administrador modificado correctamente";
                $resultado = "ok";
            }

        // =================================================
        // CREAR: exige contraseña y la guarda con hash
        // =================================================
        } elseif (strlen($contrasena) < 8) {
            $mensaje = "La contraseña debe tener al menos 8 caracteres";
        } else {
            $sql = "INSERT INTO ADMINISTRATIVO (nombre, apellido, email, usuario, contrasena, es_medico, estado_activo)
                    VALUES (:nombre, :apellido, :email, :usuario, :contrasena, :es_medico, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "nombre"     => $nombre,
                "apellido"   => $apellido,
                "email"      => $email,
                "usuario"    => $usuario,
                "contrasena" => password_hash($contrasena, PASSWORD_DEFAULT),
                "es_medico"  => $es_medico
            ]);
            $mensaje   = "usuario registrado correctamente";
            $resultado = "ok";
        }
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) == 1062) {
            $mensaje = "Ya existe un usuario con ese usuario o email";
        } else {
            error_log($e->getMessage());
            $mensaje = "Error al procesar la solicitud";
        }
    }

    $_SESSION["mensaje_usuario"]   = $mensaje;
    $_SESSION["accion_usuario"]    = $accion;
    $_SESSION["resultado_usuario"] = $resultado;
}

header("Location: Panel_Admin.php");
exit;
