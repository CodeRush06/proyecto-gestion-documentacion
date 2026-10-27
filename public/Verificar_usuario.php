<?php
session_start();
require __DIR__ .  '/../src/config/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$usuario    = trim($_POST["usuario"] ?? "");
$contrasena = trim($_POST["contrasena"] ?? "");

if ($usuario === "" || $contrasena === "") {
    $_SESSION["mensaje_usuario"] = "Complete usuario y contraseña";
    header("Location: login.php"); // ajustá al nombre real de este archivo
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM ADMINISTRATIVO WHERE usuario = :usuario");
$stmt->execute(["usuario" => $usuario]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($contrasena, $admin["contrasena"])) {
    $_SESSION["mensaje_usuario"] = "Usuario o contraseña incorrectos";
    header("Location: login.php"); 
    exit;
}
$stmt = $pdo->prepare("SELECT es_medico FROM ADMINISTRATIVO WHERE usuario = :usuario");
$stmt->execute(["es_medico" => $es_medico]);
$es_medico = $stmt->fetch(PDO::FETCH_ASSOC);


session_regenerate_id(true);
if ($es_medico == "false") {
    $_SESSION["rol"]            = "administrador";
    } else {
        $_SESSION["rol"]            = "medico";
     }

$_SESSION["id_funcionario"] = $admin["id_funcionario"];
$_SESSION["nombre"]         = $admin["nombre"];

header("Location: Panel_Admin.php");
exit;
