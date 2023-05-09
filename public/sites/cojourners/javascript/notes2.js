/* see https://github.com/dannyconnell/localbase
  we will store data in localbase the same way we store them in localStorage
  The page will be the key and the data will be a json object of all the notes on the page.

*/

async function notesAdd(noteId) {
  console.log(' I am adding note ' + noteId)
  noteResize(noteId)
  var notes = notesCollect()
  await notesSave(notes)
}
function notesAddLocalStorage(notesPage, notes) {
  localStorage.setItem(notesPage, notes) //put the object back
}
async function notesAddDatabase(key, value) {
  //console.log('I am saving note in database ' + key)
  let db = new Localbase('db')
  db.collection('notes').doc(key).set({
    notes: value,
  })
  localStorage.removeItem(key)
}
// Dealing with Textarea Height
function notesCalcHeight(value) {
  let numberOfLineBreaks = (value.match(/\n/g) || []).length
  // look for long lines
  var longLines = 0
  var extraLines = 0
  var lineMax = window.innerWidth / 16
  const line = value.split('/\n')
  var len = line.length
  for (var i = 0; i < len; i++) {
    if (line[i].length > lineMax) {
      extraLines = Math.round(line[i].length / lineMax)
      longLines += extraLines
    }
  }
  // min-height + lines x line-height + padding + border
  let newHeight = 20 + (numberOfLineBreaks + longLines) * 20 + 12 + 2
  return newHeight
}
// collect all notes on this page
function notesCollect() {
  var txtAreas = document.getElementsByTagName('textarea')
  var len = txtAreas.length
  var ids = new Array()
  var notes = new Array()
  for (i = 0; i < len; i++) {
    ids.push(txtAreas[i].id)
  }
  for (var i = 0; i < len; i++) {
    var note = document.getElementById(ids[i])
    var entry = new Object()
    entry.key = ids[i]
    entry.value = note.value
    notes[i] = entry
  }
  return JSON.stringify(notes)
}
async function notesGet(notesPage) {
  var notes = []
  var dataSource = await notesSelectDataSource()
  if (dataSource == 'database') {
    notes = await notesGetDatabase(notesPage)
  } else {
    notes = await notesGetLocalStorage(notesPage)
  }
  console.log(notes)
  return notes
}
async function notesGetDatabase(page) {
  console.log('notesGetDatabase for ' + page)
  var notes = []
  let db = new Localbase('db')
  await db
    .collection('notes')
    .doc(page)
    .get()
    .then((result) => {
      if (result != null) {
        console.log('notesGetDatabase found notes for ' + page)
        console.log(result)
        notes = JSON.parse(result.notes)
        console.log(notes)
      }
    })
  return notes
}
function notesGetLocalStorage(page) {
  var response = localStorage.getItem(page)
  var notes = JSON.parse(response)
  return notes
}
// resize the note area on the page
function noteResize(noteId) {
  var noteIdText = document.getElementById(noteId).value
  document.getElementById(noteId).style.height =
    notesCalcHeight(noteIdText) + 'px'
}
async function notesSave(notes) {
  // collect notes on page
  var dataSource = await notesSelectDataSource()
  var notesPage = document.getElementById('notes_page').value
  if (dataSource == 'database') {
    await notesAddDatabase(notesPage, notes)
  } else {
    notesAddLocalStorage(notesPage, notes)
  }
}
async function notesSelectDataSource() {
  return 'database'
}
async function notesShow(page) {
  if (!localStorage.getItem('notesVersion')) {
    await notesVersionTransition(2)
  }
  var dataSource = await notesSelectDataSource()
  // console.log('notesShow says dataSource is ' + dataSource)
  if (dataSource == 'database') {
    notesShowDatabase(page)
  } else {
    notesShowLocalStorage(page)
  }
}
async function notesShowDatabase(page) {
  var notes = await notesGetDatabase(page)
  console.log(notes)
  if (notes != null) {
    var len = notes.length
    var notePlace = null
    for (var i = 0; i < len; i++) {
      // sometimes people change the number of notes on a page after we publish
      notePlace = document.getElementById(notes[i].key)
      if (notePlace) {
        document.getElementById(notes[i].key).value = notes[i].value
        document.getElementById(notes[i].key).style.height =
          notesCalcHeight(notes[i].value) + 'px'
      }
    }
  } else {
    console.log('no notes to show in database. Try localstorage')
    notesShowLocalStorage(page)
  }
  return
}
function notesShowLocalStorage(page) {
  console.log('notesShow from LocalStorage' + page)
  var notes = notesGetLocalStorage(page)
  if (!notes) {
    return
  }
  var len = notes.length
  var notePlace = null
  for (var i = 0; i < len; i++) {
    // sometimes people change the number of notes on a page after we publish
    notePlace = document.getElementById(notes[i].key)
    if (notePlace) {
      document.getElementById(notes[i].key).value = notes[i].value
      document.getElementById(notes[i].key).style.height =
        notesCalcHeight(notes[i].value) + 'px'
    }
  }

  return
}
async function notesVersionTransition(noteVersion) {
  localStorage.setItem('notesVersion', noteVersion)
  var oldTestimonyPage = 'U1-eng-resource-resource03.html'
  var newTestimonyPage = 'U1-eng-pages-testimony.html'
  var notes = await notesGet(oldTestimonyPage)
  console.log(notes)
  if (notes.length > 2) {
    var jsonNotes = JSON.stringify(notes)
    var dataSource = await notesSelectDataSource()
    if (dataSource == 'database') {
      await notesAddDatabase(newTestimonyPage, jsonNotes)
    } else {
      notesAddLocalStorage(newTestimonyPage, jsonNotes)
    }
  }
}
