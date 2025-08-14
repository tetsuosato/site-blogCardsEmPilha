document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('lista-postagens');
    const btnMais = document.getElementById('btn-carregar-mais');
    let currentPage = 1;
    const perPage = 10;
    let loading = false;
    let hasMore = true;

    function slugify(str) {
    return str
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');
    }

    function formatarData(data) {
    const d = new Date(data);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
    });
    }

    function montarPostHTML(post) {
    const url = `postagem/${slugify(post.categoria)}/${post.slug}/${post.id}`;

    // criar container do post
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <hr class="theme-hr">
        <div class="row flex-xd-column g-0 overflow-hidden flex-md-row mb-4 h-md-300 position-relative shadow-lg postagemindex">
        <div class="postagemindextitulo">
            <a href="${url}" class="text-reset text-decoration-none">
            <strong>${post.titulo}</strong>
            </a>
        </div>
        <div class="col-md-6 postagemindexcolimg">
            <a href="${url}" class="linkimagem">
            <img src="${post.imagem}?t=${new Date().getTime()}" class="img-fluid rounded" alt="${post.titulo}">
            </a>
        </div>
        <div class="col-md-6 postagemindexcol">
            <p>
            <strong>${post.tipo}</strong> - <strong class="text-secondary">${post.categoria}</strong>
            <div class="mb-1 text-muted"><small>${post.autor}</small> - ${formatarData(post.data)}</div>
            </p>
            <p>${post.resumo}</p>
            <a href="${url}" class="btn btn-dark mt-2 theme-button">Continue lendo</a>
        </div>
        </div>
    `;
    return wrapper;
    }

    async function carregarPosts() {
    if (loading || !hasMore) return;
    loading = true;
    btnMais.disabled = true;
    btnMais.textContent = 'Carregando...';

    try {
        const resp = await fetch(`api/posts-index.php?page=${currentPage}&per_page=${perPage}/`);
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.json();

        if (!Array.isArray(data.posts)) {
        console.error('Resposta inesperada', data);
        return;
        }

        data.posts.forEach(post => {
        const postEl = montarPostHTML(post);
        container.appendChild(postEl);
        });

        hasMore = data.pagination.has_more;
        if (!hasMore) {
        btnMais.textContent = 'Não há mais posts';
        btnMais.disabled = true;
        } else {
        currentPage += 1;
        btnMais.textContent = 'Conferir Mais';
        btnMais.disabled = false;
        }
    } catch (err) {
        console.error('Erro ao carregar os posts:', err);
        btnMais.textContent = 'Tentar novamente';
        btnMais.disabled = false;
    } finally {
        loading = false;
    }
    }

    // evento do botão
    btnMais.addEventListener('click', carregarPosts);

    // carrega a primeira página automaticamente
    carregarPosts();
});