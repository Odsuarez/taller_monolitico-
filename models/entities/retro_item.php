<?php
namespace app\models\entities;
class RetroItem {
    public function __construct(private int $id, private int $sprint_id, private string $categoria, private string $descripcion, private ?bool $cumplida) {}
    public function get_id(): int { return $this->id; }
    public function get_sprint_id(): int { return $this->sprint_id; }
    public function get_categoria(): string { return $this->categoria; }
    public function get_descripcion(): string { return $this->descripcion; }
    public function get_cumplida(): ?bool { return $this->cumplida; }
}
