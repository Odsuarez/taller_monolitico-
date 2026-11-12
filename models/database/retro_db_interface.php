<?php
namespace app\models\database;
interface RetroDBInterface {
    public function query_select(string $sql, array $params = []): array;
    public function query(string $sql, array $params = []): bool;
    public function last_id(): int;
    public function transaction(callable $operation);
}
