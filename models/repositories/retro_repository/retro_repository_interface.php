<?php
namespace app\models\repositories\retro_repository;
use app\models\entities\Sprint;
interface RetroRepositoryInterface {
    public function get_item(int $id): \app\models\entities\RetroItem;
    public function update_item(int $id, string $categoria, string $descripcion, ?bool $cumplida): void;
    public function delete_item(int $id): void;
    public function get_sprint_abierto(int $id): Sprint;
    public function update_sprint(Sprint $sprint): void;
    public function get_sprints(): array;
    public function get_items(): array;
    public function agendar(Sprint $sprint, array $objetivos): int;
    public function finalizar(int $id, array $realizadas, array $nuevos): void;
}
