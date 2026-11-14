<?php
namespace app\controllers\retro;
use app\models\entities\Sprint;
class FechasRetrospectiva {
    public static function evaluar(?Sprint $sprint, string $hoy): array {
        return [
            'vencida'=>$sprint !== null && $hoy > $sprint->get_fecha_fin(),
            'anticipada'=>$sprint !== null && $hoy < $sprint->get_fecha_fin()
        ];
    }
}
