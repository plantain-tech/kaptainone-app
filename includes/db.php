<?php
/**
 * Kaptain One - Database Connection Class
 * Simple PDO wrapper for Hostinger shared hosting
 */

require_once __DIR__ . '/../config/config.php';

class Database {
    private static ?PDO $instance = null;
    
    public static function connect(): PDO {
        global $DB_CONFIG;
        
        if (self::$instance === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $DB_CONFIG['host'],
                    $DB_CONFIG['port'],
                    $DB_CONFIG['database'],
                    $DB_CONFIG['charset']
                );
                
                self::$instance = new PDO($dsn, $DB_CONFIG['username'], $DB_CONFIG['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new Exception('Service temporarily unavailable');
            }
        }
        
        return self::$instance;
    }
    
    public static function query(string $sql, array $params = []): PDOStatement {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function fetch(string $sql, array $params = []): ?array {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function fetchAll(string $sql, array $params = []): array {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        self::query($sql, $data);
        return (int) self::connect()->lastInsertId();
    }
    
    public static function update(string $table, array $data, string $where, array $whereParams): void {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $set);
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        self::query($sql, array_merge($data, $whereParams));
    }
}
