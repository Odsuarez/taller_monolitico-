<?php
namespace app\controllers\config;
use app\controllers\retro\RetroController;
use app\controllers\retro\RetroControllerInterface;
use app\models\config\ModelsConfig;
class ControllersConfig {
    public static function get_retro_controller(): RetroControllerInterface {
        return new RetroController(ModelsConfig::get_retro_repository());
    }
}
