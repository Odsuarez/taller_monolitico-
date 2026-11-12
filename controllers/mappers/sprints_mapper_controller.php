<?php
namespace app\controllers\mappers;
use app\models\entities\Sprint;
class SprintsMapperController {
    public static function to_entity(array $request): Sprint {
        return new Sprint(0, trim($request['nombre'] ?? ''), $request['inicio'] ?? '', $request['fin'] ?? '');
    }
}
