var CACHE_DYNAMIC_NAME = 'workbox-runtime'

importScripts(
  'https://storage.googleapis.com/workbox-cdn/releases/3.5.0/workbox-sw.js'
)
workbox.precaching.precacheAndRoute([
  '/',
  '/not-found.html',
  '/sites/default/javascript/fetch.js',
  '/sites/default/javascript/promise.js',
])

workbox.routing.registerRoute(
  new RegExp('.*.js'),
  workbox.strategies.networkFirst()
)

workbox.routing.registerRoute(
  new RegExp('.*.css'),
  workbox.strategies.staleWhileRevalidate()
)

workbox.routing.registerRoute(
  /.*\.(?:html|htm|shtml|png|jpg|jpeg|svg|gif)/g,
  workbox.strategies.networkFirst({
    cacheName: CACHE_DYNAMIC_NAME,
  })
)
