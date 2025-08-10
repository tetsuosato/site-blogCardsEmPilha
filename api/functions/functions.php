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