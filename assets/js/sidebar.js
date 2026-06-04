document.addEventListener('DOMContentLoaded', function () {

    function slugify(str) {
        return str.normalize('NFD').replace(/[̀-ͯ]/g, '')
            .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    }

    function formatarData(data) {
        return new Date(data).toLocaleDateString('pt-BR');
    }

    function aplicarTemaCards(container) {
        if (document.body.classList.contains('bg-dark')) {
            container.querySelectorAll('.theme-card').forEach(function (el) {
                el.classList.add('bg-dark', 'text-light');
                el.classList.remove('bg-light', 'text-dark');
            });
        }
    }

    // --- Recentes ---
    var recentes = document.getElementById('sidebar-recentes');
    if (recentes) {
        fetch(BASE_URL + '/api/posts-index.php?page=1&per_page=4')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.posts || !data.posts.length) return;
                data.posts.forEach(function (post) {
                    var url  = BASE_URL + '/postagem/' + slugify(post.categoria) + '/' + post.slug + '/' + post.id;
                    var item = document.createElement('a');
                    item.href      = url;
                    item.className = 'sidebar-post d-flex gap-2 align-items-start text-decoration-none';
                    item.innerHTML = `
                        <img src="${BASE_URL}/${post.imagem}" alt="${post.titulo}" class="sidebar-post-img rounded">
                        <div>
                            <p class="sidebar-post-title mb-0">${post.titulo}</p>
                            <small class="text-muted">${formatarData(post.data)}</small>
                        </div>
                    `;
                    recentes.appendChild(item);
                });
                aplicarTemaCards(recentes.closest('.sidebar-block') || document);
            })
            .catch(function () {});
    }

    // --- Categorias ---
    var catContainer = document.getElementById('sidebar-categorias');
    if (catContainer) {
        fetch(BASE_URL + '/api/categorias.php')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!Array.isArray(data)) return;
                data.forEach(function (item) {
                    var tag       = document.createElement('a');
                    tag.href      = BASE_URL + '/resultados?pesquisa=' + encodeURIComponent(item.categoria);
                    tag.className = 'sidebar-tag';
                    tag.textContent = item.categoria;
                    catContainer.appendChild(tag);
                });
            })
            .catch(function () {});
    }

});
