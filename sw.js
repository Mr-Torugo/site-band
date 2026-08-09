const CACHE_NAME = 'bando-map-v1';

// Arquivos básicos que ele vai salvar no cache do celular para carregar rápido
const urlsToCache = [
    './',
    './login.html',
    './index.html',
    './album.html',
    './ranking.html',
    './ranking_adesivos.html'
];

// Instala o Service Worker e salva os arquivos no cache
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

// Intercepta as requisições: tenta pegar do cache, se não tiver, busca da internet
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response; // Retorna do cache
                }
                return fetch(event.request); // Busca da rede
            })
    );
});