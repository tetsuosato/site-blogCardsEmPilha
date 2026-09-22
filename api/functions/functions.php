<?php 

// Função para gerar resumo do conteúdo
function gerar_resumo(string $conteudo, int $max_completo = 160): string {
    // Remove HTML e normaliza espaços
    $texto_limpo = trim(preg_replace('/\s+/', ' ', strip_tags($conteudo)));

    if (mb_strlen($texto_limpo) <= $max_completo) {
        return $texto_limpo;
    }

    // Queremos 157 chars + "..." = 160
    $parte = mb_substr($texto_limpo, 0, 157);
    return $parte . '...';
}

/**
 * Caminho da capa do post com a data de modificação do arquivo como versão.
 *
 * O nome da capa é sempre o identificador do vídeo, então ao atualizá-la o
 * endereço seria o mesmo e o navegador continuaria mostrando a antiga do
 * cache. A versão muda só quando o arquivo muda: fora isso a imagem segue
 * aproveitando o cache normalmente.
 */
function caminhoCapa($arquivo) {
    $relativo = 'images/img-youtube/' . $arquivo;
    $absoluto = dirname(__DIR__, 2) . '/' . $relativo;

    return is_file($absoluto) ? $relativo . '?v=' . filemtime($absoluto) : $relativo;
}

function slugify($string) {
    // Converte para minúsculas
    $slug = mb_strtolower($string, 'UTF-8');
    
    // Translitera acentos e caracteres especiais para ASCII
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);

    // Remove qualquer caractere que não seja letra, número ou espaço
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);

    // Substitui espaços por hífen
    $slug = preg_replace('/[\s-]+/', '-', $slug);

    // Remove hífens duplicados no início ou fim
    $slug = trim($slug, '-');

    return $slug;
}