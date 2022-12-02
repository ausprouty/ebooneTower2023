importScripts('js/analytics-helper.js');

importScripts('js/sw-offline-google-analytics.js');
goog.offlineGoogleAnalytics.initialize();
var CACHE_DYNAMIC_NAME = 'content-1';



const latest = {
    cache: 'video/v1'
  };
  
// from https://stackoverflow.com/questions/41916875/is-it-possible-to-cache-an-entire-html5-video-using-the-service-worker-api-for-o
 self.addEventListener('fetch', event => {
    // exclude requests that start with chrome-extension://
    if (event.request.url.startsWith('chrome-extension://')) return;
	console.log ('this is not a Chrome Extension');
	 // exclude requests that do not end with .mp4//
	if (!event.request.url.endsWith('.mp4')) return;
	console.log ('this is an mp4');
    event.respondWith(
      caches.open(latest.cache).then(cache => {
        return cache.match(event.request).then(response => {
          var fetchPromise = fetch(event.request).then(networkResponse => {
            cache.put(event.request, networkResponse.clone());
            return networkResponse;
          })
          return response || fetchPromise;
        })
    })
  );
});

importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.5.0/workbox-sw.js');
workbox.precaching.precacheAndRoute([
    '/',
	'/not-found.html',
	'/js/fetch.js',
	'/js/promise.js',
	'/js/analytics-helper.js',

]);

workbox.routing.registerRoute(
  new RegExp('.*\.js'),
  workbox.strategies.networkFirst(),
);

workbox.routing.registerRoute(
  new RegExp('.*\.css'),
  workbox.strategies.staleWhileRevalidate(),
);



workbox.routing.registerRoute(
  /.*\.(?:html|htm|shtml|png|jpg|jpeg|svg|gif)/g,
  workbox.strategies.networkFirst({
    cacheName: CACHE_DYNAMIC_NAME,
  }),
);