document.addEventListener('DOMContentLoaded', displayTeacherNotes)

function displayTeacherNotes() {
  if (!document.getElementById('show-toggle-teacher')) {
    return
  }
  var trainerNotesHidden = document.getElementsByClassName('trainer-hide')
  var trainerNotesVisible = document.getElementsByClassName('trainer')
  if (trainerNotesHidden.length == 0 && trainerNotesVisible.length == 0) {
    hideTeacherNotesToggle()
  } else {
    var showNotes = ''
    if (localStorage.getItem('cojourners-teaching-notes')) {
      showNotes = localStorage.getItem('cojourners-teaching-notes')
    }
    if (showNotes == '') {
      hideTeacherNotesToggle()
    }
    if (showNotes != '') {
      document.getElementById('show-toggle-teacher').style.visibility =
        'visible'
      if (showNotes == 'show') {
        showTeacherNotes()
        expandCollapsible()
      } else if (showNotes == 'hide') {
        hideTeacherNotes()
        collapseCollapsible()
      }
    }
  }
}
// remove space for 'Show Facilitator Notes' and all facilitor notes
function hideTeacherNotesToggle() {
  document.getElementById('show-toggle-teacher').style.display = 'none'
  var elements = document.getElementsByClassName('trainer-hide')
  for (var i = 0; i < elements.length; i++) {
    elements[i].style.display = 'none'
  }
}

function hideTeacherNotes() {
  var teacherNoteCount = document.getElementById('TrainerNoteCount')
  if (teacherNoteCount !== null) {
    var count = teacherNoteCount.value
    var notePlace = ''
    for (var i = 1; i <= count; i++) {
      notePlace = document.getElementById('TrainerNote' + i)
      if (notePlace) {
        notePlace.className = 'trainer-hide'
      }
    }
    // change Link
    var id = 'toggle-teacher'
    document.getElementById(id).innerHTML = 'Show Teacher Notes'
  }
}

function showTeacherNotes() {
  var teacherNoteCount = document.getElementById('TrainerNoteCount')
  if (teacherNoteCount !== null) {
    var count = teacherNoteCount.value
    var notePlace = ''
    for (var i = 1; i <= count; i++) {
      notePlace = document.getElementById('TrainerNote' + i)
      if (notePlace) {
        notePlace.className = 'trainer'
      }
    }
    // change Link
    var id = 'toggle-teacher'
    document.getElementById(id).innerHTML = 'Hide Teacher Notes'
  }
}
function toggleTeacherNotes() {
  var showNotes = 'show'
  if (localStorage.getItem('cojourners-teaching-notes')) {
    if (localStorage.getItem('cojourners-teaching-notes') == 'show') {
      showNotes = 'hide'
    }
  }
  localStorage.setItem('cojourners-teaching-notes', showNotes)
  displayTeacherNotes()
}

function expandCollapsible() {
  /*
    <button id="revealButton0" type="button"
      class="external-movie">Watch &nbsp;<i>This is Discipling</i>&nbsp; online
    </button>
    <div class="collapsed">[youtube]aQbmaKuT0Oo</div>
*/
  var elements = document.getElementsByClassName('external-movie')
  for (var i = 0; i < elements.length; i++) {
    var content = elements[i].nextElementSibling
    var video = content.innerHTML
    if (!video.match(/<iframe/g)) {
      var iframe = mc2CreateIframe(video)
      content.innerHTML = iframe
    }
    content.style.display = 'block'
    //this.classList.remove('external-movie')
    elements[i].classList.add('revealed')
  }
  /* unhide all items which are collapsed for students
  SUMMARIES
  <div onclick = "appRevealSummary('Summary0');" id="Summary0" class="summary">
   <h2>+ Summary</h2></div>
  <div class="collapsed" id ="Text0">
     <p>The Explorer asks good questions,.</p>
  </div>

  to

  <div onclick = "appRevealSummary('Summary0');" id="Summary0" class="summary summary-shown">
   <h2>- Summary</h2></div>
  <div class="collapsed" id ="Text0" style= "display:block">
     <p>The Explorer asks good questions,.</p>
  </div>

  class summary to class summary summary-shown
+ Motivation and Encouragement to- Motivation and Encouragement
    collapsed to style.dispaly = block
*/
  elements = document.getElementsByClassName('summary')
  for (var i = 0; i < elements.length; i++) {
    elements[i].classList.add('summary-shown')
    elements[i].classList.remove('summary-hidden')
    var text = elements[i].innerHTML
    if (text.includes('+')) {
      elements[i].innerHTML = text.replace('+', '-')
    }
    var content = elements[i].nextElementSibling
    content.style.display = 'block'
  }
}
function collapseCollapsible() {
  // unhide all items which are collapsed for students
  var elements = document.getElementsByClassName('revealed')
  for (var i = 0; i < elements.length; i++) {
    elements[i].className = 'collapsible'
  }
  // do this one first
  elements = document.getElementsByClassName('summary')
  for (var i = 0; i < elements.length; i++) {
    elements[i].classList.remove('summary-shown')
    elements[i].classList.add('summary-hidden')
    var text = elements[i].innerHTML
    if (text.includes('-')) {
      elements[i].innerHTML = text.replace('-', '+')
    }
    var content = elements[i].nextElementSibling
    content.style.display = 'none'
  }
}
