<?php
class Usuario {

    public static function listar() {
        return Database::get()->query("
            SELECT id, name, lastname, nickname, user, email, ativo, data_creation, data_login
            FROM users
            ORDER BY name ASC, lastname ASC
        ")->fetchAll();
    }

    public static function buscar($id) {
        $stmt = Database::get()->prepare("
            SELECT id, name, lastname, nickname, user, email, ativo
            FROM users
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Um mesmo e-mail ou login não pode se repetir entre usuários.
     * $ignorarId permite editar um usuário sem colidir com ele mesmo.
     */
    public static function emUso($campo, $valor, $ignorarId = null) {
        if (!in_array($campo, ['email', 'user'], true)) {
            throw new InvalidArgumentException('Campo inválido.');
        }

        $sql = "SELECT COUNT(*) FROM users WHERE `$campo` = :valor";
        $params = [':valor' => $valor];

        if ($ignorarId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $ignorarId;
        }

        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function criar(array $dados) {
        $stmt = Database::get()->prepare("
            INSERT INTO users (name, lastname, nickname, user, password, email, ativo, token, token_expiry, data_creation, data_login)
            VALUES (:name, :lastname, :nickname, :user, :password, :email, :ativo, '', '1000-01-01 00:00:00', NOW(), '1000-01-01 00:00:00')
        ");

        $stmt->execute([
            ':name'     => $dados['name'],
            ':lastname' => $dados['lastname'],
            ':nickname' => $dados['nickname'],
            ':user'     => $dados['user'],
            ':password' => password_hash($dados['password'], PASSWORD_DEFAULT),
            ':email'    => $dados['email'],
            ':ativo'    => $dados['ativo'],
        ]);
    }

    public static function atualizar($id, array $dados) {
        $sql = "UPDATE users
                SET name = :name, lastname = :lastname, nickname = :nickname,
                    user = :user, email = :email, ativo = :ativo";

        $params = [
            ':id'       => $id,
            ':name'     => $dados['name'],
            ':lastname' => $dados['lastname'],
            ':nickname' => $dados['nickname'],
            ':user'     => $dados['user'],
            ':email'    => $dados['email'],
            ':ativo'    => $dados['ativo'],
        ];

        // Senha em branco significa "manter a atual".
        //
        // Senha nova derruba a sessão que estiver aberta com a antiga: é para
        // isso que se troca a senha quando se desconfia que ela vazou. Quem
        // troca a própria senha recebe um token novo logo em seguida
        // (renovarToken), então só a sessão dos outros lugares cai.
        if ($dados['password'] !== '') {
            $sql .= ", password = :password, token = '', token_expiry = '1000-01-01 00:00:00'";
            $params[':password'] = password_hash($dados['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";

        Database::get()->prepare($sql)->execute($params);
    }

    /**
     * Gera um token novo para o usuário e devolve o valor, para gravar na
     * sessão de quem acabou de trocar a própria senha. Mesma validade de um
     * login: 1 dia.
     */
    public static function renovarToken($id) {
        $token = bin2hex(random_bytes(32));

        Database::get()
            ->prepare("UPDATE users SET token = :token, token_expiry = :expiry WHERE id = :id")
            ->execute([
                ':token'  => $token,
                ':expiry' => date('Y-m-d H:i:s', strtotime('+1 day')),
                ':id'     => $id,
            ]);

        return $token;
    }

    public static function excluir($id) {
        Database::get()->prepare("DELETE FROM users WHERE id = :id")->execute([':id' => $id]);
    }

    public static function alternarAtivo($id) {
        Database::get()->prepare("UPDATE users SET ativo = IF(ativo = 1, 0, 1) WHERE id = :id")
                       ->execute([':id' => $id]);
    }

    public static function totalPosts($id) {
        $stmt = Database::get()->prepare("SELECT COUNT(*) FROM posts WHERE autor = :id");
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }
}
