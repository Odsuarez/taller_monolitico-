<?php
namespace app\models\entities;
class Sprint {
    public function __construct(private int $id, private string $nombre, private string $fecha_inicio, private string $fecha_fin, private bool $finalizado = false) {}
    public function esta_finalizado(): bool { return $this->finalizado; }
    public function get_id(): int { return $this->id; }
    public function get_nombre(): string { return $this->nombre; }
    public function get_fecha_inicio(): string { return $this->fecha_inicio; }
    public function get_fecha_fin(): string { return $this->fecha_fin; }
}
