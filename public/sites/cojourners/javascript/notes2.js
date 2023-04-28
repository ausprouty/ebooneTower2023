/* see https://github.com/dannyconnell/localbase
  we will store data in localbase the same way we store them in localStorage
  The page will be the key and the data will be a json object of all the notes on the page.

*/

async function addNote(noteId) {
  console.log(' I am adding note ' + noteId)
  noteResize(noteId)
  var notes = notesCollect()
  await saveNotes(notes)
}
async function getNotes(notesPage) {
  var notes = []
  var dataSource = await selectDataSource()
  if (dataSource == 'database') {
    notes = await getNotesDatabase(notesPage)
  } else {
    notes = await getNotesLocalStorage(notesPage)
  }
  console.log(notes)
  return notes
}
// resize the note area on the page
function noteResize(noteId) {
  var noteIdText = document.getElementById(noteId).value
  document.getElementById(noteId).style.height = calcHeight(noteIdText) + 'px'
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
async function saveNotes(notes) {
  // collect notes on page
  var dataSource = await selectDataSource()
  var notesPage = document.getElementById('notes_page').value
  if (dataSource == 'database') {
    await addNoteDatabase(notesPage, notes)
  } else {
    addNoteLocalStorage(notesPage, notes)
  }
}
function addNoteLocalStorage(notesPage, notes) {
  localStorage.setItem(notesPage, notes) //put the object back
}
async function addNoteDatabase(key, value) {
  //console.log('I am saving note in database ' + key)
  let db = new Localbase('db')
  db.collection('notes').doc(key).set({
    notes: value,
  })
  localStorage.removeItem(key)
}
async function selectDataSource() {
  return 'database'
}
async function showNotes(page) {
  var dataSource = await selectDataSource()
  // console.log('showNotes says dataSource is ' + dataSource)
  if (dataSource == 'database') {
    showNotesDatabase(page)
  } else {
    showNotesLocalStorage(page)
  }
}
async function getNotesDatabase(page) {
  console.log('getNotesDatabase for ' + page)
  var notes = []
  let db = new Localbase('db')
  await db
    .collection('notes')
    .doc(page)
    .get()
    .then((result) => {
      if (result != null) {
        console.log('getNotesDatabase found notes for ' + page)
        notes = JSON.parse(result.notes)
        console.log(notes)
      }
    })
  return notes
}
async function showNotesDatabase(page) {
  var notes = await getNotesDatabase(page)
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
          calcHeight(notes[i].value) + 'px'
      }
    }
  } else {
    console.log('no notes to show in database. Try localstorage')
    showNotesLocalStorage(page)
  }
  return
}
function getNotesLocalStorage(page) {
  var response = localStorage.getItem(page)
  var notes = JSON.parse(response)
  return notes
}
function showNotesLocalStorage(page) {
  console.log('showNotes from LocalStorage' + page)
  var notes = getNotesLocalStorage(page)
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
        calcHeight(notes[i].value) + 'px'
    }
  }

  return
}

// Dealing with Textarea Height
function calcHeight(value) {
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
