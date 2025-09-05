// Garante que o script só será executado após o carregamento completo do HTML
document.addEventListener('DOMContentLoaded', function() {

    const form = document.getElementById('equipForm');
    const nomeInput = document.getElementById('nome');
    const setorInput = document.getElementById('setor');
    const descricaoInput = document.getElementById('descricao');
    const fotoInput = document.getElementById('foto');
    const previewImg = document.getElementById('preview');
    const qrCodeContainer = document.getElementById('qrCode');
    const gerarQrBtn = document.getElementById('gerarQrBtn');
    const submitBtn = form.querySelector('button[type="submit"]');
    const verListaBtn = document.getElementById('verListaBtn');

    // Função para gerar o QR Code
    function gerarQRCode() {
        // Limpa o QR Code anterior, se houver
        qrCodeContainer.innerHTML = '';

        const dados = {
            nome: nomeInput.value,
            setor: setorInput.value,
            descricao: descricaoInput.value,
        };

        // Validação para garantir que os campos não estão vazios
        if (!dados.nome || !dados.setor) {
            alert('Por favor, preencha os campos "Nome do Equipamento" e "Setor" para gerar o QR Code.');
            return;
        }

        const dadosString = JSON.stringify(dados);

        new QRCode(qrCodeContainer, {
            text: dadosString,
            width: 128,
            height: 128,
        });
    }

    // Adiciona o event listener ao botão "Gerar QR Code"
    gerarQrBtn.addEventListener('click', gerarQRCode);

    // Adiciona o event listener ao botão "Ver Lista de Equipamentos"
    verListaBtn.addEventListener('click', function() {
        window.location.href = 'lista_equipamentos.php';
    });

    // Função para mostrar a pré-visualização da imagem
    fotoInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Função para enviar o formulário com AJAX (Fetch API)
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Impede o envio padrão do formulário

        const formData = new FormData(form);
        const originalButtonText = submitBtn.textContent;
        submitBtn.textContent = 'Cadastrando...';
        submitBtn.disabled = true;

        fetch('cadastro.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                // Se a resposta do servidor não for OK (ex: erro 500), lança um erro
                throw new Error('Erro no servidor: ' + response.statusText);
            }
            return response.text(); // Pega a resposta do PHP como texto
        })
        .then(data => {
            alert(data); // Exibe a mensagem de sucesso do PHP ("Equipamento cadastrado com sucesso!")
            form.reset(); // Limpa todos os campos do formulário
            previewImg.style.display = 'none'; // Esconde a pré-visualização da imagem
            qrCodeContainer.innerHTML = ''; // Limpa o QR Code
        })
        .catch(error => {
            console.error('Erro ao enviar o formulário:', error);
            alert('Ocorreu um erro ao cadastrar o equipamento. Tente novamente.');
        })
        .finally(() => {
            // Este bloco é executado sempre, seja em caso de sucesso ou erro
            submitBtn.textContent = originalButtonText; // Restaura o texto original do botão
            submitBtn.disabled = false; // Reabilita o botão
        });
    });

});