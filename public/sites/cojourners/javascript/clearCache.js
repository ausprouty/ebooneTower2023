function clearCache(cacheName) {
  console.log('I want to clear ' + cacheName)
  window.caches.open(cacheName).then(function (cache) {
    return caches.delete(cacheName)
  })
  location.reload()
}

function clearCaches() {
  console.log('I want to clear ALL caches and some of Local Storage')
  clearLocalStorage()
  // All the Cache Storage API methods return Promises. If you're not familiar
  // with them, see http://www.html5rocks.com/en/tutorials/es6/promises/
  // Here, we're iterating over all the available caches, and for each cache,
  // deleting it.
  window.caches.keys().then(function (cacheNames) {
    cacheNames.forEach(function (cacheName) {
      window.caches
        .open(cacheName)
        .then(function (cache) {
          return cache.keys()
        })
        .then(function (requests) {
          requests.forEach(function (request) {
            console.log(cacheName)
            return caches.delete(cacheName)
          })
        })
    })
  })
  location.href = 'https://cojourners.sent67.com/content/U1/eng/index.html'
}

function clearLocalStorage() {
  localStorage.removeItem('lastpage')
  localStorage.removeItem('installedPWA')
  localStorage.removeItem('swWorking')
  localStorage.removeItem('lastSeenPrompt')
  localStorage.removeItem('mc2VideoPreferences')
  localStorage.removeItem('dynamic-cache')

  //localStorage.clear()
}

function listCaches() {
  console.log('I want to list caches')
  var list = ''
  var list2 = ''
  window.caches.keys().then(function (cacheNames) {
    cacheNames.forEach(function (cacheName) {
      window.caches
        .open(cacheName)
        .then(function (cache) {
          return cache.keys()
        })
        .then(function (requests) {
          requests.forEach(function (request) {
            console.log(cacheName)
            if (!list.includes(cacheName)) {
              list2 =
                list +
                '<p><a href = "javascript:clearCache(\'' +
                cacheName +
                '\');">Clear ' +
                cacheName +
                '</a></p>'
              list = list2
            }
          })
          document.getElementById('list').innerHTML = list
        })
    })
  })
}

function removeServiceWorker() {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function (registrations) {
      //returns installed service workers
      if (registrations.length) {
        for (let registration of registrations) {
          registration.unregister()
        }
      }
    })
  }
}
