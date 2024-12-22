<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Logins</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #111;
            border-radius: 10px;
        }
        .search-bar-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-bar {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: none;
        }
        .search-button {
            padding: 10px;
            background-color: #ff00ff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-item {
            background-color: #222;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .login-item a {
            color: green; /* Cor inicial da URL */
            text-decoration: none; /* Remove o sublinhado */
        }
        .login-item a.clicked {
            color: red; /* Cor da URL após ser clicada */
        }
        .login-item button {
            background-color: #ff00ff;
            border: none;
            padding: 5px 10px;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 5px;
        }
        .load-more {
            background-color: #444;
            border: none;
            padding: 10px;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            margin: 10px 0;
            width: 100%;
        }
        .download-btn {
            background-color: #28a745;
            border: none;
            padding: 10px;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gerenciador de Logins</h2>
        <div class="search-bar-container">
            <input type="text" id="search" class="search-bar" placeholder="Procurar por site, nome de usuário ou senha">
            <button class="search-button" onclick="searchLogins()">Pesquisar</button>
        </div>
        <div id="login-list"></div>
        <button id="load-more" class="load-more" onclick="loadMoreLogins()">Carregar mais</button>
        <button id="download-results" class="download-btn" style="display: none;" onclick="downloadResults()">Baixar Resultados</button>
    </div>

    <script>
        let loginData = [];
        let loadedCount = 0;
        const loadAmount = 100;

        async function fetchLogins() {
            // Faz uma requisição ao PHP para obter os dados do arquivo
            const response = await fetch('logins.php');
            const data = await response.json();
            loginData = data;
            loadMoreLogins();
        }

        function loadMoreLogins() {
            const loginList = document.getElementById('login-list');
            const end = Math.min(loadedCount + loadAmount, loginData.length);

            for (let i = loadedCount; i < end; i++) {
                const login = loginData[i];
                createLoginItem(loginList, login);
            }

            loadedCount = end;
            if (loadedCount >= loginData.length) {
                document.getElementById('load-more').style.display = 'none';
            }
        }

        function createLoginItem(container, login) {
            const loginItem = document.createElement('div');
            loginItem.classList.add('login-item');
            loginItem.innerHTML = `
                <p>
                    <strong>URL:</strong> 
                    <a href="${login.url}" target="_blank" 
                       class="login-url" 
                       onclick="markAsClicked(this);">
                       ${login.url}
                    </a>
                </p>
                <p><strong>Usuário:</strong> ${login.user}</p>
                <p><strong>Senha:</strong> ${login.password}</p>
                <button onclick="copyToClipboard('${login.url}')">Copiar URL</button>
                <button onclick="copyToClipboard('${login.user}')">Copiar Usuário</button>
                <button onclick="copyToClipboard('${login.password}')">Copiar Senha</button>
            `;
            container.appendChild(loginItem);
        }

        function markAsClicked(link) {
            link.classList.add('clicked'); // Adiciona a classe que muda a cor
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copiado: ' + text);
            });
        }

        function searchLogins() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const loginList = document.getElementById('login-list');
            loginList.innerHTML = '';

            const filteredLogins = loginData.filter(login => 
                login.url.toLowerCase().includes(searchInput) ||
                login.user.toLowerCase().includes(searchInput) ||
                login.password.toLowerCase().includes(searchInput)
            );

            filteredLogins.forEach(login => createLoginItem(loginList, login));

            // Exibir o botão de download apenas se houver resultados
            const downloadButton = document.getElementById('download-results');
            downloadButton.style.display = filteredLogins.length > 0 ? 'block' : 'none';

            // Ocultar o botão "Carregar mais" durante a busca
            document.getElementById('load-more').style.display = searchInput ? 'none' : 'block';
        }

        function downloadResults() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const filteredLogins = loginData.filter(login => 
                login.url.toLowerCase().includes(searchInput) ||
                login.user.toLowerCase().includes(searchInput) ||
                login.password.toLowerCase().includes(searchInput)
            );

            let txtContent = '';
            filteredLogins.forEach(login => {
                txtContent += `${login.url}:${login.user}:${login.password}\n`;
            });

            const blob = new Blob([txtContent], { type: 'text/plain' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'resultados.txt';
            link.click();
        }

        document.addEventListener('DOMContentLoaded', fetchLogins);
    </script>
</body>
</html>