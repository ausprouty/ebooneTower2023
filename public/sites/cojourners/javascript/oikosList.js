let fields = ['remove', 'id', 'name', 'non', 'believer', 'follower', 'leader']
let dbOikos = new Localbase('db')
let goal = 20 // number of lines

async function showOikosList() {
  console.log('showing Oikos List')
  var count = 0
  await dbOikos
    .collection('oikosData')
    .orderBy('name')
    .get()
    .then((result) => {
      var table = document.getElementById('oikos-table')
      for (var i = 0; i < result.length; i++) {
        let row = table.insertRow()
        for (var j = 0; j < fields.length; j++) {
          var key = fields[j]
          let cell = row.insertCell()
          if (key == 'delete') {
            let checkbox = document.createElement('input')
            checkbox.type = 'checkbox'
            checkbox.name = key
            checkbox.className = 'checkbox'
            checkbox.value = 1
            var checkboxId = 'remove' + i
            checkbox.id = checkboxId
            cell.appendChild(checkbox)
            if (result[i][key] == 1) {
              document.getElementById(checkboxId).checked = true
            }
          }
          if (key == 'id') {
            let text = document.createElement('input')
            text.type = 'hidden'
            text.name = 'id'
            text.value = result[i].id
            text.className = 'flex'
            var inputId = 'id' + i
            text.id = inputId
            cell.appendChild(text)
          } else if (key == 'name') {
            let text = document.createElement('input')
            text.type = 'text'
            text.name = 'name'
            text.className = 'flex'
            text.value = result[i].name
            var inputId = 'name' + i
            text.id = inputId
            cell.appendChild(text)
          } else {
            let checkbox = document.createElement('input')
            checkbox.type = 'checkbox'
            checkbox.name = key
            checkbox.className = 'checkbox'
            checkbox.value = 1
            var checkboxId = fields[j] + i
            checkbox.id = checkboxId
            cell.appendChild(checkbox)
            if (result[i][key] == 1) {
              document.getElementById(checkboxId).checked = true
            }
          }
        }
        count++
      }
      console.log(count)
      if (count < goal) {
        appendTable(count, goal)
      }
      document.addEventListener('change', saveOikos)
    })
}

function appendTable(count, goal) {
  var table = document.getElementById('oikos-table')
  for (let i = count; i <= goal; i++) {
    let row = table.insertRow()
    for (var j = 0; j < fields.length; j++) {
      let cell = row.insertCell()
      if (fields[j] == 'id') {
        let text = document.createElement('input')
        text.type = 'hidden'
        text.name = 'row' + i
        text.value = i
        text.className = 'flex'
        var inputId = 'id' + i
        text.id = inputId
        cell.appendChild(text)
      } else if (fields[j] == 'name') {
        let text = document.createElement('input')
        text.type = 'text'
        text.name = 'name'
        text.value = ''
        text.className = 'flex'
        var inputId = 'name' + i
        text.id = inputId
        cell.appendChild(text)
      } else {
        let checkbox = document.createElement('input')
        checkbox.type = 'checkbox'
        checkbox.name = fields[j]
        checkbox.value = 1
        checkbox.className = 'checkbox'
        var checkboxId = fields[j] + i
        checkbox.id = checkboxId
        cell.appendChild(checkbox)
      }
    }
  }
}

async function saveOikos() {
  var checked = []
  const table = document.getElementById('oikos-table') // Get the table element by its ID
  const rows = table.getElementsByTagName('tr') // Get all the rows in the table
  console.log(rows.length)
  var dataRows = rows.length - 1
  for (let i = 0; i < dataRows; i++) {
    var removePerson = document.getElementById('remove' + i).checked
    var id = document.getElementById('id' + i).value
    var name = document.getElementById('name' + i).value
    if (name) {
      for (var j = 1; j < 5; j++) {
        checked[j] = 0
      }
      if (document.getElementById('non' + i).checked) {
        checked[1] = 1
      }
      if (document.getElementById('believer' + i).checked) {
        checked[2] = 1
      }
      if (document.getElementById('follower' + i).checked) {
        checked[3] = 1
      }
      if (document.getElementById('leader' + i).checked) {
        checked[4] = 1
      }
      if (!removePerson) {
        await dbOikos.collection('oikosData').doc(id).set({
          id: id,
          name: name,
          non: checked[1],
          believer: checked[2],
          follower: checked[3],
          leader: checked[4],
        })
      }
      if (removePerson) {
        await dbOikos.collection('oikosData').doc({ id: id }).delete()
        refreshScreen()
      }
    }
  }
}

async function refreshScreen() {
  await dbOikos
    .collection('oikosData')
    .orderBy('name')
    .get()
    .then((result) => {
      var maxId = 0
      for (var i = 0; i < result.length; i++) {
        if (result[i].id > maxId) {
          maxId = result[i].id
        }
        document.getElementById('remove' + i).checked = 0
        document.getElementById('id' + i).value = result[i].id
        document.getElementById('name' + i).value = result[i].name
        document.getElementById('non' + i).checked = result[i].non
        document.getElementById('believer' + i).checked = result[i].believer
        document.getElementById('follower' + i).checked = result[i].follower
        document.getElementById('leader' + i).checked = result[i].leader
      }
      i++
      maxId++
      for (i = result.length; i < goal; i++) {
        document.getElementById('remove' + i).checked = 0
        var newId = maxId + i
        document.getElementById('id' + i).value = newId
        document.getElementById('name' + i).value = ''
        document.getElementById('non' + i).checked = 0
        document.getElementById('believer' + i).checked = 0
        document.getElementById('follower' + i).checked = 0
        document.getElementById('leader' + i).checked = 0
      }
    })
}
