<?php
/**
 * Persistencia SQLite. Los datos se guardan fuera del directorio público.
 * No se importan automáticamente las cuentas de demostración del proyecto de 2022.
 */
class Dao
{
    public static function dataDir(): string {
        $dir = getenv('GOBLIN_DATA_DIR') ?: dirname(__DIR__, 2) . '/var';
        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new RuntimeException('No se puede crear el directorio de datos.');
        }
        return $dir;
    }

    public static function db(): PDO {
        static $db = null;
        if ($db !== null) return $db;
        $db = new PDO('sqlite:' . self::dataDir() . '/goblin.sqlite', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $db->exec('PRAGMA busy_timeout = 5000');
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            name TEXT PRIMARY KEY COLLATE NOCASE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'usuario',
            email TEXT NOT NULL DEFAULT '',
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS wins (
            id TEXT PRIMARY KEY, owner TEXT NOT NULL COLLATE NOCASE,
            character_json TEXT NOT NULL, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            bucket TEXT PRIMARY KEY, attempts INTEGER NOT NULL, started INTEGER NOT NULL
        )");
        return $db;
    }

    public static function findUser(string $name): ?array {
        $query = self::db()->prepare('SELECT * FROM users WHERE name = ?');
        $query->execute([$name]);
        return $query->fetch() ?: null;
    }

    public static function register(string $name, string $password, string $email = ''): bool {
        $query = self::db()->prepare('INSERT OR IGNORE INTO users(name, password, email) VALUES (?, ?, ?)');
        $query->execute([$name, password_hash($password, PASSWORD_DEFAULT), $email]);
        return $query->rowCount() === 1;
    }

    // Shared across sessions; opening a new browser session does not reset the limit.
    public static function loginAllowed(string $ip): bool {
        $db = self::db();
        $now = time();
        $bucket = hash('sha256', $ip);
        $db->prepare('DELETE FROM login_attempts WHERE started < ?')->execute([$now - 900]);
        $query = $db->prepare('INSERT INTO login_attempts(bucket, attempts, started) VALUES (?, 1, ?)
            ON CONFLICT(bucket) DO UPDATE SET attempts = attempts + 1 RETURNING attempts');
        $query->execute([$bucket, $now]);
        return (int) $query->fetchColumn() <= 20;
    }

    public static function saveWin(string $id, string $owner, Xogador $player): void {
        $data = [$player->getNombre(), $player->getClase(), $player->getVida(),
            $player->getAtaque(), $player->getDefensa(), $player->getEsquiva(), $player->getInventario()];
        self::db()->prepare('INSERT OR IGNORE INTO wins(id, owner, character_json) VALUES (?, ?, ?)')
            ->execute([$id, $owner, json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]);
    }

    public static function wins(string $owner): array {
        $query = self::db()->prepare('SELECT character_json FROM wins WHERE owner = ? ORDER BY created_at DESC LIMIT 100');
        $query->execute([$owner]);
        return array_map(fn($row) => json_decode($row['character_json'], true, 512, JSON_THROW_ON_ERROR), $query->fetchAll());
    }

    public static function users(): array {
        return self::db()->query('SELECT name, role, created_at FROM users ORDER BY name')->fetchAll();
    }

    public static function deleteUser(string $name): void {
        $db = self::db();
        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM wins WHERE owner = ?')->execute([$name]);
            $db->prepare("DELETE FROM users WHERE name = ? AND role <> 'administrador'")->execute([$name]);
            $db->commit();
        } catch (Throwable $error) {
            $db->rollBack();
            throw $error;
        }
    }
}
