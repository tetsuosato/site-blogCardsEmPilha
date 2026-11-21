<?php
require_once __DIR__ . '/Captcha.php';
require_once __DIR__ . '/LimiteLogin.php';

class LoginAuthentication {

    /**
     * Fica true quando a última tentativa foi recusada pelo limite de
     * tentativas, e não por dado errado — a tela usa isso para avisar que
     * é preciso esperar, já que digitar de novo não adiantaria.
     */
    public static $bloqueado = false;

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

        self::$bloqueado = false;

        $pdo = self::connect();
        if (!$pdo) {
            unset($_SESSION['captcha']);
            return false;
        }

        // Origem que já errou demais nem chega a ter a senha conferida: do
        // contrário um ataque continuaria descobrindo a senha certa, só que
        // sem conseguir entrar.
        if (LimiteLogin::bloqueado($pdo, $login)) {
            unset($_SESSION['captcha']);
            self::$bloqueado = true;
            return false;
        }

        // CAPTCHA — conferido antes da senha e descartado em seguida.
        // Errar o captcha também conta como tentativa.
        if (!Captcha::validar($captcha)) {
            LimiteLogin::registrarFalha($pdo, $login);
            return false;
        }

        try {
            $stmt = $pdo->prepare("
                SELECT id, user, password, name, lastname, email
                FROM users
                WHERE email = :email AND ativo = 1
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

                LimiteLogin::limpar($pdo, $login);

                return true;
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        LimiteLogin::registrarFalha($pdo, $login);

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
                WHERE token = :token AND ativo = 1
                LIMIT 1
            ");
            $stmt->execute([':token' => $token]);

            $user = $stmt->fetch();

            if (!$user) return false;

            $now = time();

            /*
             * token_expiry pode estar vazio num registro antigo. Passar null
             * para o strtotime gera um aviso de depreciação, e esse aviso é
             * impresso antes do header('Location') de quem chamou — o
             * redirecionamento falha e o login parece recusar a senha certa.
             */
            $expiryTime = strtotime((string) ($user['token_expiry'] ?? ''));

            // token sem validade legível ou expirado
            if ($expiryTime === false || $expiryTime < $now) {
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