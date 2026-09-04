<!--
  Modal de confirmação genérico do backoffice.

  Use em qualquer form ou link que precise de confirmação, no lugar de confirm():

    <form method="post"
          data-confirmar="Excluir o usuário Maria?"
          data-confirmar-titulo="Excluir usuário"
          data-confirmar-ok="Excluir">

    <a href="..." data-confirmar="Deseja sair do sistema?">Sair</a>

  Atributos opcionais: data-confirmar-titulo, data-confirmar-ok, data-confirmar-tipo (danger|primary|success).
-->
<div class="modal fade" id="modalConfirmacao" tabindex="-1" aria-labelledby="modalConfirmacaoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacaoTitulo">Confirmação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="modalConfirmacaoMensagem"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="modalConfirmacaoOk">Confirmar</button>
            </div>
        </div>
    </div>
</div>
