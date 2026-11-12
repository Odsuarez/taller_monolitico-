<?php
namespace app\models\database;
use PDO;
use RuntimeException;
use Throwable;
class RetroDB implements RetroDBInterface {
    private PDO $conex;
    public function __construct(?PDO $connection = null) {
        $this->conex = $connection ?? new PDO(
            'mysql:host='.(getenv('DB_HOST') ?: 'localhost').';dbname='.(getenv('DB_NAME') ?: 'registro_retro_db').';charset=utf8mb4',
            getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: '',
            [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
        );
    }
    public function query_select(string $sql, array $params=[]): array {
        $q=$this->conex->prepare($sql); $q->execute($params); return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function query(string $sql, array $params=[]): bool { return $this->conex->prepare($sql)->execute($params); }
    public function last_id(): int { return (int)$this->conex->lastInsertId(); }
    public function transaction(callable $operation) {
        $mysql=$this->conex->getAttribute(PDO::ATTR_DRIVER_NAME)==='mysql';
        if ($mysql && (int)$this->conex->query("SELECT GET_LOCK('registro_retro_flujo', 10)")->fetchColumn()!==1) {
            throw new RuntimeException('Otra operación está en curso. Intenta de nuevo.');
        }
        try {
            $this->conex->beginTransaction();
            $result=$operation();
            $this->conex->commit();
            return $result;
        } catch (Throwable $ex) {
            if ($this->conex->inTransaction()) $this->conex->rollBack();
            throw $ex;
        } finally {
            if ($mysql) $this->conex->query("SELECT RELEASE_LOCK('registro_retro_flujo')");
        }
    }
}
