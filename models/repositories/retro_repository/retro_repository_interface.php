<?php
namespace app\models\repositories\retro_repository;
use app\models\entities\Sprint;
interface RetroRepositoryInterface {
    public function get_sprints(): array;
    public function get_items(): array;
    public function agendar(Sprint $sprint, array $objetivos): int;
    public function finalizar(int $id, array $realizadas, array $nuevos): void;
}
