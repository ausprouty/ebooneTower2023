import { CapacitorSQLite, SQLiteConnection } from "@capacitor-community/sqlite";

export default {
	async notes (route){
		console.log ('notes attempting to find source')
		var source = await this.getDataSource()

		console.log ('notes found source')
		var data = []
		if (source == 'database'){
			data =  await this.notesFromDatabase(route)
		}
		else{
            data =  this.notesFromLocalStorage(route)
		}
		for (var i = 0; i< data.length; i++){
			var noteid = data[i].noteid
			var height= this.calcNoteHeight(data[i].note)
			document.getElementById(noteid).value = data[i].note
			document.getElementById(noteid).style.height = height + 'px'
		}
		console.log ('notes displayed')
	},

	async addNote(noteid, route, noteText){
		var source = localStorage.getItem('mc2NoteSource');
		if (source == 'database'){
			return await this.addNoteToDatabase(noteid, route, noteText)
		}
		return this.addNoteToLocalStorage(noteid, route, noteText)
	},


	notesFromLocalStorage(route){
		var notes = JSON.parse(localStorage.getItem('Notes-'+ route));
		if (notes == null){
			notes = [];
		}
		return notes;
	},

    addNoteToLocalStorage (noteid, route, noteText ){
       var height= this.calcNoteHeight(noteText)
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
		localStorage.setItem('Notes-'+ route, JSON.stringify(notes)) //put the object back
	},
	async notesFromDatabase(route){
			try {
				const sqlite = new SQLiteConnection(CapacitorSQLite);
				let db =  await this.openDatabase()
				let query = 'SELECT * FROM notes WHERE page=?'
				var res = await db.query(query,  [route])
				await sqlite.closeConnection("db_mc2notes");
				return res.values
			} catch (err) {
				alert (' error in SQLite Service Notes')
				console.log(`Error: ${err}`);
				throw new Error(`Error: ${err}`);
			}
	},
	async addNoteToDatabase(noteid, route, noteText){
		try {
			const sqlite = new SQLiteConnection(CapacitorSQLite);
			let db =  await this.openDatabase()
			let query = 'SELECT note FROM notes WHERE page=? AND noteid = ?'
			let values = [route, noteid]
			let res = await db.query(query, values);
			if (res.values[0] !== undefined) {
				query = 'UPDATE notes set note = ?  WHERE page=? AND noteid = ?'
			}
			else{
				query = 'INSERT INTO notes (note, page, noteid) VALUES (?, ?, ?)'
			}
			values = [noteText, route, noteid]
			res = await db.query(query, values);
			query = 'SELECT note FROM notes WHERE page=? AND noteid = ?'
			values = [route, noteid]
			res = await db.query(query, values);
			return this.calcNoteHeight(res.values[0].note) +'px'

		} catch (err) {
			console.log(`Error: ${err}`);
			throw new Error(`Error: ${err}`);
		}
	},
	async openDatabase(){
		try {
			const sqlite = new SQLiteConnection(CapacitorSQLite);
			const ret = await sqlite.checkConnectionsConsistency();
			const isConn = (await sqlite.isConnection("db_mc2notes")).result;
			//console.log(`after isConnection ${isConn}`);
			let db;
			if (ret.result && isConn) {
				//console.log("I am retreiving connection")
				db = await sqlite.retrieveConnection("db_mc2notes");
			} else {
				//console.log("I am creating  connection")
				db = await sqlite.createConnection("db_mc2notes", false, "no-encryption", 1);
			}
			await db.open();
			return db
		} catch (err) {
			console.log(`Error: ${err}`);
			throw new Error(`Error: ${err}`);
		}

	},
  // Dealing with Textarea Height
	// from https://css-tricks.com/auto-growing-inputs-textareas/
	calcNoteHeight(value) {
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
	},
	async getDataSource(){

		var noteSource = localStorage.getItem('mc2NoteSource')
		if (!noteSource){
			noteSource= await createDataStore()
		}
		return noteSource
	},
	async createDataStore(){
		const sqlite = new SQLiteConnection(CapacitorSQLite);
		console.log ('createDataStore attempting to create database')
		try {
			const ret = await sqlite.checkConnectionsConsistency();
			const isConn = (await sqlite.isConnection("db_mc2notes")).result;
			let db;
			if (ret.result && isConn) {
				db = await sqlite.retrieveConnection("db_mc2notes");
			} else {
				db = await sqlite.createConnection("db_mc2notes", false, "no-encryption", 1);
			}
			await db.open();
			const query = `
			CREATE TABLE IF NOT EXISTS notes (
					page   VARCHAR NOT NULL,
					noteid VARCHAR NOT NULL,
					note TEXT,
					CONSTRAINT note_index PRIMARY KEY (page, noteid)
				); `
			const res = await db.execute(query);
			if (res.changes && res.changes.changes && res.changes.changes < 0) {
				console.log ('createDataStore says database was NOT created')
				localStorage.setItem('mc2NoteSource', 'localstorage')
				return;
			}
			await sqlite.closeConnection("db_mc2notes");
			localStorage.setItem('mc2NoteSource', 'database')
		} catch (err) {
			localStorage.setItem('mc2NoteSource', 'localstorage')
		}
		return localStorage.getItem('mc2NoteSource')
	}
}
