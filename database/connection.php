<?php
// ============================================================
// Railway MySQL Connection + Database-Based Sessions
// Vercel ma file system write nathi hotu, etle
// sessions ne database ma store karva padse.
// ============================================================

$db_host     = getenv('DB_HOST')     ?: 'localhost';
$db_user     = getenv('DB_USER')     ?: 'root';
$db_pass     = getenv('DB_PASS')     ?: '';
$db_name     = getenv('DB_NAME')     ?: 'your_database';
$db_port     = getenv('DB_PORT')     ?: 3306;

// MySQLi Connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, (int)$db_port);

if ($conn->connect_error) {
    // Production ma error display na karo
    error_log("DB Connection failed: " . $conn->connect_error);
    // Graceful fallback
    $conn = null;
} else {
    $conn->set_charset("utf8mb4");
}

// ============================================================
// Custom DB-Based Session Handler
// Vercel serverless ma PHP sessions kaam nathi karta
// etle sessions ne MySQL ma store karva padshe
// ============================================================

class DbSessionHandler implements SessionHandlerInterface {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function open($path, $name): bool {
        return $this->conn !== null;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string|false {
        if (!$this->conn) return '';
        $stmt = $this->conn->prepare("SELECT data FROM php_sessions WHERE session_id = ? AND expires > NOW()");
        if (!$stmt) return '';
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['data'];
        }
        return '';
    }

    public function write($id, $data): bool {
        if (!$this->conn) return false;
        $expires = date('Y-m-d H:i:s', time() + 86400); // 1 day
        $stmt = $this->conn->prepare(
            "INSERT INTO php_sessions (session_id, data, expires) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE data = VALUES(data), expires = VALUES(expires)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("sss", $id, $data, $expires);
        return $stmt->execute();
    }

    public function destroy($id): bool {
        if (!$this->conn) return false;
        $stmt = $this->conn->prepare("DELETE FROM php_sessions WHERE session_id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc($max_lifetime): int|false {
        if (!$this->conn) return false;
        $this->conn->query("DELETE FROM php_sessions WHERE expires < NOW()");
        return true;
    }
}

// Session handler register karo (session_start() pehla)
if ($conn) {
    $handler = new DbSessionHandler($conn);
    session_set_save_handler($handler, true);
}
