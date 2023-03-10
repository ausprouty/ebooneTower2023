

async function showNotes(route) {
  console.log('notes attempting to find source')
  var source = await getDataSource()
  console.log('notes found source')
  var data = []
  source = 'localhost'
  if (source == 'database') {
    data = await this.notesFromDatabase(route)
  } else {
    data = this.notesFromLocalStorage(route)
  }
  for (var i = 0; i < data.length; i++) {
    var noteid = data[i].noteid
    var height = this.calcNoteHeight(data[i].note)
    document.getElementById(noteid).value = data[i].note
    document.getElementById(noteid).style.height = height + 'px'
  }
  console.log('notes displayed')
}

async function addNote(noteid, route, noteText) {
  var source = localStorage.getItem('sent67NoteSource')
  if (source == 'database') {
    return await this.addNoteToDatabase(noteid, route, noteText)
  }
  return this.addNoteToLocalStorage(noteid, route, noteText)
}

function notesFromLocalStorage(route) {
  var notes = JSON.parse(localStorage.getItem('Notes-' + route))
  if (notes == null) {
    notes = []
  }
  return notes
}

function addNoteToLocalStorage(noteid, route, noteText) {
  var height = this.calcNoteHeight(noteText)
  // resize note
  document.getElementById(noteid).style.height = height + 'px'
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
    entry.noteid = ids[i]
    entry.note = note.value

    notes[i] = entry
  }
  localStorage.setItem('Notes-' + route, JSON.stringify(notes)) //put the object back
}
async function notesFromDatabase(route) {
  try {
    const sqlite = new SQLiteConnection(CapacitorSQLite)
    let db = await this.openDatabase()
    let query = 'SELECT * FROM notes WHERE page=?'
    var res = await db.query(query, [route])
    await sqlite.closeConnection('db_sent67notes')
    return res.values
  } catch (err) {
    alert(' error in SQLite Service Notes')
    console.log(`Error: ${err}`)
    throw new Error(`Error: ${err}`)
  }
}
async function addNoteToDatabase(noteid, route, noteText) {
  try {
    const sqlite = new SQLiteConnection(CapacitorSQLite)
    let db = await this.openDatabase()
    let query = 'SELECT note FROM notes WHERE page=? AND noteid = ?'
    let values = [route, noteid]
    let res = await db.query(query, values)
    if (res.values[0] !== undefined) {
      query = 'UPDATE notes set note = ?  WHERE page=? AND noteid = ?'
    } else {
      query = 'INSERT INTO notes (note, page, noteid) VALUES (?, ?, ?)'
    }
    values = [noteText, route, noteid]
    res = await db.query(query, values)
    query = 'SELECT note FROM notes WHERE page=? AND noteid = ?'
    values = [route, noteid]
    res = await db.query(query, values)
    return this.calcNoteHeight(res.values[0].note) + 'px'
  } catch (err) {
    console.log(`Error: ${err}`)
    throw new Error(`Error: ${err}`)
  }
}

// Dealing with Textarea Height
// from https://css-tricks.com/auto-growing-inputs-textareas/
function calcNoteHeight(value) {
  let numberOfLineBreaks = (value.match(/\n/g) || []).length
  // look for long lines
  var longLines = 0
  var extraLines = 0
  var lineMax = window.innerWidth / 7
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
async function getDataSource() {
  var noteSource = localStorage.getItem('sent67NoteSource')
  if (!noteSource) {
    if (!window.indexedDB) {
      console.log('Your browser does not support IndexedDB')
      noteSource = 'localStorage'
    } else {
      console.log('Your browser supports IndexedDB')
      noteSource = await createDataStore()
    }
    localStorage.setItem('sent67NoteSource', noteSource)
  }
  return noteSource
}

//Opening a Database
// https://sweetcode.io/indexeddb-client-storage/
function openDatabase() {
  // Open the database
  //parameters - database name and version number. - integer
  var db
  var request = indexedDB.open('Database', 1)
  db = this.result

  //Generating handlers
  //Error handlers
  request.onerror = function (event) {
    console.log('Error: ')
  }

  //OnSuccess Handler
  request.onsuccess = function (event) {
    console.log('Success: ')
    db = event.target.result
  }

  //OnUpgradeNeeded Handler
  request.onupgradeneeded = function (event) {
    console.log('On Upgrade Needed')

    db = event.target.result

    // Create an objectStore for this database
    //Provide the ObjectStore name and provide the keyPath which acts as a primary key
    var objectStore = db.createObjectStore('ObjectStoreName', {
      keyPath: 'id',
      autoIncrement: true,
    })
  }
}
//Simple function to get the ObjectStore
//Provide the ObjectStore name and the mode ('readwrite')
function getObjectStore(store_name, mode) {
  var tx = db.transaction(store_name, mode)
  return tx.objectStore(store_name)
}

//Adding to the Database
function addPerson(store_name) {
  var obj = { fname: 'Test', lname: 'Test', age: 30, email: 'test@company.com' }
  var store = getObjectStore(store_name, 'readwrite')
  var req
  try {
    req = store.add(obj)
  } catch (e) {
    throw e
  }
  req.onsuccess = function (evt) {
    alert('Insertion in DB successful')
  }
  req.onerror = function () {
    alert('Insertion in DB Failed ', this.error)
  }
}
