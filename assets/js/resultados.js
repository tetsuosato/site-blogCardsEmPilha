document.addEventListener('DOMContentLoaded', function () {

    const urlParams = new URLSearchParams(window.location.search);
    const pesquisa  = urlParams.get('pesquisa') || '';

    const container = document.getElementById('lista-postagens');
    const btnMais   = document.getElementById('btn-carregar-mais');

    let currentPage = 1;
    const perPage   = 10;
    let loading     = false;
    let hasMore     = true;

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
                    <img src="${BASE_URL}/${post.imagem}" alt="${post.titulo}" class="card-img-top">
                    <span class="card-categoria-badge">${post.categoria}</span>
                    <h5 class="card-img-title">${post.titulo}</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-meta">
                        <i class="bi bi-person-fill"></i> ${post.autor}
                        &nbsp;&middot;&nbsp;
                        <i class="bi bi-calendar3"></i> ${formatarData(post.data)}
                    </p>
                    <p class="card-text flex-grow-1">${post.resumo}</p>
                    <a href="${url}" class="card-read-more stretched-link mt-auto">Leia mais <i class="bi bi-arrow-right"></i></a>
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
            const resp = await fetch(BASE_URL + '/api/posts-resultados.php?page=' + currentPage + '&per_page=' + perPage + '&pesquisa=' + encodeURIComponent(pesquisa));
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const data = await resp.json();

            if (!Array.isArray(data.posts)) return;

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
