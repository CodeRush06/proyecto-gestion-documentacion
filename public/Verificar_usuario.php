<?php
session_start();
require __DIR__ . "/conectar.php";

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

session_regenerate_id(true);

$_SESSION["rol"]            = "administrador";
$_SESSION["id_funcionario"] = $admin["id_funcionario"];
$_SESSION["nombre"]         = $admin["nombre"];

header("Location: Panel_Admin.php");
exit;