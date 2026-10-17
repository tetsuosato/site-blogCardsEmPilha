<?php
/**
 * Tipos de postagem.
 *
 * A coluna Local separa os tipos em dois grupos, como diz o comentário da
 * tabela: 1 para vídeo, 0 para postagem escrita. A tela de publicar vídeo
 * lista apenas os de vídeo.
 */
class Tipo {

    const VIDEO    = 1;
    const POSTAGEM = 0;

    /** Lista os tipos com a quantidade de postagens de cada um. */
    public static function listar() {
        return Database::get()->query("
            SELECT t.id, t.Nome, t.Local, COUNT(p.id) AS postagens
            FROM tipo t
                LEFT JOIN posts p ON p.tipo = t.id
            GROUP BY t.id, t.Nome, t.Local
            ORDER BY t.Local DESC, t.Nome ASC
        ")->fetchAll();
    }

    public static function buscar($id) {
        $consulta = Database::get()->prepare("
            SELECT id, Nome, Local
            FROM tipo
            WHERE id = :id
            LIMIT 1
        ");
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    /**
     * Dois tipos com o mesmo nome só causariam confusão na hora de escolher.
     * $ignorarId permite renomear um tipo sem ele colidir consigo mesmo.
     */
    public static function nomeEmUso($nome, $ignorarId = null) {
        $sql    = "SELECT COUNT(*) FROM tipo WHERE Nome = :nome";
        $params = [':nome' => $nome];

        if ($ignorarId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $ignorarId;
        }

        $consulta = Database::get()->prepare($sql);
        $consulta->execute($params);

        return (int) $consulta->fetchColumn() > 0;
    }

    /** Quantas postagens usam este tipo. Zero é o que libera a exclusão. */
    public static function totalPosts($id) {
        $consulta = Database::get()->prepare("SELECT COUNT(*) FROM posts WHERE tipo = :id");
        $consulta->execute([':id' => $id]);

        return (int) $consulta->fetchColumn();
    }

    public static function criar(array $dados) {
        Database::get()
            ->prepare("INSERT INTO tipo (Nome, Local) VALUES (:nome, :local)")
            ->execute([
                ':nome'  => $dados['nome'],
                ':local' => $dados['local'],
            ]);
    }

    public static function atualizar($id, array $dados) {
        Database::get()
            ->prepare("UPDATE tipo SET Nome = :nome, Local = :local WHERE id = :id")
            ->execute([
                ':id'    => $id,
                ':nome'  => $dados['nome'],
                ':local' => $dados['local'],
            ]);
    }

    public static function excluir($id) {
        Database::get()->prepare("DELETE FROM tipo WHERE id = :id")->execute([':id' => $id]);
    }
}
