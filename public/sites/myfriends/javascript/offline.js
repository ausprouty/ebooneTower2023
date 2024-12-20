var CACHE_DYNAMIC_NAME = 'content-1'
document.addEventListener('DOMContentLoaded', offlineRequestCheck)
document.addEventListener('DOMContentLoaded', iOShomescreenCheck)

let deferredPrompt = null
const androidButton = document.getElementById('addToHomeScreenAndroidButton')
androidButton.addEventListener('click', handleInstallButtonClick)
// Event listener for the beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault()
  // Stash the event so it can be triggered later.
  deferredPrompt = e
  // Update UI to notify the user they can install the PWA
  homescreenCheck()
  // Optionally, send analytics event that PWA install promo was shown.
  console.log(`'beforeinstallprompt' event was fired.`)
})
// Function to handle the install button click
async function handleInstallButtonClick() {
  if (deferredPrompt) {
    console.log('deferredPrompt found')
    // Hide the app provided install promotion
    try {
      // Show the install prompt
      await deferredPrompt.prompt()
      // Wait for the user to respond to the prompt
      const { outcome } = await deferredPrompt.userChoice
      // Optionally, send analytics event with the outcome of user choice
      console.log(`User response to the install prompt: ${outcome}`)
      // We've used the prompt, and can't use it again, throw it away
      deferredPrompt = null
    } catch (error) {
      // Handle any errors that may occur when trying to prompt the user
      console.error('Error when prompting for installation:', error)
    }
  } else {
    console.log('deferredPrompt NOT found')
  }
  homescreenPromptHide('addToHomeScreenAndroid')
}
function offlineRequestCheck() {
  const series = document.getElementById('offline-request');
  if (series) {
    console.log('checking series');
    offlineSeriesCheck(series.dataset.json);
  }
  else{
    console.log('no series to check');
  }
};
}
function offlineSeriesCheck(series) {
  console.log(series + ' series is being checked')
  // set ios prompt if needed
  //https://www.netguru.co/codestories/few-tips-that-will-make-your-pwa-on-ios-feel-like-native

  if (navigator.onLine) {
    console.log('I am online')
    var swWorking = localStorage.getItem('swWorking')
    if ('serviceWorker' in navigator && swWorking == 'TRUE') {
      console.log('I have a service worker')
      inLocalStorage('offline', series).then(function (result) {
        console.log(result + ' is value')
        var link = ''
        if (result == '') {
          console.log(series + ' not offline')
          link = document.getElementById('offline-request')
          link.style.visibility = 'visible'
        } else {
          link = document.getElementById('offline-ready')
          link.style.visibility = 'visible'
        }
      })
    } else {
      console.log('I do NOT have a service worker')
      var link = document.getElementById('offline-request')
      link.style.display = 'none'
      //var link = document.getElementById('offline-already');
      //link.style.display = 'none';
    }
  } else {
    console.log('I am offline')
    offlineItemsHide()
  }
}
function datediff(first, second) {
  //  Take the difference between the dates and divide by milliseconds per day.
  //  Round to nearest whole number to deal with DST.
  return Math.round((second - first) / (1000 * 60 * 60 * 24))
}

function iOShomescreenCheck() {
  var operatingSystem = isAndroidOriOS()
  if (operatingSystem == 'iOS') {
    homescreenCheck()
  }
}
function homescreenCheck() {
  // android is called by  beforeinstallprompt
  var lastSeenPrompt = localStorage.lastSeenPrompt
  if (typeof lastSeenPrompt == 'undefined') {
    var operatingSystem = isAndroidOriOS()
    if (operatingSystem != 'iOS') {
      homescreenPromptShow()
    }
    if (!isPWAInstalledOniOS()) {
      homescreenPromptShow()
    }
  }
}
function homescreenPromptHide(screenName) {
  var dlg = document.getElementById(screenName)
  dlg.classList.remove('xhidden')
  dlg.classList.add('hidden')
}

function isPWAInstalledOniOS() {
  // Check if the app is running in standalone mode on iOS.
  return (
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone
  )
}
function homescreenPromptShow() {
  let today = Date.now()
  console.log('I am on line 106')
  //var lastPrompt = localStorage.lastSeenPrompt
  //let days = Math.round((today - lastPrompt) / (1000 * 60 * 60 * 24))
  //if (isNaN(days) || days > SHOW_PROMPT_EVERY_X_DAYS){
  localStorage.setItem('lastSeenPrompt', today)
  var osNotice = ''
  var operatingSystem = isAndroidOriOS()
  if (operatingSystem == 'iOS') {
    osNotice = document.getElementById('addToHomeScreenIos')
  } else {
    osNotice = document.getElementById('addToHomeScreenAndroid')
  }
  osNotice.classList.remove('hidden')
  osNotice.classList.add('xhidden')
  //}
}
function isAndroidOriOS() {
  const userAgent = navigator.userAgent.toLowerCase()
  if (/android/.test(userAgent)) {
    return 'Android'
  } else if (/iphone|ipad|ipod|macintosh/.test(userAgent)) {
    return 'iOS'
  } else {
    return 'Unknown'
  }
}
function closeScreen(screenName) {
  var screen = document.getElementById(screenName)
  screen.remove()
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
    for (var i = 0; i < readmore.length; i++) {
      readmore[i].style.display = 'none'
    }
  }
  // hide external-link
  var links = document.getElementsByClassName('external-link')
  if (links.length > 0) {
    console.log('I found external-link')
    for (var i = 0; i < links.length; i++) {
      links[i].style.className = 'unlink'
    }
  }
  // hide external-movie
  links = document.getElementsByClassName('external-movie')
  if (links.length > 0) {
    console.log('I found external-link')
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
      console.log('button pressed')
      var id = this.dataset.json
      var ajaxPromise = fetch(id)
        .then(function (response) {
          //get-series-urls returns a JSON-encoded array of
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
          var already
          if (
            typeof localStorage.offline != 'undefined' &&
            localStorage.offline
          ) {
            offline = JSON.parse(localStorage.offline) //get existing values
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
          localStorage.setItem('offline', JSON.stringify(offline)) //put the object back
          var ready = document.getElementById('offline-ready').innerHTML
          document.getElementById('offline-request').innerHTML = ready
          document.getElementById('offline-request').style.background =
            '#00693E'
        })
        .catch(function (err) {
          console.log(err)
        })
    })
}
