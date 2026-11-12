<?php
namespace app\models\repositories\retro_repository;
use app\models\database\RetroDBInterface;
use app\models\entities\Sprint;
use app\models\mappers\RetroMappers;
use RuntimeException;
class RetroRepository implements RetroRepositoryInterface {
    private const CIERRE = 'Retrospectiva finalizada.';
    private function esta_finalizado(int $id): bool {
        return (bool)$this->db->query_select(
            "SELECT id FROM retro_items WHERE sprint_id = ? AND categoria = 'otro' AND descripcion = ? AND fecha_revision IS NOT NULL",
            [$id, self::CIERRE]
        );
    }
    public function __construct(private RetroDBInterface $db) {}
    public function get_sprints(): array {
        return array_map([RetroMappers::class,'to_sprint'], $this->db->query_select(
            "SELECT s.*, EXISTS (SELECT 1 FROM retro_items r WHERE r.sprint_id = s.id AND r.categoria = 'otro' AND r.descripcion = ? AND r.fecha_revision IS NOT NULL) AS finalizado FROM sprints s ORDER BY s.fecha_inicio DESC, s.id DESC",
            [self::CIERRE]
        ));
    }
    public function get_items(): array {
        return array_map([RetroMappers::class,'to_item'], $this->db->query_select(
            "SELECT * FROM retro_items WHERE NOT (categoria = 'otro' AND descripcion = ? AND fecha_revision IS NOT NULL) ORDER BY id",
            [self::CIERRE]
        ));
    }
    public function agendar(Sprint $sprint, array $objetivos): int {
        return $this->db->transaction(function() use ($sprint,$objetivos) {
            if ($this->db->query_select(
                "SELECT s.id FROM sprints s WHERE NOT EXISTS (SELECT 1 FROM retro_items r WHERE r.sprint_id = s.id AND r.categoria = 'otro' AND r.descripcion = ? AND r.fecha_revision IS NOT NULL)",
                [self::CIERRE]
            )) {
                throw new RuntimeException('Primero finaliza la retrospectiva del sprint abierto.');
            }
            $this->db->query("INSERT INTO sprints (nombre,fecha_inicio,fecha_fin) VALUES (?,?,?)",
                [$sprint->get_nombre(),$sprint->get_fecha_inicio(),$sprint->get_fecha_fin()]);
            $id=$this->db->last_id();
            foreach ($objetivos as $texto) $this->db->query("INSERT INTO retro_items (sprint_id,categoria,descripcion,cumplida) VALUES (?,'accion',?,0)",[$id,$texto]);
            return $id;
        });
    }
    public function finalizar(int $id, array $realizadas, array $nuevos): void {
        $this->db->transaction(function() use ($id,$realizadas,$nuevos) {
            $rows=$this->db->query_select('SELECT * FROM sprints WHERE id = ?',[$id]);
            if (!$rows || $this->esta_finalizado($id)) throw new RuntimeException('Este sprint ya se finalizó o no existe.');
            if ($rows[0]['fecha_inicio'] > date('Y-m-d')) throw new RuntimeException('Este sprint aún no ha comenzado.');
            $acciones=$this->db->query_select("SELECT id FROM retro_items WHERE sprint_id = ? AND categoria = 'accion'",[$id]);
            $validos=array_map('intval',array_column($acciones,'id'));
            if (array_diff($realizadas,$validos)) throw new RuntimeException('Hay objetivos que no pertenecen a este sprint.');
            foreach ($validos as $itemId) $this->db->query('UPDATE retro_items SET cumplida = ? WHERE id = ? AND sprint_id = ?', [in_array($itemId,$realizadas,true) ? 1 : 0,$itemId,$id]);
            foreach ($nuevos as $categoria=>$textos) {
                foreach ($textos as $texto) $this->db->query('INSERT INTO retro_items (sprint_id,categoria,descripcion,cumplida) VALUES (?,?,?,?)',
                    [$id,$categoria,$texto,$categoria==='accion' ? 1 : null]);
            }
            $this->db->query(
                "INSERT INTO retro_items (sprint_id,categoria,descripcion,fecha_revision) VALUES (?,'otro',?,?)",
                [$id,self::CIERRE,date('Y-m-d')]
            );
        });
    }
}
