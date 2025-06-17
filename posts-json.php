<?php
header('Content-Type: application/json');

$posts = [
  [
    "id" => 1,
    "titulo" => "TÍTULO DA POSTAGEM 1",
    "categoria" => "Notícias",
    "tipo" => "Vídeo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-13 17:30",
    "resumo" => "Texto Resumo da postagem 1. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.",
    "imagem" => "imagesposts/postagem1.webp",
    "slug" => "titulo-da-postagem-1"
  ],
  [
    "id" => 2,
    "titulo" => "TÍTULO DA POSTAGEM 2",
    "categoria" => "Dicas",
    "tipo" => "Artigo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-13 17:30",
    "resumo" => "Texto Resumo da postagem 2. Aqui você pode colocar uma breve descrição do conteúdo, destacando os pontos principais ou o que o leitor pode esperar encontrar.",
    "imagem" => "imagesposts/postagem2.webp",
    "slug" => "titulo-da-postagem-2"
  ],
  [
    "id" => 3,
    "titulo" => "COMO GRAVAR PODCAST EM CASA",
    "categoria" => "Dicas",
    "tipo" => "Tutorial",
    "autor" => "Pablo Sato",
    "data" => "2025-06-12 14:00",
    "resumo" => "Confira um passo a passo simples para gravar podcasts com qualidade mesmo em casa.",
    "imagem" => "imagesposts/postagem3.webp",
    "slug" => "como-gravar-podcast-em-casa"
  ],
  [
    "id" => 4,
    "titulo" => "NOVIDADES DO ROCK EM 2025",
    "categoria" => "Notícias",
    "tipo" => "Artigo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-11 10:45",
    "resumo" => "As bandas de rock estão com muitos lançamentos! Veja os principais destaques de 2025 até agora.",
    "imagem" => "imagesposts/postagem4.webp",
    "slug" => "novidades-do-rock-2025"
  ],
  [
    "id" => 5,
    "titulo" => "TOP 5 DISCOS DE METAL DOS ANOS 90",
    "categoria" => "Listas",
    "tipo" => "Artigo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-10 11:15",
    "resumo" => "Relembramos cinco álbuns que marcaram o metal nos anos 1990. Será que o seu favorito está na lista?",
    "imagem" => "imagesposts/postagem5.webp",
    "slug" => "top-5-discos-metal-anos-90"
  ],
  [
    "id" => 6,
    "titulo" => "ENTREVISTA EXCLUSIVA COM NICKO MCBRAIN",
    "categoria" => "Entrevistas",
    "tipo" => "Vídeo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-09 16:20",
    "resumo" => "Conversamos com o lendário baterista do Iron Maiden sobre turnês, saúde e planos futuros.",
    "imagem" => "imagesposts/postagem6.webp",
    "slug" => "entrevista-nicko-mcbrain"
  ],
  [
    "id" => 7,
    "titulo" => "COMO FOI O ROCK IN RIO 2025",
    "categoria" => "Cobertura",
    "tipo" => "Reportagem",
    "autor" => "Pablo Sato",
    "data" => "2025-06-08 21:00",
    "resumo" => "Veja como foi o evento, com fotos, vídeos e detalhes dos principais shows do festival.",
    "imagem" => "imagesposts/postagem7.webp",
    "slug" => "como-foi-o-rock-in-rio-2025"
  ],
  [
    "id" => 8,
    "titulo" => "ÁLBUNS IMPERDÍVEIS DE 2024",
    "categoria" => "Listas",
    "tipo" => "Artigo",
    "autor" => "Pablo Sato",
    "data" => "2025-06-07 09:30",
    "resumo" => "Selecionamos os álbuns mais importantes do rock e metal lançados no ano passado.",
    "imagem" => "imagesposts/postagem8.webp",
    "slug" => "albuns-imperdiveis-2024"
  ],
  [
    "id" => 9,
    "titulo" => "DEEP PURPLE: STORMBRINGER EM DETALHES",
    "categoria" => "Clássicos",
    "tipo" => "Análise",
    "autor" => "Pablo Sato",
    "data" => "2025-06-06 15:10",
    "resumo" => "Análise completa do disco Stormbringer, com curiosidades, bastidores e crítica.",
    "imagem" => "imagesposts/postagem9.webp",
    "slug" => "deep-purple-stormbringer"
  ],
  [
    "id" => 10,
    "titulo" => "O RETORNO DO SYSTEM OF A DOWN?",
    "categoria" => "Rumores",
    "tipo" => "Notícia",
    "autor" => "Pablo Sato",
    "data" => "2025-06-05 20:00",
    "resumo" => "Boatos apontam para uma nova turnê do SOAD. Veja o que já sabemos até agora.",
    "imagem" => "imagesposts/postagem10.webp",
    "slug" => "retorno-do-system-of-a-down"
  ]
];

echo json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);