<?php
//Es llamado por app.js para pedir el menu sin recargar la pagina.
require_once "controlMenu.php";

$ctrl = new Control_Menu();

$action = $_REQUEST['a'] ?? "";

if ($action === "obtenerMenuAjax") $ctrl->obtenerMenuAjax();
else echo json_encode(["error" => "Acción inválida"]);