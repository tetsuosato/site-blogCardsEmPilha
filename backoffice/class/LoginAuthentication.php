<?php
class LoginAuthentication {

    private static function connect() {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            error_log($e->getMessage()); // não expõe erro ao usuário
            return null;
        }
    }

    public static function authenticate($login, $password, $captcha) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // CAPTCHA
        if (!isset($_SESSION['captcha']) || $captcha !== $_SESSION['captcha']) {
            return false;
        }

        $pdo = self::connect();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("
                SELECT id, user, password, name, lastname, email 
                FROM users 
                WHERE email = :email
                LIMIT 1
            ");
            $stmt->execute([':email' => $login]);

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                // 🔐 proteção contra session fixation
                session_regenerate_id(true);

                $token = bin2hex(random_bytes(32));
                $now = date("Y-m-d H:i:s");
                $expiry = date("Y-m-d H:i:s", strtotime("+1 day"));

                $update = $pdo->prepare("
                    UPDATE users 
                    SET token = :token,
                        token_expiry = :expiry,
                        data_login = :now
                    WHERE id = :id
                ");

                $update->execute([
                    ':token' => $token,
                    ':expiry' => $expiry,
                    ':now' => $now,
                    ':id' => $user['id']
                ]);

                // sessão
                $_SESSION['token'] = $token;
                $_SESSION['id_user'] = $user['id'];
                $_SESSION['user'] = $user['user'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['lastname'] = $user['lastname'];
                $_SESSION['email'] = $user['email'];

                return true;
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return false;
    }

    public static function validateToken($token) {
        if (empty($token)) return false;

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $pdo = self::connect();
        if (!$pdo) return false;

        try {
            $stmt = $pdo->prepare("
                SELECT id, token_expiry 
                FROM users 
                WHERE token = :token
                LIMIT 1
            ");
            $stmt->execute([':token' => $token]);

            $user = $stmt->fetch();

            if (!$user) return false;

            $now = time();
            $expiryTime = strtotime($user['token_expiry']);

            // token expirado
            if ($expiryTime < $now) {
                return false;
            }

            // 🔥 RENOVAÇÃO INTELIGENTE
            // só renova se faltar menos de 6h
            if (($expiryTime - $now) < (6 * 3600)) {

                $newExpiry = date("Y-m-d H:i:s", strtotime("+1 day"));

                $update = $pdo->prepare("
                    UPDATE users 
                    SET token_expiry = :expiry 
                    WHERE id = :id
                ");

                $update->execute([
                    ':expiry' => $newExpiry,
                    ':id' => $user['id']
                ]);
            }

            return true;

        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return false;
    }
}
?>