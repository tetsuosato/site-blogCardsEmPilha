/**
 * Tela de cadastro de vídeo: consulta o YouTube pelo link e prepara o
 * formulário com título, capa, player e tags.
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-video');
    if (!form) {
        return;
    }

    var link        = document.getElementById('link');
    var botao       = document.getElementById('btn-carregar');
    var aviso       = document.getElementById('aviso-video');
    var previa      = document.getElementById('previa');
    var blocoDados  = document.getElementById('bloco-dados');
    var campoVideo  = document.getElementById('video');
    var campoTitulo = document.getElementById('titulo');
    var campoConteudo = document.getElementById('conteudo');

    // Os endpoints ficam em backoffice/functions, um nível acima de main/.
    var urlDados  = new URL('../functions/youtube-dados.php', window.location.href).href;
    var urlUpload = new URL('../functions/imagem-upload.php', window.location.href).href;
    var csrf      = form.querySelector('[name="csrf"]').value;

    iniciarEditor();
    protegerEnvio();

    /**
     * Converte texto em base64 preservando acentos e emoji.
     * O btoa() sozinho só aceita Latin-1 e quebraria com esses caracteres.
     */
    function paraBase64(texto) {
        var bytes = new TextEncoder().encode(texto);
        var binario = '';

        for (var i = 0; i < bytes.length; i++) {
            binario += String.fromCharCode(bytes[i]);
        }

        return btoa(binario);
    }

    /**
     * Envia título, conteúdo e link codificados.
     *
     * O firewall da hospedagem (ModSecurity) recusa envios com <iframe> — todo
     * post traz o player do YouTube — e com endereços externos em parâmetros,
     * como o link do vídeo na criação do post. Codificados, os campos passam sem
     * ser confundidos com ataque; o servidor decodifica antes de validar.
     */
    function protegerEnvio() {
        // O link só existe na criação; na edição o campo não está na página.
        var campos = ['titulo', 'conteudo', 'link'];

        form.addEventListener('submit', function () {
            // Traz o que está no editor para a textarea antes de codificar.
            if (window.tinymce) {
                tinymce.triggerSave();
            }

            campos.forEach(function (nome) {
                var campo = form.querySelector('[name="' + nome + '"]');

                if (!campo) {
                    return;
                }

                var oculto = form.querySelector('input[name="' + nome + '_b64"]');

                if (!oculto) {
                    oculto = document.createElement('input');
                    oculto.type = 'hidden';
                    oculto.name = nome + '_b64';
                    form.appendChild(oculto);
                }

                oculto.value = paraBase64(campo.value);

                // Campo desativado não é enviado: só a versão codificada segue.
                campo.disabled = true;
            });
        });

        // Voltando pelo histórico a página sai do cache com os campos ainda
        // desativados; reativa para que continuem editáveis.
        window.addEventListener('pageshow', function () {
            campos.forEach(function (nome) {
                var campo = form.querySelector('[name="' + nome + '"]');

                if (campo) {
                    campo.disabled = false;
                }
            });
        });
    }

    /** Lê o conteúdo do editor, ou da textarea enquanto ele não abriu. */
    function lerConteudo() {
        var editor = window.tinymce && tinymce.get('conteudo');
        return editor ? editor.getContent() : campoConteudo.value;
    }

    /** Escreve no editor, ou na textarea enquanto ele não abriu. */
    function escreverConteudo(html) {
        var editor = window.tinymce && tinymce.get('conteudo');

        if (editor) {
            editor.setContent(html);
        } else {
            campoConteudo.value = html;
        }
    }

    function mostrarAviso(texto, estilo) {
        aviso.innerHTML = '<div class="alert alert-' + estilo + ' mb-0 py-2">' + texto + '</div>';
    }

    function limparAviso() {
        aviso.innerHTML = '';
    }

    async function carregarVideo() {
        var valor = link.value.trim();

        if (valor === '') {
            mostrarAviso('Cole o link do vídeo antes de carregar.', 'warning');
            return;
        }

        botao.disabled = true;
        botao.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Carregando...';
        limparAviso();

        try {
            // Codificado para o firewall não tratar o endereço externo como ataque.
            var resposta = await fetch(urlDados + '?link_b64=' + encodeURIComponent(paraBase64(valor)));
            var dados    = await resposta.json();

            if (!dados.ok) {
                mostrarAviso(dados.erro, 'danger');
                return;
            }

            campoVideo.value = dados.id;

            // Só preenche título e conteúdo se ainda estiverem vazios, para não
            // descartar o que já foi escrito ao recarregar outro link.
            if (campoTitulo.value.trim() === '') {
                campoTitulo.value = dados.titulo;
            }
            if (lerConteudo().trim() === '') {
                escreverConteudo(dados.conteudo);
            }

            document.getElementById('previa-capa').src      = dados.capa;
            document.getElementById('previa-canal').textContent = dados.autor;
            document.getElementById('previa-id').textContent    = dados.id;
            document.getElementById('previa-arquivo').textContent = dados.id + '.webp';

            previa.classList.remove('d-none');
            blocoDados.classList.remove('d-none');

            if (dados.jaCadastrado) {
                mostrarAviso(
                    'Atenção: este vídeo já tem a postagem <strong>#' + dados.jaCadastrado.id + '</strong> — ' +
                    dados.jaCadastrado.titulo + '. Salvar de novo vai gerar um aviso.',
                    'warning'
                );
            } else {
                mostrarAviso('Vídeo carregado. Confira os dados abaixo antes de salvar.', 'success');
            }

            campoTitulo.focus();

        } catch (erro) {
            mostrarAviso('Não foi possível consultar o vídeo. Verifique sua conexão.', 'danger');
        } finally {
            botao.disabled = false;
            botao.innerHTML = '<i class="bi bi-download"></i> Carregar vídeo';
        }
    }

    // ----- Atualizar a capa (só na edição) -----
    var botaoCapa = document.getElementById('btn-atualizar-capa');

    if (botaoCapa) {
        var urlCapa = new URL('../functions/youtube-capa.php', window.location.href).href;

        botaoCapa.addEventListener('click', async function () {
            var avisoCapa = document.getElementById('aviso-capa');
            var original  = botaoCapa.innerHTML;

            botaoCapa.disabled = true;
            botaoCapa.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Baixando...';
            avisoCapa.innerHTML = '';

            try {
                var dados = new FormData();
                dados.append('id', botaoCapa.dataset.post);

                var resposta = await fetch(urlCapa, {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': csrf },
                    body: dados
                });
                var retorno = await resposta.json();

                if (!retorno.ok) {
                    avisoCapa.innerHTML = '<div class="alert alert-danger py-1 px-2 mb-2">' + retorno.erro + '</div>';
                    return;
                }

                // A URL nova traz outra versão, então o navegador busca o arquivo
                // em vez de reaproveitar a capa antiga do cache.
                document.getElementById('capa-atual').src = retorno.capa;
                avisoCapa.innerHTML = '<div class="alert alert-success py-1 px-2 mb-2">'
                                    + 'Capa atualizada. Ela já aparece em todo o site.</div>';

            } catch (erro) {
                avisoCapa.innerHTML = '<div class="alert alert-danger py-1 px-2 mb-2">'
                                    + 'Não foi possível atualizar a capa. Verifique sua conexão.</div>';
            } finally {
                botaoCapa.disabled = false;
                botaoCapa.innerHTML = original;
            }
        });
    }

    // Na edição o vídeo já está definido e o passo do link nem aparece.
    if (botao && link) {
        botao.addEventListener('click', carregarVideo);

        // Enter no campo do link carrega o vídeo em vez de enviar o formulário.
        link.addEventListener('keydown', function (evento) {
            if (evento.key === 'Enter') {
                evento.preventDefault();
                carregarVideo();
            }
        });
    }

    // ----- Tags -----
    var caixaTags  = document.getElementById('tags-caixa');
    var entradaTag = document.getElementById('tag-entrada');
    var campoTags  = document.getElementById('tags');
    var tags       = [];

    function sincronizar() {
        campoTags.value = tags.join(',');
    }

    function desenhar() {
        // Remove as fichas atuais, preservando o campo de digitação.
        caixaTags.querySelectorAll('.bo-tag').forEach(function (ficha) {
            ficha.remove();
        });

        tags.forEach(function (tag, indice) {
            var ficha = document.createElement('span');
            ficha.className = 'bo-tag badge bg-secondary d-inline-flex align-items-center gap-1';
            ficha.textContent = tag;

            var fechar = document.createElement('button');
            fechar.type = 'button';
            fechar.className = 'btn-close btn-close-white';
            fechar.style.fontSize = '.5rem';
            fechar.setAttribute('aria-label', 'Remover ' + tag);
            fechar.addEventListener('click', function () {
                tags.splice(indice, 1);
                desenhar();
                sincronizar();
            });

            ficha.appendChild(fechar);
            caixaTags.insertBefore(ficha, entradaTag);
        });
    }

    function adicionarTag(texto) {
        texto = texto.trim().replace(/\s+/g, ' ');

        if (texto === '') {
            return;
        }

        var repetida = tags.some(function (tag) {
            return tag.toLowerCase() === texto.toLowerCase();
        });

        if (!repetida) {
            tags.push(texto.slice(0, 60));
            desenhar();
            sincronizar();
        }
    }

    entradaTag.addEventListener('keydown', function (evento) {
        if (evento.key === 'Enter' || evento.key === ',') {
            evento.preventDefault();
            adicionarTag(entradaTag.value);
            entradaTag.value = '';
        } else if (evento.key === 'Backspace' && entradaTag.value === '' && tags.length) {
            tags.pop();
            desenhar();
            sincronizar();
        }
    });

    // Tag digitada e deixada no campo ainda é aproveitada ao salvar.
    entradaTag.addEventListener('blur', function () {
        adicionarTag(entradaTag.value);
        entradaTag.value = '';
    });

    caixaTags.addEventListener('click', function () {
        entradaTag.focus();
    });

    // Recompõe as fichas quando a página volta com erro de validação.
    if (campoTags.value.trim() !== '') {
        campoTags.value.split(',').forEach(adicionarTag);
    }

    /** Abre o editor visual sobre a textarea do conteúdo. */
    function iniciarEditor() {
        if (!window.tinymce) {
            return;   // sem a biblioteca, a textarea continua utilizável
        }

        tinymce.init({
            selector: '#conteudo',
            language: 'pt_BR',
            height: 520,
            menubar: 'edit insert format table',
            branding: false,
            promotion: false,

            plugins: 'lists link image table code preview fullscreen searchreplace charmap wordcount autolink',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | '
                   + 'bullist numlist | link image table | removeformat | code preview fullscreen',

            // O player do vídeo é um iframe: sem isto o editor o apagaria ao abrir.
            extended_valid_elements: 'iframe[src|title|style|frameborder|allow|allowfullscreen|referrerpolicy|width|height|class|id]',

            // Mantém os endereços como foram escritos, em vez de encurtá-los
            // para caminhos relativos que quebrariam fora do backoffice.
            convert_urls: false,
            relative_urls: false,

            // Grava acentos como caracteres, e não como &uacute;, &ccedil;...
            // O banco é UTF-8 e os posts antigos já estão assim; com entidades,
            // o resumo dos cards aparecia como "M&uacute;sica" no site.
            entity_encoding: 'raw',

            image_caption: true,
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            images_upload_handler: enviarImagem,

            // Aproxima a área de edição da aparência do site.
            content_style: 'body{font-family:system-ui,-apple-system,"Segoe UI",sans-serif;font-size:16px;line-height:1.6;margin:1rem}'
                         + 'img{max-width:100%;height:auto}',

            setup: function (editor) {
                // Mantém a textarea espelhando o editor. O envio do formulário já
                // dispara o triggerSave, mas espelhar durante a edição evita perder
                // texto caso a página seja enviada por outro caminho.
                editor.on('change input undo redo SetContent', function () {
                    editor.save();
                });
            }
        });

        form.addEventListener('submit', function () {
            tinymce.triggerSave();
        });
    }

    /**
     * Sobe a imagem escolhida no editor. O endpoint devolve o endereço final
     * já convertido para WebP, que o TinyMCE insere no conteúdo.
     */
    function enviarImagem(informacoes, progresso) {
        return new Promise(function (resolver, rejeitar) {
            var dados = new FormData();
            dados.append('file', informacoes.blob(), informacoes.filename());

            var requisicao = new XMLHttpRequest();
            requisicao.open('POST', urlUpload);
            requisicao.setRequestHeader('X-CSRF-Token', csrf);

            requisicao.upload.onprogress = function (evento) {
                if (evento.lengthComputable) {
                    progresso(evento.loaded / evento.total * 100);
                }
            };

            requisicao.onload = function () {
                var resposta;

                try {
                    resposta = JSON.parse(requisicao.responseText);
                } catch (erro) {
                    rejeitar({ message: 'Resposta inesperada do servidor.', remove: true });
                    return;
                }

                if (requisicao.status !== 200 || !resposta.location) {
                    rejeitar({ message: resposta.error || 'Falha ao enviar a imagem.', remove: true });
                    return;
                }

                resolver(resposta.location);
            };

            requisicao.onerror = function () {
                rejeitar({ message: 'Não foi possível enviar a imagem.', remove: true });
            };

            requisicao.send(dados);
        });
    }
});
