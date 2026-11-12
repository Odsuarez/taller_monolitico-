<?php
namespace app\models\config;
use app\models\database\RetroDB;
use app\models\database\RetroDBInterface;
use app\models\repositories\retro_repository\RetroRepository;
use app\models\repositories\retro_repository\RetroRepositoryInterface;
class ModelsConfig {
    public static function get_retro_db(): RetroDBInterface { return new RetroDB(); }
    public static function get_retro_repository(): RetroRepositoryInterface {
        return new RetroRepository(self::get_retro_db());
    }
}
