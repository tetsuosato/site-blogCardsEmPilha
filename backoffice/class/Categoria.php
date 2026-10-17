<?php
/**
 * Categorias das postagens.
 *
 * O nome aparece no site e também no endereço da postagem, mas apenas como
 * texto: a página é localizada pelo id e pelo slug do post. Renomear uma
 * categoria, portanto, não invalida os links já publicados.
 */
class Categoria {

    /** Lista as categorias com a quantidade de postagens de cada uma. */
    public static function listar() {
        return Database::get()->query("
            SELECT c.id, c.Nome, COUNT(p.id) AS postagens
            FROM categoria c
                LEFT JOIN posts p ON p.categoria = c.id
            GROUP BY c.id, c.Nome
            ORDER BY c.Nome ASC
        ")->fetchAll();
    }

    public static function buscar($id) {
        $consulta = Database::get()->prepare("
            SELECT id, Nome
            FROM categoria
            WHERE id = :id
            LIMIT 1
        ");
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    /**
     * Duas categorias com o mesmo nome só causariam confusão na hora de
     * escolher. $ignorarId permite renomear uma sem ela colidir consigo mesma.
     */
    public static function nomeEmUso($nome, $ignorarId = null) {
        $sql    = "SELECT COUNT(*) FROM categoria WHERE Nome = :nome";
        $params = [':nome' => $nome];

        if ($ignorarId !== null) {
            $sql .= " AND id <> :id";
            $params[':id'] = $ignorarId;
        }

        $consulta = Database::get()->prepare($sql);
        $consulta->execute($params);

        return (int) $consulta->fetchColumn() > 0;
    }

    /** Quantas postagens usam esta categoria. Zero é o que libera a exclusão. */
    public static function totalPosts($id) {
        $consulta = Database::get()->prepare("SELECT COUNT(*) FROM posts WHERE categoria = :id");
        $consulta->execute([':id' => $id]);

        return (int) $consulta->fetchColumn();
    }

    public static function criar(array $dados) {
        Database::get()
            ->prepare("INSERT INTO categoria (Nome) VALUES (:nome)")
            ->execute([':nome' => $dados['nome']]);
    }

    public static function atualizar($id, array $dados) {
        Database::get()
            ->prepare("UPDATE categoria SET Nome = :nome WHERE id = :id")
            ->execute([':id' => $id, ':nome' => $dados['nome']]);
    }

    public static function excluir($id) {
        Database::get()->prepare("DELETE FROM categoria WHERE id = :id")->execute([':id' => $id]);
    }
}
