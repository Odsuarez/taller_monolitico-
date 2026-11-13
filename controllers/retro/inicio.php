<?php
require_once __DIR__.'/../config/bootstrap.php';
require_once __DIR__.'/formularios_controller.php';
use app\controllers\config\ControllersConfig;
use app\controllers\retro\FormulariosController;
date_default_timezone_set('America/Bogota');
session_start();
$_SESSION['token'] ??= bin2hex(random_bytes(32));
$token=$_SESSION['token']; $error=$_SESSION['error_formulario'] ?? null;
unset($_SESSION['error_formulario']);
$datos=null;
$paginas=['inicio','retrospectiva','agendar','anteriores'];
$pagina=in_array($_GET['pagina'] ?? '',$paginas,true) ? $_GET['pagina'] : 'inicio';
$_SESSION['agenda'] ??= FormulariosController::agenda_inicial();
$agenda=&$_SESSION['agenda'];
try {
    $controller=ControllersConfig::get_retro_controller();
    $datos=$controller->get_resumen();
    if ($datos['actual']) {
        if (($_SESSION['revision']['sprint_id'] ?? null)!==$datos['actual']['sprint']->get_id()) $_SESSION['revision']=FormulariosController::revision_inicial($datos['actual']);
    } else unset($_SESSION['revision']);
    $revision=&$_SESSION['revision'];
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!is_string($_POST['token'] ?? null) || !hash_equals($token,$_POST['token'])) throw new RuntimeException('La sesión del formulario venció. Recarga la página.');
        try {
            if ($pagina==='agendar') {
                if ($datos['actual'] || $datos['programado']) throw new RuntimeException('Primero finaliza el sprint abierto.');
                if (FormulariosController::editar_agenda($agenda,$_POST)) {
                    $controller->agendar(['nombre'=>$agenda['nombre'],'inicio'=>$agenda['inicio'],'fin'=>$agenda['fin'],'objetivos'=>implode("\n",$agenda['objetivos']),'retomar'=>$agenda['retomar']]);
                    unset($_SESSION['agenda']);
                    header('Location: index.php'); exit;
                }
            } elseif ($pagina==='retrospectiva' && $datos['actual'] && $revision) {
                if (FormulariosController::editar_revision($revision,$_POST)) {
                    $controller->finalizar($revision);
                    unset($_SESSION['revision']);
                    header('Location: index.php?pagina=agendar&finalizado=1'); exit;
                }
            } else throw new RuntimeException('No hay un sprint actual para revisar.');
        } catch (PDOException $ex) { throw $ex; }
        catch (RuntimeException $ex) { $_SESSION['error_formulario']=$ex->getMessage(); }
        header('Location: index.php?pagina='.$pagina); exit;
    }
} catch (PDOException $ex) {
    $error='No se pudo consultar la base de datos. Revisa la conexión con registro_retro_db.';
} catch (RuntimeException $ex) { $error=$ex->getMessage(); }
$revision=$_SESSION['revision'] ?? null;
require __DIR__.'/../../views/'.$pagina.'.php';
