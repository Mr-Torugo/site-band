// Aumentamos para a versão v3 para forçar o celular/PC a deletar o código antigo!
const CACHE_NAME = 'bando-map-v3';

const urlsToCache = [
    './',
    './login.html',
    './index.html',
    './album.html',
    './ranking.html',
    './ranking_adesivos.html',
    './feed.html' // Aproveitei para colocar nossa nova página do feed no cache também!
];

// Instala o Service Worker e salva os arquivos
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
    // Força a instalação imediata do novo Service Worker
    self.skipWaiting(); 
});

// Limpa os caches antigos (versões v1 e v2) quando essa nova versão entra em cena
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Assume o controle das abas abertas imediatamente
    self.clients.claim(); 
});

// ESTRATÉGIA: Network First (Rede Primeiro, fallback para Cache)
self.addEventListener('fetch', event => {
    
    // 1. A REGRA DE OURO PARA O ERRO DA EXTENSÃO:
    // Só permite salvar no cache se o link começar estritamente com http ou https
    if (!event.request.url.startsWith('http')) {
        return;
    }

    // 2. Ignora requisições de API (PHP) para elas nunca ficarem presas no cache
    if (event.request.url.includes('/api/') || event.request.url.includes('/auth/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Se baixou com sucesso e é um arquivo do nosso próprio site (basic)
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Se falhou (sem internet), busca a última versão salva no cache!
                return caches.match(event.request);
            })
    );
});