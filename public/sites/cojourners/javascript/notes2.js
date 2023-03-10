function addNote(noteId) {
  // resize note
  var noteIdText = document.getElementById(noteId).value
  document.getElementById(noteId).style.height = calcHeight(noteIdText) + 'px'
  // save notes
  var notesPage = document.getElementById('notes_page').value
  // find ids of all textareas
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
  saveNote(notesPage, JSON.stringify(notes)) //put the object back
}
async function saveNote(key, value) {
  let db = new Localbase('db')
  db.collection('notes').doc(key).set({
    notes: value,
  })
}
async function showNotes(page) {
  let db = new Localbase('db')
  db.collection('notes')
    .doc(page)
    .get()
    .then((result) => {
      var notes = JSON.parse(result.notes)
      console.log(notes)
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
    })
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
