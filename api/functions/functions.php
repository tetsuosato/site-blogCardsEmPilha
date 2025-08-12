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