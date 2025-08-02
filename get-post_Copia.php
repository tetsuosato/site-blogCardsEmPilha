<?php
header('Content-Type: application/json');

// Simular parâmetros recebidos via GET
$categoria = $_GET['categoria'] ?? '';
$slug = $_GET['slug'] ?? '';
$id = $_GET['id'] ?? '';

if ($categoria === '' || $slug === '' || $id === '') {
    echo json_encode(['erro' => 'Parâmetros inválidos ou incompletos.']);
    exit;
}

// Simulando uma postagem fixa só para teste
$post = [
    'titulo' => 'Minha Primeira Postagem:'.$id,
    'autor' => 'Pablo Sato',
    'data' => "07/07/2025 18:30",
    'categoria' => ucfirst($categoria),
    'imagem' => 'imagesposts/postagem'.$id.'.webp',
    'altimagem' => 'imagem principal da postagem',
    'conteudo' => 
        "   <p> <strong>Lorem ipsum</strong> dolor sit amet, consectetur adipiscing elit. Vivamus suscipit, nisl ut vehicula hendrerit, 
            urna tortor tincidunt arcu, vel porttitor nibh tellus nec sem.
            Aenean quis magna sapien. Praesent ullamcorper, massa at lacinia tempus, justo nunc dapibus quam, 
            a malesuada odio lorem ut justo
            Proin vel purus nec libero lacinia convallis. 
            Fusce commodo magna sed fermentum convallis. Morbi rhoncus nulla nec ex imperdiet mattis
            Mauris congue finibus turpis nec ultricies. Vestibulum ante ipsum primis in faucibus orci 
            luctus et ultrices posuere cubilia curae.
            </p>
        "
    ,
    'tags' => ['Dicas', 'Rock', 'Metal']
];

// Retorna o JSON
echo json_encode($post, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);