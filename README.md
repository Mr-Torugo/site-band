<h1 align="center">🗺️ Bando Map </h1>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/PWA-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA">
</p>

## 📌 Sobre o Projeto
O **Bando Map** é um Progressive Web App (PWA) interativo de caça ao tesouro focado em exploração urbana e gamificação. Os usuários podem espalhar adesivos pelo mapa global e registrar descobertas ao encontrar os adesivos deixados por outros caçadores. 

O sistema utiliza geolocalização e cálculos matemáticos (Fórmula de Haversine) para gerar raridades dinâmicas e distribuir pontos de experiência (XP), criando uma competição real entre os usuários.

## 🚀 Funcionalidades Principais

*   **Geolocalização Interativa (Leaflet):** Mapa dinâmico com suporte a GPS em tempo real, seleção manual via clique ou busca por endereço (API Nominatim).
*   **Agrupamento Inteligente (Clustering):** Otimização de performance visual utilizando `Leaflet.markercluster` para agrupar marcadores em regiões com alta densidade de adesivos.
*   **Gacha e Geofencing (Raridades Dinâmicas):** O backend calcula a distância do local do adesivo até um ponto central. Adesivos colados a mais de 100km tornam-se "Raros", e a distâncias extremas tornam-se "Lendários", rendendo mais XP aos descobridores.
*   **Sistema de Gamificação e Conquistas:** Ranking automático baseado em XP, listando os jogadores com base no peso de suas descobertas e concedendo emblemas automáticos (Badges) por metas alcançadas.
*   **Mural de Comentários e Selfies:** Cada ponto no mapa atua como um micro-fórum geolocalizado, onde os usuários podem enviar selfies e deixar mensagens ao registrar uma descoberta.
*   **Álbum de Colecionador:** Galeria pessoal segmentada por "Adesivos Encontrados" e "Adesivos Colados", refletindo o histórico e o impacto do jogador no mapa.
*   **Progressive Web App (PWA):** Instalação nativa em dispositivos móveis Android/iOS com suporte a cache local via Service Workers, oferecendo navegação fluida em tela cheia.

## 🛠️ Tecnologias Utilizadas

**Front-end:**
*   HTML5 / CSS3 / JavaScript (Vanilla)
*   [Bootstrap 5](https://getbootstrap.com/) (UI, Grid e Modais)
*   [Leaflet.js](https://leafletjs.com/) (Renderização de Mapas)
*   Service Workers & Web App Manifest (PWA)

**Back-end & Banco de Dados:**
*   PHP 8.x (API Restful para comunicação com o front-end)
*   SQLite3 (Banco de dados relacional leve e integrado)
*   Upload e manipulação de imagens nativo

## 📁 Estrutura de Pastas

```text
site-bando/
├── api/
│   ├── banco.sqlite         # Banco de dados gerado automaticamente
│   ├── descobrir.php        # Lógica de encontrar um adesivo
│   ├── excluir.php          # Lógica de remoção com trava de segurança
│   ├── listar.php           # Busca de adesivos para o mapa
│   ├── meu_album.php        # Consulta de histórico do usuário
│   ├── mural.php            # Feed de comentários por adesivo
│   ├── ranking_adesivos.php # Classificação de "Hot Spots"
│   ├── ranking.php          # Classificação de jogadores e XP
│   └── salvar.php           # Upload de imagem, cálculo Haversine e Insert
├── auth/
│   ├── login.php            # Validação de credenciais
│   └── registrar.php        # Criação de contas
├── uploads/                 # Diretório de armazenamento local das imagens
├── index.html               # Mapa principal (Core do App)
├── login.html               # Tela de Autenticação
├── album.html               # Coleção do usuário (Tabs dinâmicas)
├── ranking.html             # Tabela de Líderes
├── ranking_adesivos.html    # Locais mais populares
├── manifest.json            # Configurações do App (PWA)
└── sw.js                    # Service Worker (Cache management)
