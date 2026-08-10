// Mudamos a versão para forçar os celulares a atualizarem!
const CACHE_NAME = 'bando-map-v2';

const urlsToCache = [
    './',
    './login.html',
    './index.html',
    './album.html',
    './ranking.html',
    './ranking_adesivos.html'
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

// Limpa os caches antigos (versão v1) quando essa nova versão entra em cena
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

// NOVA ESTRATÉGIA: Network First (Rede Primeiro, fallback para Cache)
self.addEventListener('fetch', event => {
    // Ignora requisições de API (PHP) para elas nunca ficarem presas no cache
    if (event.request.url.includes('/api/') || event.request.url.includes('/auth/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Se baixou com sucesso da internet, salva uma cópia atualizada no cache
                if (response && response.status === 200) {
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