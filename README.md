# 🗺️ Mapa dos Bandesivos (Bando Map)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=leaflet&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-blueviolet?style=for-the-badge)

O **Mapa dos Bandesivos** é uma aplicação web gamificada (PWA) desenvolvida para uma comunidade de caçadores de adesivos (Sticker Art). O sistema permite que os usuários registrem novos adesivos colados pelas ruas, encontrem adesivos de outros membros, ganhem experiência (XP), desbloqueiem conquistas e subam no ranking geral do "Bando".

## 🚀 Funcionalidades Principais

- **📍 Mapa Interativo:** Visualização de adesivos geolocalizados usando Leaflet.js com sistema de clusterização inteligente. Filtros por categoria, raridade e status de descoberta.
- **🎮 Gamificação e Patentes:** Sistema de XP dinâmico onde os usuários sobem de nível (de _Novato_ até _Entidade Suprema_) conforme interagem com o mapa.
- **🏆 Motor de Conquistas e Missões:** Avaliação automática do progresso do usuário para destravar medalhas e missões semanais.
- **📡 Radar (Feed Social):** Um feed em tempo real com as atividades da comunidade (quem colou, quem achou, conquistas alcançadas). Permite curtidas, comentários e _Deep Linking_ (clique no adesivo do feed e o mapa "viaja" até ele).
- **📸 Álbum de Coleção:** Uma galeria pessoal onde o usuário visualiza os adesivos que colou e os que conquistou (com fotos e selfies).
- **🏅 Hall da Fama (Ranking):** Listagem dos melhores caçadores da comunidade e dos adesivos mais visitados do mapa.
- **👑 Painel Administrativo:** Criação de adesivos de "Evento/Tesouro", gerenciamento de usuários e moderação de conteúdo.
- **📱 PWA Ready:** Pode ser instalado direto na tela inicial do celular como um aplicativo nativo.

## 🛠️ Tecnologias Utilizadas

- **Frontend:** HTML5, CSS3, Vanilla JavaScript, Bootstrap 5, Bootstrap Icons.
- **Mapas:** Leaflet.js & Leaflet.markercluster.
- **Backend:** PHP (API RESTful e processamento de lógica).
- **Banco de Dados:** SQLite (leve, sem necessidade de configuração complexa de servidor).

## ⚙️ Como rodar o projeto localmente

### Pré-requisitos

Você precisará de um servidor web com suporte a PHP instalado na sua máquina (recomendamos o [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou [Laragon](https://laragon.org/)).

### Passo a passo

1. **Clone o repositório:**

   ```bash
   git clone [https://github.com/Mr-Torugo/site-band.git](https://github.com/Mr-Torugo/site-band)
   ```

2. **Mova para o servidor local:**

Coloque a pasta do projeto dentro do diretório do seu servidor local (ex: htdocs no XAMPP ou www no WAMP/Laragon).

Crie o Banco de Dados Automaticamente:
O projeto conta com um script de setup que gera o banco SQLite sozinho. Abra o navegador e acesse:

http://localhost/site-band/api/setup.php

Se a tela exibir "Setup Concluído com Sucesso", o banco de dados foi criado dentro da pasta database/.

3. **Acesse a Aplicação:**

Agora basta acessar a raiz do projeto no seu navegador:

http://localhost/site-band/

4. **(Opcional) Torne-se Administrador:**
   Crie uma conta na tela de registro. Depois, descubra o seu ID (geralmente 1 se for o primeiro usuário) e acesse:

http://localhost/site-band/api/setup_admin.php?id=1

📂 **Estrutura de Pastas (Ignoradas no Git)**

⚠️ Atenção: As seguintes pastas e arquivos não estão versionados por motivos de segurança e armazenamento (configurados no .gitignore):

- `database/banco.sqlite`: O banco de dados de produção.

- `uploads/`: O diretório onde as fotos e selfies dos usuários são salvas.

### 👨‍💻 Autor

Desenvolvido por Vitor Hugo (e o Bando!).

[Linkedin](https://www.linkedin.com/in/vitor-hugo-05b2b91a7/)

Que a caçada comece! 🎯
