importScripts('js/analytics-helper.js')

importScripts('js/sw-offline-google-analytics.js')
goog.offlineGoogleAnalytics.initialize()
var CACHE_DYNAMIC_NAME = 'content-1'

importScripts(
  'https://storage.googleapis.com/workbox-cdn/releases/3.5.0/workbox-sw.js'
)
workbox.precaching.precacheAndRoute([
  '/',
  '/not-found.html',
  '/sites/default/js/fetch.js',
  '/sites/default/js/promise.js',

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
