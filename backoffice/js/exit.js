document.addEventListener("DOMContentLoaded", function() {
    var logoutLink = document.getElementById("logout");
    var confirmLogoutButton = document.getElementById("confirmLogout");
    var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    logoutLink.addEventListener("click", function(event) {
        event.preventDefault();
        confirmModal.show();
    });

    confirmLogoutButton.addEventListener("click", function() {
        // Redireciona para a página de logout PHP após a confirmação
        // Redirects to PHP logout page after confirmation
        window.location.href = '../functions/logout.php';
    });

    // Fecha o modal se o usuário clicar no botão de fechar
    // Closes the modal if the user clicks the close button
    document.querySelectorAll('[data-dismiss="modal"]').forEach(function(element) {
        element.addEventListener("click", function() {
            confirmModal.hide();
        });
    });
});