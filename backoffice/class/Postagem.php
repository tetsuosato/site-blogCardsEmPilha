<?php
require_once __DIR__ . '/../../api/functions/functions.php';
require_once __DIR__ . '/Imagem.php';
require_once __DIR__ . '/YouTube.php';

/**
 * Cadastro e consulta de postagens.
 */
class Postagem {

    /**
     * Tipos que podem ser escolhidos no backoffice.
     * A coluna Local marca quais tipos são publicados por aqui.
     */
    public static function tiposLocais() {
        return Database::get()->query("
            SELECT id, Nome
            FROM tipo
            WHERE Local = 1
            ORDER BY Nome ASC
        ")->fetchAll();
    }

    public static function categorias() {
        return Database::get()->query("
            SELECT id, Nome
            FROM categoria
            ORDER BY Nome ASC
        ")->fetchAll();
    }

    /**
     * Monta o slug a partir do título.
     *
     * Emoji e outros símbolos viram espaço antes da conversão porque o
     * iconv() usado pelo slugify() interrompe a transliteração ao encontrá-los
     * e devolveria um slug vazio — algo comum em títulos do YouTube.
     *
     * @param string $alternativo usado quando o título não sobra nada aproveitável
     */
    public static function gerarSlug($titulo, $alternativo = '') {
        // Mantém apenas Latim básico e estendido, que cobre os acentos do português.
        $limpo = preg_replace('/[^\x{0000}-\x{024F}]/u', ' ', (string) $titulo);

        $slug = slugify($limpo);

        if ($slug === '' && $alternativo !== '') {
            $slug = slugify($alternativo);
        }

        if ($slug === '') {
            $slug = 'post-' . date('YmdHis');
        }

        return mb_substr($slug, 0, 200);
    }

    /**
     * Garante que o slug não colida com outro post, acrescentando um número
     * ao final quando necessário.
     */
    public static function slugUnico($slug, $ignorarId = null) {
        $base     = $slug;
        $tentativa = 1;

        while (true) {
            $sql    = "SELECT COUNT(*) FROM posts WHERE slug = :slug";
            $params = [':slug' => $slug];

            if ($ignorarId !== null) {
                $sql .= " AND id <> :id";
                $params[':id'] = $ignorarId;
            }

            $consulta = Database::get()->prepare($sql);
            $consulta->execute($params);

            if ((int) $consulta->fetchColumn() === 0) {
                return $slug;
            }

            $tentativa++;
            $slug = $base . '-' . $tentativa;
        }
    }

    /**
     * Lista as postagens para a tela de gerenciamento.
     *
     * @param string $busca    procura no título e nas tags
     * @param string $situacao 'publicadas', 'agendadas' ou vazio para todas
     */
    public static function listar($busca = '', $situacao = '', $limite = 30, $deslocamento = 0) {
        list($onde, $params) = self::filtros($busca, $situacao);

        $sql = "SELECT p.id, p.titulo, p.slug, p.`data`, p.data_cadastro, p.imagem, p.urlimagem,
                       u.nickname AS autor, t.Nome AS tipo, c.Nome AS categoria
                FROM posts p
                    LEFT JOIN users u ON u.id = p.autor
                    LEFT JOIN tipo t ON t.id = p.tipo
                    LEFT JOIN categoria c ON c.id = p.categoria
                $onde
                ORDER BY p.`data` DESC
                LIMIT " . (int) $limite . " OFFSET " . (int) $deslocamento;

        $consulta = Database::get()->prepare($sql);
        $consulta->execute($params);

        return $consulta->fetchAll();
    }

    /** Total de postagens que atendem ao filtro, para montar a paginação. */
    public static function contar($busca = '', $situacao = '') {
        list($onde, $params) = self::filtros($busca, $situacao);

        $consulta = Database::get()->prepare("SELECT COUNT(*) FROM posts p $onde");
        $consulta->execute($params);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Monta o WHERE compartilhado entre a listagem e a contagem, para as duas
     * nunca divergirem.
     *
     * @return array [trecho sql, parâmetros]
     */
    private static function filtros($busca, $situacao) {
        $condicoes = [];
        $params    = [];

        if (trim($busca) !== '') {
            // Dois marcadores com o mesmo termo: sem emulação de prepared
            // statements o PDO não aceita repetir um parâmetro na consulta.
            $condicoes[] = "(p.titulo LIKE :buscaTitulo OR p.tags LIKE :buscaTags)";
            $params[':buscaTitulo'] = '%' . trim($busca) . '%';
            $params[':buscaTags']   = '%' . trim($busca) . '%';
        }

        if ($situacao === 'agendadas') {
            $condicoes[] = "p.`data` > NOW()";
        } elseif ($situacao === 'publicadas') {
            $condicoes[] = "p.`data` <= NOW()";
        }

        return [$condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '', $params];
    }

    /** Carrega uma postagem completa para edição. */
    public static function buscar($id) {
        $consulta = Database::get()->prepare("
            SELECT id, titulo, slug, autor, `data`, data_cadastro, tipo, categoria,
                   imagem, urlimagem, conteudo, tags
            FROM posts
            WHERE id = :id
            LIMIT 1
        ");
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    /**
     * Atualiza a postagem.
     *
     * O vídeo e a capa não mudam aqui: trocar o vídeo significaria outra
     * postagem. A data de cadastro também é preservada — ela registra quando
     * o post nasceu, não quando foi mexido.
     */
    public static function atualizar($id, array $dados) {
        $sql = "UPDATE posts
                SET titulo = :titulo, `data` = :data, tipo = :tipo,
                    categoria = :categoria, conteudo = :conteudo, tags = :tags";

        $params = [
            ':id'        => $id,
            ':titulo'    => $dados['titulo'],
            ':data'      => $dados['data'],
            ':tipo'      => $dados['tipo'],
            ':categoria' => $dados['categoria'],
            ':conteudo'  => $dados['conteudo'],
            ':tags'      => json_encode($dados['tags'], JSON_UNESCAPED_UNICODE),
        ];

        // O endereço da postagem só muda quando pedido, para não quebrar links
        // que já foram compartilhados.
        if (!empty($dados['slug'])) {
            $sql .= ", slug = :slug";
            $params[':slug'] = $dados['slug'];
        }

        $sql .= " WHERE id = :id";

        Database::get()->prepare($sql)->execute($params);
    }

    /**
     * Exclui a postagem e os arquivos que só ela usava.
     *
     * @return array ['capa' => bool, 'imagens' => int] o que foi removido do disco
     */
    public static function excluir($id) {
        $post = self::buscar($id);

        if (!$post) {
            return ['capa' => false, 'imagens' => 0];
        }

        // As imagens do conteúdo precisam ser lidas antes de a linha sumir,
        // senão a checagem de uso não encontraria mais nada.
        $imagens = self::imagensDoConteudo($post['conteudo']);

        Database::get()->prepare("DELETE FROM posts WHERE id = :id")->execute([':id' => $id]);

        $removidas = self::limparImagensOrfas($imagens, '');

        // A capa leva o nome do vídeo e não é compartilhada, mas a conferência
        // evita apagar algo que outra postagem ainda exiba.
        $capaRemovida = false;
        if (!empty($post['imagem']) && !self::capaEmUso($post['imagem'])) {
            $caminho = YouTube::pastaCapas() . '/' . basename($post['imagem']);
            $capaRemovida = is_file($caminho) && @unlink($caminho);
        }

        return ['capa' => $capaRemovida, 'imagens' => count($removidas)];
    }

    /** Diz se alguma postagem ainda usa esta capa. */
    public static function capaEmUso($arquivo) {
        $consulta = Database::get()->prepare("SELECT COUNT(*) FROM posts WHERE imagem = :arquivo");
        $consulta->execute([':arquivo' => $arquivo]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /** Extrai os nomes das imagens de images/img-post citadas no conteúdo. */
    public static function imagensDoConteudo($conteudo) {
        preg_match_all('~img-post/([A-Za-z0-9._-]+\.webp)~i', (string) $conteudo, $achados);

        return array_unique($achados[1]);
    }

    /** Procura um post já cadastrado para o mesmo vídeo. */
    public static function porVideo($videoId) {
        $consulta = Database::get()->prepare("
            SELECT id, titulo
            FROM posts
            WHERE urlimagem = :url OR imagem = :arquivo
            LIMIT 1
        ");
        $consulta->execute([
            ':url'     => 'https://youtu.be/' . $videoId,
            ':arquivo' => $videoId . '.webp',
        ]);

        return $consulta->fetch();
    }

    /**
     * Grava a postagem.
     *
     * A coluna data guarda quando o post aparece no site (pode ser futura,
     * para agendamento) e data_cadastro quando ele foi criado.
     *
     * @return int id gerado
     */
    public static function criar(array $dados) {
        $pdo = Database::get();

        $comando = $pdo->prepare("
            INSERT INTO posts
                (titulo, slug, autor, `data`, data_cadastro, tipo, categoria, imagem, urlimagem, conteudo, tags)
            VALUES
                (:titulo, :slug, :autor, :data, NOW(), :tipo, :categoria, :imagem, :urlimagem, :conteudo, :tags)
        ");

        $comando->execute([
            ':titulo'    => $dados['titulo'],
            ':slug'      => $dados['slug'],
            ':autor'     => $dados['autor'],
            ':data'      => $dados['data'],
            ':tipo'      => $dados['tipo'],
            ':categoria' => $dados['categoria'],
            ':imagem'    => $dados['imagem'],
            ':urlimagem' => $dados['urlimagem'],
            ':conteudo'  => $dados['conteudo'],
            ':tags'      => json_encode($dados['tags'], JSON_UNESCAPED_UNICODE),
        ]);

        return (int) $pdo->lastInsertId();
    }

    /** Diz se alguma postagem usa esta imagem no conteúdo. */
    public static function imagemReferenciada($arquivo) {
        $consulta = Database::get()->prepare("
            SELECT COUNT(*)
            FROM posts
            WHERE conteudo LIKE :busca
        ");
        $consulta->execute([':busca' => '%' . $arquivo . '%']);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Apaga as imagens que foram enviadas mas acabaram não entrando no post.
     *
     * Acontece ao trocar de imagem no meio da edição: a anterior continuaria
     * ocupando espaço sem nunca aparecer no site. Só são consideradas as
     * imagens enviadas nesta sessão, e cada uma ainda é conferida contra todas
     * as postagens antes de sumir — uma imagem reaproveitada em outro post não
     * pode ser apagada.
     *
     * @param  array  $candidatos     nomes de arquivo enviados durante a edição
     * @param  string $conteudoSalvo  conteúdo que acabou de ser gravado
     * @return array  nomes removidos
     */
    public static function limparImagensOrfas(array $candidatos, $conteudoSalvo) {
        $pasta    = Imagem::pastaPosts();
        $removidas = [];

        foreach (array_unique($candidatos) as $arquivo) {
            // Aceita apenas o nome do arquivo, nunca um caminho.
            $arquivo = basename((string) $arquivo);
            $caminho = $pasta . '/' . $arquivo;

            if ($arquivo === '' || !is_file($caminho)) {
                continue;
            }

            // Está no post recém-salvo.
            if (strpos($conteudoSalvo, $arquivo) !== false) {
                continue;
            }

            // Está em alguma outra postagem.
            if (self::imagemReferenciada($arquivo)) {
                continue;
            }

            if (@unlink($caminho)) {
                $removidas[] = $arquivo;
            }
        }

        return $removidas;
    }

    /**
     * Normaliza a lista de tags: remove vazias e repetidas, preservando a
     * ordem em que foram digitadas.
     */
    public static function normalizarTags($tags) {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        if (!is_array($tags)) {
            return [];
        }

        $limpas = [];

        foreach ($tags as $tag) {
            $tag = trim(preg_replace('/\s+/u', ' ', (string) $tag));

            if ($tag === '') {
                continue;
            }

            // Comparação sem diferenciar maiúsculas evita "Rock" e "rock" juntas.
            $chave = mb_strtolower($tag, 'UTF-8');

            if (!isset($limpas[$chave])) {
                $limpas[$chave] = mb_substr($tag, 0, 60);
            }
        }

        return array_values($limpas);
    }
}
