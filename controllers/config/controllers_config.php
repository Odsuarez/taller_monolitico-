<?php

namespace app\controllers\config;

use app\controllers\retro\RetroController;
use app\controllers\retro\RetroControllerInterface;
use app\models\config\ModelsConfig;

class ControllersConfig
{
    public static function get_sprints_controller(): \app\controllers\retro\SprintsController
    {
        return new \app\controllers\retro\SprintsController(ModelsConfig::get_retro_repository());
    }
    public static function get_registros_controller(): \app\controllers\retro\RegistrosController
    {
        return new \app\controllers\retro\RegistrosController(ModelsConfig::get_retro_repository());
    }
    public static function get_retro_controller(): RetroControllerInterface
    {
        return new RetroController(ModelsConfig::get_retro_repository());
    }
}
