var CACHE_DYNAMIC_NAME = 'content-1'

if ('serviceWorker' in navigator) {
  navigator.serviceWorker
    .register('/sw.js')
    .then(function () {
      console.log('Service worker registered!')
      localStorage.setItem('swWorking', 'TRUE')
    })
    .catch(function (err) {
      console.log(err)
      localStorage.setItem('swWorking', 'FALSE')
    })
}

// return to last page if restarting
// check for current dynamic

function router() {
  // check if dynamic cache needs updating
  if (CACHE_DYNAMIC_NAME != localStorage.getItem('dynamic-cache')) {
    restoreDynamic()
    localStorage.setItem('dynamic-cache', CACHE_DYNAMIC_NAME)
  }
  // store last page so that root index can redirect when you come again
  localStorage.setItem('lastpage', window.location.href)
  //
}
document.addEventListener('DOMContentLoaded', router)

// check to see if this is an index file for a series and get value index.json
document.addEventListener('DOMContentLoaded', (event) => {
  // restore revealed areas
  appRevealedRestore()
  findCollapsible()
  appRevealDivCalled()
})

function findCollapsible() {
  var coll = document.getElementsByClassName('collapsible')
  var i
  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener('click', function () {
      this.classList.toggle('active')
      var content = this.nextElementSibling
      if (content.style.display === 'block') {
        content.style.display = 'none'
        //this.className= "collapsible";
        this.classList.remove('revealed')
        this.classList.add('collapsible')
      } else {
        content.style.display = 'block'
        //this.className= "revealed";
        this.classList.remove('collapsible')
        this.classList.add('revealed')
      }
    })
  }
}
function appRevealDivCalled() {
  var windowLocation = window.location.href
  if (windowLocation.includes('#')) {
    var divCalled = windowLocation.split('#')[1]
    var div = null
    var divs = document.getElementsByClassName('summary')
    for (var i = 0; i < divs.length; i++) {
      if (divs[i].innerHTML.indexOf(divCalled) != -1) {
        div = divs[i].id
      }
    }
    if (div) {
      appRevealDiv(div)
    }
  }
}
function appRevealDiv(divId) {
  console.log(divId)
  var windowLocation = appRevealWindowLocation()
  var button = document.getElementById(divId)
  console.log(button)
  var content = button.nextElementSibling
  if (content.style.display === 'block') {
    // we save this in case we need to goToPageAndSetReturn;
    appRevealSummaryDelete(windowLocation, divId)
    content.style.display = 'none'
    button.classList.remove('summary-shown')
    button.classList.add('summary-hidden')
  } else {
    // we save this in case we need to goToPageAndSetReturn;
    appRevealSummaryAdd(windowLocation, divId)
    content.style.display = 'block'
    button.classList.remove('summary-hidden')
    button.classList.add('summary-shown')
  }
  var text = button.innerHTML
  if (text.includes('+')) {
    button.innerHTML = text.replace('+', '-')
  } else {
    button.innerHTML = text.replace('-', '+')
  }
}

function appRevealSummary(divId) {
  appRevealDiv(divId)
}
function appRevealSummaryAdd(windowLocation, divId) {
  var current = appRevealSummaryRetreive(windowLocation)
  if (current) {
    appRevealSummaryClose(windowLocation, current)
  }
  current = divId
  appRevealSummarySave(windowLocation, current)
}
function appRevealSummaryClose(windowLocation, divId) {
  console.log(divId)
  if (!document.getElementById(divId)) {
    return
  }
  var button = document.getElementById(divId)
  //button.classList.toggle("active");
  var text = button.innerHTML
  if (text.includes('-')) {
    var new_text = text.replace('-', '+')
    button.innerHTML = new_text
  }
  var content = button.nextElementSibling
  if (content.style.display === 'block') {
    content.style.display = 'none'
    button.classList.remove('summary-shown')
    button.classList.add('summary-hidden')
  }
}

function appRevealSummaryDelete(windowLocation, divId) {
  //var current = appRevealSummaryRetreive(windowLocation);
  //for( var i = 0; i < current.length; i++){
  //    if ( current[i] === id) {
  //        current.splice(i, 1);
  //    }
  //}
  var current = null
  appRevealSummarySave(windowLocation, current)
}

function appRevealSummaryRetreive(windowLocation) {
  if (window.localStorage.getItem('sent67SummaryRevealed')) {
    var current = JSON.parse(
      window.localStorage.getItem('sent67SummaryRevealed')
    )
    if (current.page == windowLocation) {
      return current.revealed
    }
  }
  //var blank = []
  var blank = null
  return blank
}
function appRevealSummarySave(windowLocation, current) {
  var record = {}
  record.page = windowLocation
  record.revealed = current
  window.localStorage.setItem('sent67SummaryRevealed', JSON.stringify(record))
}
function appRevealWindowLocation() {
  var windowLocation = window.location.href
  if (windowLocation.includes('#')) {
    windowLocation = windowLocation.split('#')[0]
  }
  return windowLocation
}
function appRevealedRestore() {
  var windowLocation = appRevealWindowLocation()
  var current = appRevealSummaryRetreive(windowLocation)
  if (current) {
    appRevealSummary(current)
  }
}

function goToPageAndSetReturn(page, anchor = null) {
  // If you are in Lesson 1 and want a person to go to Lesson 7,
  // The return button will now bring them back
  // rather than take them to the index.
  // DANGER:  If window.location.href contains '#' you must remove it
  // This is because the person has already returned once.
  var windowLocation = appRevealWindowLocation()
  var returnLocation = windowLocation + anchor
  localStorage.setItem('returnpage', returnLocation)
  // save revealed for return
  if (localStorage.getItem('sent67SummaryRevealed')) {
    var last = localStorage.getItem('sent67SummaryRevealed')
    localStorage.setItem('sent67SummaryRevealedSaved', last)
  }
  window.location.replace(page)
}

function pageGoBack(page) {
  if (localStorage.getItem('returnpage')) {
    page = localStorage.getItem('returnpage')
    localStorage.removeItem('returnpage')
  }
  if (localStorage.getItem('sent67SummaryRevealedSaved')) {
    var last = localStorage.getItem('sent67SummaryRevealedSaved')
    localStorage.setItem('sent67SummaryRevealed', last)
  }
  window.location.replace(page)
}
function popUp(field) {
  var content = document.getElementById(field)
  if (content.style.display === 'block') {
    content.style.display = 'none'
    this.classList.remove('revealed')
    this.classList.add('collapsible')
  } else {
    content.style.display = 'block'
    //this.className= "revealed";
    this.classList.remove('collapsible')
    this.classList.add('revealed')
  }
}
function restoreDynamic() {
  if (typeof localStorage.offline != 'undefined' && localStorage.offline) {
    console.log('restoreDynamic')
    var offline = JSON.parse(localStorage.offline) //get existing values
    offline.forEach(function (series) {
      var ajaxPromise = fetch(series)
        .then(function (response) {
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
    })
  }
}

// for sharing
//https://developers.google.com/web/updates/2016/09/navigator-share
