<?php
require_once __DIR__.'/../config/bootstrap.php';
use app\controllers\config\ControllersConfig;
date_default_timezone_set('America/Bogota');
session_start();
$_SESSION['token'] ??= bin2hex(random_bytes(32));
$token=$_SESSION['token']; $error=null; $datos=null;
$paginas=['inicio','retrospectiva','agendar','anteriores'];
$pagina=in_array($_GET['pagina'] ?? '',$paginas,true) ? $_GET['pagina'] : 'inicio';
try {
    $controller=ControllersConfig::get_retro_controller();
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!is_string($_POST['token'] ?? null) || !hash_equals($token,$_POST['token'])) throw new RuntimeException('La sesión del formulario venció. Recarga la página.');
        try {
            if (($_POST['operacion'] ?? '')==='agendar') {
                $controller->agendar($_POST);
                header('Location: index.php'); exit;
            } elseif (($_POST['operacion'] ?? '')==='finalizar') {
                $controller->finalizar($_POST);
                header('Location: index.php?pagina=agendar&finalizado=1'); exit;
            } else throw new RuntimeException('Operación no válida.');
        } catch (PDOException $ex) { throw $ex; }
        catch (RuntimeException $ex) { $error=$ex->getMessage(); }
    }
    $datos=$controller->get_resumen();
} catch (PDOException $ex) {
    $error='No se pudo consultar la base de datos. Revisa la conexión con registro_retro_db.';
} catch (RuntimeException $ex) { $error=$ex->getMessage(); }
require __DIR__.'/../../views/'.$pagina.'.php';
