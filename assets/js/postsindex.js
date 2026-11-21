document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('lista-postagens');
    const btnMais   = document.getElementById('btn-carregar-mais');
    let currentPage = 1;
    const perPage   = 10;
    // As 3 mais recentes já aparecem em "Últimas Postagens", no topo da página.
    const pular     = 3;
    const secao     = document.getElementById('secao-mais-postagens');
    let loading     = false;
    let hasMore     = true;

    // Dado da API entra na página como texto, nunca como HTML: um título
    // de vídeo contendo <img onerror=...> seria executado ao montar o card.
    function escapeHtml(valor) {
        return String(valor == null ? '' : valor)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function slugify(str) {
        return str.normalize('NFD').replace(/[̀-ͯ]/g, '')
            .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    }

    function formatarData(data) {
        return new Date(data).toLocaleDateString('pt-BR');
    }

    function montarPostHTML(post) {
        const url = BASE_URL + '/postagem/' + slugify(post.categoria) + '/' + post.slug + '/' + post.id;

        const wrapper = document.createElement('div');
        wrapper.className = 'col-sm-6';
        wrapper.innerHTML = `
            <div class="card h-100 theme-card">
                <div class="card-img-wrapper">
                    <img src="${BASE_URL}/${escapeHtml(post.imagem)}" alt="${escapeHtml(post.titulo)}" class="card-img-top">
                    <span class="card-categoria-badge">${escapeHtml(post.categoria)}</span>
                    <h5 class="card-img-title">${escapeHtml(post.titulo)}</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-meta">
                        <i class="bi bi-person-fill"></i> ${escapeHtml(post.autor)}
                        &nbsp;&middot;&nbsp;
                        <i class="bi bi-calendar3"></i> ${formatarData(post.data)}
                    </p>
                    <p class="card-text flex-grow-1">${escapeHtml(post.resumo)}</p>
                    <a href="${escapeHtml(url)}" class="card-read-more stretched-link mt-auto">Leia mais <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>`;
        return wrapper;
    }

    async function carregarPosts() {
        if (loading || !hasMore) return;
        loading = true;
        btnMais.disabled = true;
        btnMais.textContent = 'Carregando...';

        try {
            const resp = await fetch(BASE_URL + '/api/posts-index.php?page=' + currentPage + '&per_page=' + perPage + '&pular=' + pular);
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();

            if (!Array.isArray(data.posts)) return;

            // Até 3 posts no site, todos já estão no topo: a seção não tem o que mostrar.
            if (currentPage === 1 && data.posts.length === 0) {
                if (secao) secao.hidden = true;
                hasMore = false;
                return;
            }

            data.posts.forEach(function (post) {
                container.appendChild(montarPostHTML(post));
            });

            if (document.body.classList.contains('bg-dark')) {
                container.querySelectorAll('.theme-card').forEach(function (card) {
                    card.classList.add('bg-dark', 'text-light');
                    card.classList.remove('bg-light', 'text-dark');
                });
            }

            hasMore = data.pagination.has_more;
            if (!hasMore) {
                btnMais.textContent = 'Não há mais posts';
                btnMais.disabled = true;
            } else {
                currentPage += 1;
                btnMais.innerHTML = 'Carregar mais posts <i class="bi bi-arrow-down-circle"></i>';
                btnMais.disabled = false;
            }
        } catch (err) {
            console.error('Erro ao carregar posts:', err);
            btnMais.textContent = 'Tentar novamente';
            btnMais.disabled = false;
        } finally {
            loading = false;
        }
    }

    btnMais.addEventListener('click', carregarPosts);
    carregarPosts();
});
