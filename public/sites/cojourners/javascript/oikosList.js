let fields = ['name', 'non', 'believer', 'follower', 'leader']
let db = new Localbase('db')

async function showOikosList() {
  console.log('showing Oikos List')
  var count = 0
  var table = document.getElementById('oikos-table')
  await db
    .collection('oikosData')
    .orderBy('personName')
    .get()
    .then((result) => {
      for (var i = 0; i < result.length; i++) {
        let row = table.insertRow()
        for (key in element) {
          let cell = row.insertCell()
          if (key == 'name') {
            let text = document.createElement('input')
            text.type = 'text'
            text.name = 'name'
            text.className = 'flex'
            text.value = element[key]
            var inputId = count + '-' + key
            text.id = inputId
            cell.appendChild(text)
          } else {
            let checkbox = document.createElement('input')
            checkbox.type = 'checkbox'
            checkbox.name = key
            checkbox.className = 'checkbox'
            checkbox.value = 1
            var checkboxId = count + '-' + key
            checkbox.id = checkboxId
            cell.appendChild(checkbox)
            if (element[key] == 1) {
              document.getElementById(checkboxId).checked = true
            }
          }
        }
        count++
      }
    })
  console.log(count)
  if (count < 20) {
    appendTable(count, 20)
  }
  document.addEventListener('change', saveOikos)
}

function appendTable(count, goal) {
  var table = document.getElementById('oikos-table')
  for (let i = count; i <= goal; i++) {
    let row = table.insertRow()
    for (var j = 0; j < fields.length; j++) {
      let cell = row.insertCell()
      if (fields[j] == 'name') {
        let text = document.createElement('input')
        text.type = 'text'
        text.name = 'name'
        text.value = ''
        text.className = 'flex'
        var inputId = i + '-' + fields[j]
        text.id = inputId
        cell.appendChild(text)
      } else {
        let checkbox = document.createElement('input')
        checkbox.type = 'checkbox'
        checkbox.name = fields[j]
        checkbox.value = 1
        checkbox.className = 'checkbox'
        var checkboxId = i + '-' + fields[j]
        checkbox.id = checkboxId
        cell.appendChild(checkbox)
      }
    }
  }
}

function saveOikos() {
  var table = document.getElementById('oikos-table')
  let data = new Array()
  let checked = new Array()
  var rows = table.rows.length - 1
  for (var i = 0; i < rows; i++) {
    var id = document.getElementById(i + '-id').value
    if (document.getElementById(i + '-name').value) {
      for (var j = 1; j < 5; j++) {
        checked[j] = 0
      }
      if (document.getElementById(i + '-non').checked) {
        checked[1] = 1
      }
      if (document.getElementById(i + '-believer').checked) {
        checked[2] = 1
      }
      if (document.getElementById(i + '-follower').checked) {
        checked[3] = 1
      }
      if (document.getElementById(i + '-leader').checked) {
        checked[4] = 1
      }
      var person = {
        id: id,
        name: document.getElementById(i + '-name').value,
        non: checked[1],
        believer: checked[2],
        follower: checked[3],
        leader: checked[4],
      }
      db.collection('OikosList').doc(id).set({ person })
    }
  }
}
