var CACHE_DYNAMIC_NAME = 'myfriends-run-time-v1'
var DEFAULT_ENTRY = '/content/index.html'

navigator.serviceWorker.getRegistrations().then(function (registrations) {
  for (let registration of registrations) {
    if (
      registration.active.scriptURL === 'https://prototype.myfriends.network/sw.js'
    ) {
      registration.unregister()
    }
  }
})

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
  // which page should we go to?
  var currentPage = window.location.pathname
  var lastpage = ''
  // go to last page visited if you are visiting root page again
  if (localStorage.getItem('lastpage') && currentPage == '/') {
    lastpage = localStorage.getItem('lastpage')
    localStorage.removeItem('lastpage')
    window.location.replace(lastpage)
  } else {
    // stay here if you entered into any other page than the root,
    if (currentPage !== '/') {
      lastpage = localStorage.getItem('lastpage')
      localStorage.setItem('previouspage', lastpage)
      localStorage.setItem('lastpage', window.location.href)
    }
    // otherwise guess the best language for this browser,
    else {
      if ('bob' != 'genious') {
        var home = DEFAULT_ENTRY
        window.location.replace(home)
        return
      }
      fetch('/browserlanguage.php')
        .then(function (response) {
          console.log(response.json)
          return response.json()
        })
        .then(function (jsonFile) {
          var pref = jsonFile.preference
          var codes = jsonFile.languageCodes
          codes.forEach(function (row) {
            if (row.code == pref) {
              var start = row.start
              window.location.replace(start)
            }
          })
        })
        .catch(function (err) {
          console.log('Do not know browser language')
          console.log(err)
        })
    }
    // see which index we should be at
    //findIndexPage();
  }
}
document.addEventListener('DOMContentLoaded', router)
document.addEventListener('DOMContentLoaded', modifyPage)

function restoreDynamic() {
  if (typeof localStorage.offline != 'undefined' && localStorage.offline) {
    console.log('restoreDynamic')
    var offline = JSON.parse(localStorage.offline) //get existing values
    offline.forEach(function (series) {
      fetch(series)
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
function modifyPage() {
  findCollapsible()
  findSummaries()
  findTrainerNotes()
}
function findTrainerNotes() {
  if (localStorage.getItem('mc2Trainer')) {
    // unhide all trainer notes
    var elements = document.getElementsByClassName('trainer-hide')
    for (var i = 0; i < elements.length; i++) {
      elements[i].className = 'trainer'
    }
    // unhide all items which are collapsed for students
    elements = document.getElementsByClassName('collapsible')
    for (var i = 0; i < elements.length; i++) {
      elements[i].className = 'revealed'
    }
    elements = document.getElementsByClassName('collapsed')
    for (var i = 0; i < elements.length; i++) {
      elements[i].style.display = 'block'
    }
  }
}
function findSummaries() {
  var coll = document.getElementsByClassName('summary')
  var i
  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener('click', function () {
      this.classList.toggle('active')
      var text = this.innerHTML
      var new_text = ''
      if (text.includes('+')) {
        new_text = text.replace('+', '-')
      } else {
        new_text = text.replace('-', '+')
      }
      this.innerHTML = new_text
      // get nextElementSibling
      var content = this.nextElementSibling
      // hide or show?
      if (content.style.display === 'block') {
        content.style.display = 'none'
        this.classList.remove('summary-shown')
        this.classList.add('summary-hidden')
      } else {
        content.style.display = 'block'
        this.classList.remove('summary-hidden')
        this.classList.add('summary-shown')
      }
    })
  }
}

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

// page is set in the nav bar of each web page
function pageGoBack(page) {
  if (localStorage.getItem('returnpage')) {
    page = localStorage.getItem('returnpage')
    localStorage.removeItem('returnpage')
  }
  window.location.replace(page)
}
// If you are in Lesson 1 and want a person to go to Lesson 7,
// The return button will now bring them back
// rather than take them to the index.
function goToPageAndSetReturn(page) {
  localStorage.setItem('returnpage', window.location.href)
  window.location.replace(page)
}
