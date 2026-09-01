var CACHE_DYNAMIC_NAME = 'content-1'
var SHOW_PROMPT_EVERY_X_DAYS = 30

// Initialize deferredPrompt for use later to show browser install prompt.
var deferredPrompt = null

// Chrome / Android install prompt
window.addEventListener('beforeinstallprompt', function (e) {
  console.log("'beforeinstallprompt' event was fired.")

  // Prevent the browser mini-infobar from appearing
  e.preventDefault()

  // Save the event so we can trigger it when the user clicks our button
  deferredPrompt = e

  // Android / Chrome gets its prompt from this event
  if (!isIOS() && needsToShowHomescreenPrompt()) {
    homescreenPromptShow()
  }
})

document.addEventListener('DOMContentLoaded', function () {
  offlineRequestCheck()
  homescreenCheck()
  setupAddToHomeScreenButton()
})

function setupAddToHomeScreenButton() {
  var button = document.getElementById('addToHomeScreenButton')

  if (!button) {
    return
  }

  button.addEventListener('click', async function () {
    // This button is only intended to trigger the browser install
    // prompt on browsers that support beforeinstallprompt.
    if (!deferredPrompt) {
      console.log('No deferred PWA prompt available.')
      return
    }

    homescreenPromptHide()

    // Show the browser's actual install prompt
    deferredPrompt.prompt()

    // Wait for the user's response
    const choiceResult = await deferredPrompt.userChoice

    console.log(
      'User response to the install prompt: ' + choiceResult.outcome
    )

    // The saved event can only be used once
    deferredPrompt = null
  })
}

window.addEventListener('appinstalled', function (event) {
  console.log('👍 appinstalled', event)

  // Record that installation really occurred
  localStorage.setItem('installedPWA', String(Date.now()))

  deferredPrompt = null

  homescreenPromptHide()
})

function offlineRequestCheck() {
  var series = document.getElementById('offline-request')

  if (series !== null) {
    offlineSeriesCheck(series.dataset.json)
  }
}

function offlineSeriesCheck(series) {
  console.log(series + ' series is being checked')

  if (navigator.onLine) {
    console.log('I am ONline')

    var swWorking = localStorage.getItem('swWorking')

    if ('serviceWorker' in navigator && swWorking == 'TRUE') {
      console.log('I have a service worker')

      inLocalStorage('offline', series).then(function (result) {
        var link = ''

        if (result == '') {
          console.log(series + ' not available offline')

          link = document.getElementById('offline-request')

          if (link) {
            link.style.visibility = 'visible'
          }
        } else {
          console.log(series + ' available offline')

          link = document.getElementById('offline-ready')

          if (link) {
            link.style.visibility = 'visible'
          }
        }
      })
    } else {
      console.log('I do NOT have a service worker')

      var link = document.getElementById('offline-request')

      if (link) {
        link.style.display = 'none'
      }
    }
  } else {
    console.log('I am offline')
    offlineItemsHide()
  }
}

function homescreenCheck() {
  if (getPWADisplayMode() == 'standalone') {
    console.log('pwa already installed')
    return
  }

  // iOS does not provide beforeinstallprompt.
  // We therefore show our own iOS installation instructions.
  if (isIOS() && needsToShowHomescreenPrompt()) {
    homescreenPromptShow()
  }

  // Android / Chrome is handled by beforeinstallprompt.
}

function isIOS() {
  return (
    /iPad|iPhone|iPod/.test(navigator.userAgent) ||
    (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)
  )
}

function needsToShowHomescreenPrompt() {
  if (getPWADisplayMode() == 'standalone') {
    return false
  }

  if (localStorage.getItem('installedPWA')) {
    return false
  }

  var lastPrompt = Number(localStorage.getItem('lastSeenPrompt'))

  if (!lastPrompt) {
    return true
  }

  var days = Math.floor(
    (Date.now() - lastPrompt) / (1000 * 60 * 60 * 24)
  )

  console.log(days + ' days since PWA prompt')

  return days >= SHOW_PROMPT_EVERY_X_DAYS
}

function getPWADisplayMode() {
  var isStandalone = window.matchMedia('(display-mode: standalone)').matches

  if (document.referrer.startsWith('android-app://')) {
    return 'twa'
  }

  if (navigator.standalone || isStandalone) {
    return 'standalone'
  }

  return 'browser'
}

function homescreenPromptShow() {
  var dlg = document.getElementById('addToHomeScreen')
  var iosInstructions = document.getElementById('iosInstallInstructions')
  var androidButton = document.getElementById('addToHomeScreenButton')

  if (!dlg) {
    return
  }

  if (isIOS()) {
    console.log('Showing iOS PWA instructions')

    if (iosInstructions) {
      iosInstructions.classList.remove('hidden')
    }

    if (androidButton) {
      androidButton.classList.add('hidden')
    }
  } else {
    console.log('Showing Android PWA install prompt')

    if (iosInstructions) {
      iosInstructions.classList.add('hidden')
    }

    if (androidButton) {
      androidButton.classList.remove('hidden')
    }
  }

  // Record when the user actually sees our prompt
  localStorage.setItem('lastSeenPrompt', String(Date.now()))

  dlg.classList.remove('hidden')
  dlg.classList.add('xhidden')
}

function homescreenPromptHide() {
  var dlg = document.getElementById('addToHomeScreen')

  if (!dlg) {
    return
  }

  dlg.classList.remove('xhidden')
  dlg.classList.add('hidden')
}

function closeScreen() {
  // User deliberately dismissed the prompt.
  // Do not show it again for 30 days.
  localStorage.setItem('lastSeenPrompt', String(Date.now()))

  homescreenPromptHide()

  return false
}

// get value of variable in array
// is id in key?
function inLocalStorage(key, id) {
  var deferred = $.Deferred()
  var result = ''

  console.log('looking offline for local storage')

  var key_value = localStorage.getItem(key)

  if (typeof key_value != 'undefined' && key_value) {
    key_value = JSON.parse(key_value)

    console.log(key_value)

    key_value.forEach(function (array_value) {
      console.log(array_value + '  array value')
      console.log(id + '  id')

      if (array_value == id) {
        console.log('stored locally')
        result = id
      }
    })

    console.log(result)
  } else {
    result = ''
    console.log('not stored locally')
  }

  deferred.resolve(result)

  return deferred.promise()
}

function offlineItemsHide() {
  // get rid of all readmore comments
  var readmore = document.getElementsByClassName('readmore')

  if (readmore.length > 0) {
    console.log('I found readmore')

    for (var i = 0; i < readmore.length; i++) {
      readmore[i].style.display = 'none'
    }
  }

  readmore = document.getElementsByClassName('bible_readmore')

  if (readmore.length > 0) {
    console.log('I found bible_readmore')

    for (var j = 0; j < readmore.length; j++) {
      readmore[j].style.display = 'none'
    }
  }

  // hide external-link
  var links = document.getElementsByClassName('external-link')

  if (links.length > 0) {
    console.log('I found external-link')

    for (var k = 0; k < links.length; k++) {
      links[k].className = 'unlink'
    }
  }

  // hide external-movie
  links = document.getElementsByClassName('external-movie')

  if (links.length > 0) {
    console.log('I found external-movie')

    for (var l = 0; l < links.length; l++) {
      links[l].style.display = 'none'
    }
  }
}

// this stores series for offline use
// https://developers.google.com/web/ilt/pwa/caching-files-with-service-worker
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('offline-request')

  if (!el) {
    return
  }

  el.addEventListener('click', function (event) {
    event.preventDefault()

    console.log('button pressed')

    var id = this.dataset.json

    console.log(id)

    fetch(id)
      .then(function (response) {
        // get-series-urls returns a JSON-encoded array of
        // resource URLs that a given series depends on
        return response.json()
      })
      .then(function (jsonFile) {
        jsonFile.forEach(function (element) {
          console.log(element.url)

          caches.open(CACHE_DYNAMIC_NAME).then(function (cache) {
            cache.add(element.url)
          })
        })
      })
      .then(function () {
        // store that series is available for offline use
        console.log(id + ' Series ready for offline use')

        var offline = []
        var already = 'N'

        if (
          typeof localStorage.offline != 'undefined' &&
          localStorage.offline
        ) {
          offline = JSON.parse(localStorage.offline)
        }

        offline.forEach(function (array_value) {
          if (array_value == id) {
            console.log('stored locally')
            already = 'Y'
          }
        })

        console.log(already + ' is already')

        if (already != 'Y') {
          offline.push(id)

          console.log(offline)
        }

        localStorage.setItem('offline', JSON.stringify(offline))

        var ready = document.getElementById('offline-ready')
        var request = document.getElementById('offline-request')

        if (ready && request) {
          request.innerHTML = ready.innerHTML
          request.style.background = '#00693E'
        }
      })
      .catch(function (err) {
        console.log(err)
      })
  })
})