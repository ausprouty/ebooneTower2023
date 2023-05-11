let SHOW_PROMPT_EVERY_X_DAYS = 30
let today = Date.now()
// Initialize deferredPrompt for use later to show browser install prompt.
var deferredPrompt

// from https://web.dev/customize-install/
window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  //console.log(`'beforeinstallprompt' event was started.`)
  e.preventDefault()
  // Stash the event so it can be triggered later.
  deferredPrompt = e
  // Update UI notify the user they can install the PWA
  homescreenPromptShow()
  // Optionally, send analytics event that PWA install promo was shown.
  //console.log(`'beforeinstallprompt' event was fired.`)
})
addToHomeScreenButton.addEventListener('click', async () => {
  //deferredPrompt = e
  // Hide the app provided install promotion
  localStorage.setItem('installedPWA', today)
  homescreenPromptHide()
  // Show the install prompt
  deferredPrompt.prompt()
  // Wait for the user to respond to the prompt
  const { outcome } = await deferredPrompt.userChoice
  // Optionally, send analytics event with outcome of user choice
  //console.log(`User response to the install prompt: ${outcome}`)

  // We've used the prompt, and can't use it again, throw it away
  deferredPrompt = null
})

window.addEventListener('appinstalled', (event) => {
  //console.log('appinstalled', event)
  // Clear the deferredPrompt so it can be garbage collected
  window.deferredPrompt = null
})

document.addEventListener('DOMContentLoaded', offlineReadyCheck)
document.addEventListener('DOMContentLoaded', homescreenCheck)

function offlineReadyCheck() {
  //console.log('in offlneReadyCheck')
  // set ios prompt if needed
  //https://www.netguru.co/codestories/few-tips-that-will-make-your-pwa-on-ios-feel-like-native
  if (navigator.onLine) {
    var link = ''
    //console.log('I am ONline')
    var swWorking = localStorage.getItem('swWorking')
    if ('serviceWorker' in navigator && swWorking == 'TRUE') {
      var offlineReady = localStorage.getItem('offline')
      //console.log(offlineReady)
      if (!offlineReady) {
        //console.log('app can not be offline')
        link = document.getElementById('offline-request')
        link.style.visibility = 'visible'
      } else {
        //console.log('app CAN be offline')
        link = document.getElementById('offline-ready')
        link.style.visibility = 'visible'
      }
    } else {
      //console.log('I do NOT have a service worker')
      link = document.getElementById('offline-request')
      link.style.display = 'none'
      //var link = document.getElementById('offline-already');
      //link.style.display = 'none';
    }
  } else {
    //console.log('I am offline')
    offlineItemsHide()
  }
}
function homescreenCheck() {
  // https://web.dev/customize-install/#detect-cojourners-type
  if (getPWADisplayMode() == 'standalone') {
    //console.log('pwa already installed')
    return
  }
  var lastPrompt = localStorage.lastSeenPrompt
  if (typeof lastPrompt == 'undefined') {
    homescreenPromptShow()
  }
  var installedPWA = localStorage.installedPWA
  if (typeof installedPWA == 'undefined') {
    let days = Math.round((today - lastPrompt) / (1000 * 60 * 60 * 24))
    if (days > SHOW_PROMPT_EVERY_X_DAYS) {
      homescreenPromptShow()
    }
  }
}

function getPWADisplayMode() {
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches
  if (document.referrer.startsWith('android-app://')) {
    return 'twa'
  } else if (navigator.standalone || isStandalone) {
    return 'standalone'
  }
  return 'browser'
}
function homescreenPromptHide() {
  var dlg = document.getElementById('addToHomeScreen')
  dlg.classList.remove('xhidden')
  dlg.classList.add('hidden')
}
function homescreenPromptShow() {
  localStorage.setItem('lastSeenPrompt', today)
  var dlg = document.getElementById('addToHomeScreen')
  dlg.classList.remove('hidden')
  dlg.classList.add('xhidden')
  //}
}
function closeScreen() {
  var screen = document.getElementById('addToHomeScreen')
  screen.remove()
  localStorage.setItem('lastSeenPrompt', today)
  return false
}
// get value of variable in array
// is id in key?
function inLocalStorage(key, id) {
  var deferred = $.Deferred()
  var result = ''
  //console.log('looking offline for local storage')
  var key_value = localStorage.getItem(key)
  if (typeof key_value != 'undefined' && key_value) {
    key_value = JSON.parse(key_value)
    //console.log(key_value)
    key_value.forEach(function (array_value) {
      //console.log(array_value + '  array value')
      //console.log(id + '  id')
      if (array_value == id) {
        //console.log('stored locally')
        result = id
      }
    })
    //console.log(result)
  } else {
    result = ''
    //console.log('not stored locally')
  }
  deferred.resolve(result)
  return deferred.promise()
}

function offlineItemsHide() {
  // get rid of all readmore comments
  var readmore = document.getElementsByClassName('readmore')
  if (readmore.length > 0) {
    //console.log('I found readmore')
    for (var i = 0; i < readmore.length; i++) {
      readmore[i].style.display = 'none'
    }
  }
  readmore = document.getElementsByClassName('bible_readmore')
  if (readmore.length > 0) {
    //console.log('I found bible_readmore')
    for (var i = 0; i < readmore.length; i++) {
      readmore[i].style.display = 'none'
    }
  }
  // hide external-link
  var links = document.getElementsByClassName('external-link')
  if (links.length > 0) {
    //console.log('I found external-link')
    for (var i = 0; i < links.length; i++) {
      links[i].style.className = 'unlink'
    }
  }
  // hide external-movie
  links = document.getElementsByClassName('external-movie')
  if (links.length > 0) {
    //console.log('I found external-link')
    for (var i = 0; i < links.length; i++) {
      links[i].style.display = 'none'
    }
  }
}

// this stores series for offline use
// https://developers.google.com/web/ilt/pwa/caching-files-with-service-worker
var el = document.getElementById('offline-request')
if (el) {
  document
    .getElementById('offline-request')
    .addEventListener('click', function (event) {
      event.preventDefault()
      //console.log('button pressed')
      el.style.background = '#FF9700'
      var id = this.dataset.json
      var ajaxPromise = fetch(id)
        .then(function (response) {
          //get-series-urls returns a JSON-encoded array of
          // resource URLs that a given series depends on
          return response.json()
        })
        .then(function (jsonFile) {
          jsonFile.forEach(function (element) {
            //console.log(element.url)
            caches.open(CACHE_DYNAMIC_NAME).then(function (cache) {
              cache.add(element.url)
            })
          })
        })
        .then(function () {
          console.log('store that content is available for offline use')
          let today = Date.now()
          localStorage.setItem('offline', today)
          var ready = document.getElementById('offline-ready').innerHTML
          el.innerHTML = ready
          el.style.background = '#717073'
        })
        .catch(function (err) {
          console.log(err)
        })
    })
}
