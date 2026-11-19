<?php

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/formularios_controller.php';
require_once __DIR__ . '/fechas_retrospectiva.php';

use app\controllers\config\ControllersConfig;
use app\controllers\retro\FormulariosController;
use app\controllers\retro\FechasRetrospectiva;

date_default_timezone_set('America/Bogota');
session_start();
$_SESSION['token'] ??= bin2hex(random_bytes(32));
$token = $_SESSION['token'];
$error = $_SESSION['error_formulario'] ?? null;
unset($_SESSION['error_formulario']);
$datos = null;
$revision = null;
$retrospectivaVencida = false;
$registro = null;
$registrosController = null;
$registroFormulario = [];
$sprintFormulario = [];
$paginas = ['inicio', 'retrospectiva', 'agendar', 'anteriores', 'editar_registro', 'eliminar_registro', 'sprint_actual'];
$pagina = in_array($_GET['pagina'] ?? '', $paginas, true) ? $_GET['pagina'] : 'inicio';
$_SESSION['agenda'] ??= FormulariosController::agenda_inicial();
$agenda = &$_SESSION['agenda'];
try {
    $controller = ControllersConfig::get_retro_controller();
    $datos = $controller->get_resumen();
    $sprintActual = $datos['actual']['sprint'] ?? null;
    $fechas = FechasRetrospectiva::evaluar($sprintActual, date('Y-m-d'));
    $retrospectivaVencida = $fechas['vencida'];

    if ($retrospectivaVencida && $pagina !== 'retrospectiva') {
        header('Location: index.php?pagina=retrospectiva');
        exit;
    }
    if (
        $pagina === 'retrospectiva' && $fechas['anticipada']
        && ($_SESSION['adelanto_confirmado'] ?? null) !== $sprintActual->get_id()
    ) {
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && is_string($_POST['token'] ?? null) && hash_equals($token, $_POST['token'])
            && (string)($_POST['sprint_id'] ?? '') === (string)$sprintActual->get_id()
        ) {
            if (($_POST['accion'] ?? '') === 'confirmar_adelanto') {
                $_SESSION['adelanto_confirmado'] = $sprintActual->get_id();
                header('Location: index.php?pagina=retrospectiva');
                exit;
            }
            if (($_POST['accion'] ?? '') === 'cancelar_adelanto') {
                unset($_SESSION['adelanto_confirmado']);
                header('Location: index.php');
                exit;
            }
        }
        $pagina = 'confirmar_retrospectiva';
        require __DIR__ . '/../../views/confirmar_retrospectiva.php';
        return;
    }

    if ($datos['actual']) {
        if (($_SESSION['revision']['sprint_id'] ?? null) !== $datos['actual']['sprint']->get_id()) $_SESSION['revision'] = FormulariosController::revision_inicial($datos['actual']);
    } else unset($_SESSION['revision']);
    $revision = &$_SESSION['revision'];
    if (is_array($revision)) $revision += ['acuerdos' => ''];
    if (in_array($pagina, ['editar_registro', 'eliminar_registro'], true)) {
        $registrosController = ControllersConfig::get_registros_controller();
        $registro = $registrosController->get_item($_GET['id'] ?? null);
        $registroFormulario = ['categoria' => $registro->get_categoria(), 'descripcion' => $registro->get_descripcion(), 'cumplida' => $registro->get_cumplida() ? '1' : '0'];
    }
    if ($pagina === 'sprint_actual' && ($datos['actual'] || $datos['programado'])) {
        $sprintDetalle = ($datos['actual'] ?? $datos['programado'])['sprint'];
        $sprintFormulario = ['nombre' => $sprintDetalle->get_nombre(), 'inicio' => $sprintDetalle->get_fecha_inicio(), 'fin' => $sprintDetalle->get_fecha_fin()];
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!is_string($_POST['token'] ?? null) || !hash_equals($token, $_POST['token'])) throw new RuntimeException('La sesión del formulario venció. Recarga la página.');
        try {
            if ($pagina === 'sprint_actual') {
                $sprintDetalle = ($datos['actual'] ?? $datos['programado'])['sprint'] ?? null;
                if (!$sprintDetalle || (string)($_POST['id'] ?? '') !== (string)$sprintDetalle->get_id() || ($_POST['accion'] ?? '') !== 'guardar_sprint') throw new RuntimeException('Sprint no válido.');
                ControllersConfig::get_sprints_controller()->guardar($_POST);
                unset($_SESSION['adelanto_confirmado']);
                header('Location: index.php?pagina=sprint_actual&guardado=1');
                exit;
            } elseif (in_array($pagina, ['editar_registro', 'eliminar_registro'], true)) {
                if ($registrosController === null || $registro === null) throw new RuntimeException('No se pudo cargar el registro solicitado.');
                if ((string)($_POST['id'] ?? '') !== (string)$registro->get_id()) throw new RuntimeException('El registro del formulario no coincide.');
                if ($pagina === 'editar_registro' && ($_POST['accion'] ?? '') === 'guardar_registro') {
                    $sprintId = $registrosController->guardar($_POST);
                } elseif ($pagina === 'eliminar_registro' && ($_POST['accion'] ?? '') === 'eliminar_registro') {
                    $sprintId = $registrosController->eliminar($_POST['id']);
                } else throw new RuntimeException('Operación no válida.');
                if (($_SESSION['revision']['sprint_id'] ?? null) === $sprintId) unset($_SESSION['revision']);
                $esActual = ($datos['actual']['sprint'] ?? $datos['programado']['sprint'] ?? null)?->get_id() === $sprintId;
                header('Location: ' . ($esActual ? 'index.php?pagina=sprint_actual' : 'index.php?pagina=anteriores'));
                exit;
            } elseif ($pagina === 'agendar') {
                if ($datos['actual'] || $datos['programado']) throw new RuntimeException('Primero finaliza el sprint abierto.');
                if (FormulariosController::editar_agenda($agenda, $_POST)) {
                    $controller->agendar(['nombre' => $agenda['nombre'], 'inicio' => $agenda['inicio'], 'fin' => $agenda['fin'], 'objetivos' => implode("\n", $agenda['objetivos']), 'retomar' => $agenda['retomar']]);
                    unset($_SESSION['agenda']);
                    header('Location: index.php');
                    exit;
                }
            } elseif ($pagina === 'retrospectiva' && $datos['actual'] && $revision) {
                if (FormulariosController::editar_revision($revision, $_POST)) {
                    $controller->finalizar($revision);
                    unset($_SESSION['revision'], $_SESSION['adelanto_confirmado']);
                    header('Location: index.php?pagina=agendar&finalizado=1');
                    exit;
                }
            } else throw new RuntimeException('No hay un sprint actual para revisar.');
        } catch (PDOException $excepcion) {
            throw $excepcion;
        } catch (RuntimeException $excepcion) {
            if ($pagina === 'sprint_actual') {
                $error = $excepcion->getMessage();
                foreach (['nombre', 'inicio', 'fin'] as $campo) if (is_string($_POST[$campo] ?? null)) $sprintFormulario[$campo] = $_POST[$campo];
                require __DIR__ . '/../../views/sprint_actual.php';
                return;
            }
            if ($pagina === 'editar_registro') {
                $error = $excepcion->getMessage();
                foreach (['categoria', 'descripcion', 'cumplida'] as $campo) if (is_string($_POST[$campo] ?? null)) $registroFormulario[$campo] = $_POST[$campo];
                require __DIR__ . '/../../views/editar_registro.php';
                return;
            }
            $_SESSION['error_formulario'] = $excepcion->getMessage();
        }
        $destino = 'index.php?pagina=' . $pagina;
        if (in_array($pagina, ['editar_registro', 'eliminar_registro'], true) && $registro) $destino .= '&id=' . $registro->get_id();
        header('Location: ' . $destino);
        exit;
    }
} catch (PDOException $excepcion) {
    $error = 'No se pudo consultar la base de datos. Revisa la conexión con registro_retro_db.';
} catch (RuntimeException $excepcion) {
    $error = $excepcion->getMessage();
}
$revision = $_SESSION['revision'] ?? null;
require __DIR__ . '/../../views/' . $pagina . '.php';
