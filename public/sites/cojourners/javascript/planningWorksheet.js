function getDate() {
  var today = new Date()
  var dd = String(today.getDate()).padStart(2, '0')
  var mm = String(today.getMonth() + 1).padStart(2, '0')
  var yyyy = today.getFullYear()
  var dateTaken = mm + '/' + dd + '/' + yyyy
  return dateTaken
}

async function getFormatedCycleData(cycle) {
  var data = {}
  var stored = await getPlan(cycle)
  for (var i = 0; i < stored.length; i++) {
    var key = stored[i].key
    var value = '<li>' + stored[i].value + '</li>'
    console.log(value)
    var formatted = value.replace(/\n/g, '</li><li>')
    console.log(formatted)
    data[key] = formatted
  }
  console.log(data)
  return data
}

async function shareEvangelismPlan() {
  var dateTaken = getDate()
  var content = '<h2> Evangelism Planning Worksheet (' + dateTaken + ')</h2>'
  var cycle = getCycle()
  var cycleData = await getFormatedCycleData(cycle)
  console.log(cycleData)
  content += '<h3>Prayer Strategy </h3>'
  content += '<ul>' + cycleData.PrayerStrategy + '</ul>'
  content += '<h3>Natural Evangelism Mode Strategy</h3>'
  content += '<ul>' + cycleData.NaturalEvangelismStrategy + '</ul>'
  content += '<h3>Community Evangelism Mode Strategy:</h3>'
  content += '<ul>' + cycleData.CommunityEvangelismStrategy + '</ul>'
  content += '<h3>Ministry Evangelism Mode Strategy:</h3>'
  content += '<ul>' + cycleData.MinistryEvangelismStrategy + '</ul>'
  content += '<h3>Building Strategy<h3>'
  content += '<ul>' + cycleData.BuildingStrategy + '</ul>'
  content += '<h3>Equipping Strategy </h3>'
  content += '<ul>' + cycleData.EquippingStrategy + '</ul>'
  content += '<h3>from www.cojourners.sent67.com/plan</h3>'
  document.getElementById('pdf').innerHTML = content
  let pdf = new jsPDF('p', 'pt', 'letter')
  let srcwidth = document.getElementById('pdf').scrollWidth
  pdf.html(document.getElementById('pdf'), {
    html2canvas: {
      scale: 595.26 / srcwidth,
      scrollY: 0,
    },
    x: 5,
    y: 5,
    callback: function (pdf) {
      pdf.save('Evangelism Planning Worksheet.pdf')
      document.getElementById('pdf').innerHTML = ''
    },
  })
}

async function showPlan() {
  var cycle = getCycle()
  showCycle(cycle)
  var data = await getPlan(cycle)
  console.log(data)
  if (data) {
    for (var i = 0; i < data.length; i++) {
      var id = data[i].key
      var text = ''
      if (data[i].value) {
        var text = data[i].value
      }
      document.getElementById(id).value = text
      document.getElementById(id).style.height = calcPlanHeight(text) + 'px'
    }
  }

  // from https://css-tricks.com/auto-growing-inputs-textareas/

  var coll = document.getElementsByClassName('resize-ta')
  for (var i = 0; i < coll.length; i++) {
    coll[i].addEventListener('keyup', function () {
      this.style.height = calcPlanHeight(this.value) + 'px'
      addPlan()
    })
  }
}

async function addPlan() {
  // save plan for this cycle
  //var cycle = document.getElementById('evangelismCycleNumber').value
  var cycle = 1
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
  cycle = getCycle()
  //localStorage.setItem('evangelismPlan' + cycle, JSON.stringify(notes))
  savePlan(cycle, notes)
}

async function savePlan(cycle, notes) {
  //console.log('I am saving note in database ' + key)
  let db = new Localbase('db')
  db.collection('plan').doc(cycle).set({
    value: notes,
  })
}
async function getLatestPlan() {
  var notes = []
  let db = new Localbase('db')
  await db
    .collection('plan')
    .orderBy('key', 'desc')
    .limit(1)
    .get()
    .then((notes) => {
      console.log('plan: ', notes)
    })
  return notes
}
async function getPlan(cycle) {
  var notes = []
  if (localStorage.getItem('evangelismPlan' + cycle)) {
    var local = localStorage.getItem('evangelismPlan' + cycle)
    notes = JSON.parse(local)
    savePlan(cycle, notes)
    localStorage.removeItem('evangelismPlan' + cycle)
    return notes
  } else {
    let db = new Localbase('db')
    await db
      .collection('plan')
      .doc(cycle)
      .get()
      .then((result) => {
        if (result != null) {
          notes = result.value
          console.log(notes)
        }
      })
    return notes
  }
}

function calcPlanHeight(value) {
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

function getCycle() {
  var cycle = 1
  if (localStorage.getItem('evangelismPlanCycle')) {
    cycle = localStorage.getItem('evangelismPlanCycle')
  }
  return cycle
}

function nextCycle() {
  var cycle = 1
  if (localStorage.getItem('evangelismPlanCycle')) {
    cycle = localStorage.getItem('evangelismPlanCycle')
  }
  cycle++
  localStorage.setItem('evangelismPlanCycle', cycle)
  showCycle(cycle)
  showPlan()
}

function showCycle(cycle) {
  var coll = document.getElementsByClassName('cycle')
  for (var i = 0; i < coll.length; i++) {
    coll[i].innerHTML = 'Cycle ' + cycle
  }
}
