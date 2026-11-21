<?php
/**
 * Limite de tentativas de login.
 *
 * Cada falha fica registrada na tabela login_tentativas. Dois limites valem
 * ao mesmo tempo, sempre dentro da mesma janela de tempo:
 *
 *  - por e-mail + IP: barra quem insiste em uma conta a partir de um lugar;
 *  - só por IP: barra quem testa várias contas diferentes de um mesmo lugar.
 *
 * O IP entra em ambos de propósito. Contar só pelo e-mail deixaria qualquer
 * pessoa, de qualquer lugar, trancar o administrador fora do painel errando
 * a senha dele de propósito.
 *
 * Se a tabela ainda não existir (o SQL de criação não foi rodado), o login
 * segue funcionando sem o limite e o problema vai para o log. Travar a
 * entrada por falta da tabela deixaria o próprio dono sem acesso ao painel.
 */
class LimiteLogin {

    const JANELA_MINUTOS    = 15;
    const MAX_POR_EMAIL_IP  = 5;
    const MAX_POR_IP        = 20;

    /** Registros mais antigos que isso já não servem para nada. */
    const GUARDAR_HORAS = 24;

    /** O índice da tabela comporta até 190 caracteres de e-mail. */
    const TAMANHO_EMAIL = 190;

    public static function ip() {
        return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
    }

    /** Mesmo e-mail digitado com outra caixa ou espaços conta como o mesmo. */
    private static function normalizarEmail($email) {
        return mb_substr(mb_strtolower(trim((string) $email), 'UTF-8'), 0, self::TAMANHO_EMAIL);
    }

    /** Diz se a origem já passou de algum dos limites. */
    public static function bloqueado(PDO $pdo, $email) {
        try {
            $consulta = $pdo->prepare("
                SELECT
                    COUNT(*) AS por_ip,
                    COALESCE(SUM(email = :email), 0) AS por_email
                FROM login_tentativas
                WHERE ip = :ip
                  AND `data` >= NOW() - INTERVAL " . (int) self::JANELA_MINUTOS . " MINUTE
            ");
            $consulta->execute([
                ':ip'    => self::ip(),
                ':email' => self::normalizarEmail($email),
            ]);
            $total = $consulta->fetch(PDO::FETCH_ASSOC);

            return (int) $total['por_email'] >= self::MAX_POR_EMAIL_IP
                || (int) $total['por_ip'] >= self::MAX_POR_IP;

        } catch (PDOException $e) {
            error_log('LimiteLogin (tabela login_tentativas criada?): ' . $e->getMessage());
            return false;
        }
    }

    /** Guarda uma falha e aproveita para descartar o que já venceu. */
    public static function registrarFalha(PDO $pdo, $email) {
        try {
            $pdo->prepare("INSERT INTO login_tentativas (ip, email, `data`) VALUES (:ip, :email, NOW())")
                ->execute([
                    ':ip'    => self::ip(),
                    ':email' => self::normalizarEmail($email),
                ]);

            $pdo->exec("DELETE FROM login_tentativas
                        WHERE `data` < NOW() - INTERVAL " . (int) self::GUARDAR_HORAS . " HOUR");

        } catch (PDOException $e) {
            error_log('LimiteLogin (tabela login_tentativas criada?): ' . $e->getMessage());
        }
    }

    /** Login certo zera as falhas daquele e-mail naquele IP. */
    public static function limpar(PDO $pdo, $email) {
        try {
            $pdo->prepare("DELETE FROM login_tentativas WHERE ip = :ip AND email = :email")
                ->execute([
                    ':ip'    => self::ip(),
                    ':email' => self::normalizarEmail($email),
                ]);

        } catch (PDOException $e) {
            error_log('LimiteLogin (tabela login_tentativas criada?): ' . $e->getMessage());
        }
    }
}
