<?php
namespace app\models\mappers;
use app\models\entities\Sprint;
use app\models\entities\RetroItem;
class RetroMappers {
    public static function to_sprint(array $row): Sprint {
        return new Sprint((int)$row['id'], $row['nombre'], $row['fecha_inicio'], $row['fecha_fin'], (bool)($row['finalizado'] ?? false));
    }
    public static function to_item(array $row): RetroItem {
        return new RetroItem((int)$row['id'], (int)$row['sprint_id'], $row['categoria'], $row['descripcion'], $row['cumplida'] === null ? null : (bool)$row['cumplida']);
    }
}
