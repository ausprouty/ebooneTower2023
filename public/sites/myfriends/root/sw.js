//importScripts("js/analytics-helper.js");

//importScripts("js/sw-offline-google-analytics.js");

// https://developers.google.com/web/tools/workbox/modules

//goog.offlineGoogleAnalytics.initialize();
//https://developers.google.com/web/tools/workbox/guides/get-started
var CACHE_DYNAMIC_NAME = 'myfriends-run-time-v5'

const NOT_FOUND_PAGE = '/index.html'

importScripts(
  'https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js'
)

// from https://developers.google.com/web/tools/workbox/guides/advanced-recipes
// but this is not working right now

addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    alert('I am about to skipWaiting')
    skipWaiting()
  }
})

// from https://developers.google.com/web/tools/workbox/

workbox.setConfig({
  debug: true,
  skipwaiting: true,
})

workbox.core.setCacheNameDetails({
  prefix: 'myfriends',
  suffix: 'v5',
  precache: 'install-time',
  runtime: 'run-time',
  googleAnalytics: 'ga',
})
workbox.googleAnalytics.initialize()

workbox.precaching.cleanupOutdatedCaches()
workbox.precaching.precacheAndRoute([
  '/index.html',
  '/content/index.html',
  '/not-found.html',
  '/sites/default/javascript/fetch.js',
  '/sites/default/javascript/promise.js',
])

workbox.routing.registerRoute(
  new RegExp('.*.js'),
  new workbox.strategies.NetworkFirst({
    // Use a custom cache name.
    cacheName: 'js-cache',
  })
)

workbox.routing.registerRoute(
  new RegExp('.*.css'),
  new workbox.strategies.NetworkFirst({
    // Use a custom cache name.
    cacheName: 'css-cache',
  })
)

workbox.routing.registerRoute(
  /.*\.(?:html|htm|shtml|png|jpg|jpeg|svg|gif)/g,
  new workbox.strategies.NetworkFirst()
)
