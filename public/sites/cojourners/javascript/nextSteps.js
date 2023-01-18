
function generateNextSteps() {
  const div = document.getElementById('next-steps-area')
  var content = showStepsPending()
  content += considerShowNewStep()
  div.innerHTML = content
  document.getElementById('next-steps-completed').innerHTML = showStepsCompleted()
  return
}

function showStepsPending() {
  var content = ''
  var written = getStepsWritten()
  if (written == null){
    return content
  }
  var length = written.length
  for (var i = 0; i < length; i++) {
    if (written[i].complete !== true) {
      content += stepTemplate(written[i])
    }
  }
  return content
}
function showStepsCompleted() {
  var content = '<hr><h3>Steps Completed</h3><ul>'
  var written = getStepsWritten()
  if (written == null){
    return content
  }
  var length = written.length
  for (var i = 0; i < length; i++) {
    if (written[i].complete == true) {
      content += '<li>' + written[i].text + '</li>'
    }
  }
  content += '</ul>'
  return content
}

function showStepNew() {
  document.getElementById('add-new-step-button').classList = 'hidden'
  return stepTemplate(null)
}
function showNewStepButton() {
  document.getElementById('add-new-step-button').classList.remove('hidden')
}

function getStepsWritten() {
  var stored = localStorage.getItem('cojournersStepsWritten')
  if (stored != null){
    return JSON.parse(stored)
  }
  return null
}

function getStepWritten(id) {
  var empty = []
  var stored = localStorage.getItem('cojournersStepsWritten', empty)
  var written = JSON.parse(stored)
  if (written) {
    var length = written.length
    for (var i = 0; i < length; i++) {
      if (written[i].id == id) {
        console.log(written[i])
        return written[i]
      }
    }
  }
  return empty
}
function saveStepWritten(id) {
  nextStepsChangeHeight(id)
  considerShowNewStepButton()
  let typed = {
    id: id,
    text: document.getElementById('next-step-text' + id).value,
    complete: document.getElementById('next-step-complete' + id).checked,
  }
  let found = false
  let storing = []
  let stored = getStepsWritten()
  if (stored !== null){
    for (var i = 0; i < stored.length; i++) {
      if (stored[i].id == id) {
        storing[i] = typed
        found = true
      } else {
        storing[i] = stored[i]
      }
    }
  }
  if (found == false) {
    storing.push(typed)
  }
  localStorage.setItem('cojournersStepsWritten', JSON.stringify(storing))
}
function deleteStepWritten(id) {
  let stored = getStepsWritten()
  if (stored !== null){
    for (var i = 0; i < stored.length; i++) {
      if (stored[i].id == id) {
        stored.splice(i,1)
      } 
    }
  }
  localStorage.setItem('cojournersStepsWritten', JSON.stringify(stored))
  generateNextSteps()
}

function considerShowNewStepButton() {
  var ShowNewStepButton = true
  var col = document.getElementsByClassName('next-steps')
  for (var i = 0; i < col.length; i++) {
    var step = col[i].value
    if (step.length < 1){
      ShowNewStepButton = false
    }
  }
  if (ShowNewStepButton){
    showNewStepButton()
  }
  return
}
function considerShowNewStep() {
  var ShowNewStep = true
  var col = document.getElementsByClassName('next-steps')
  console.log(col)
  console.log(col.length)
  for (var i = 0; i < col.length; i++) {
    console.log(col[i])
    console.log(col[i].value)
    var step = col[i].value
    console.log ('length' + step.length)
    if (step.length < 1){
      ShowNewStep = false
    }
  }
  if (ShowNewStep){
     return showStepNew()
  }
  return null
}

// Dealing with Textarea Height
function nextStepsChangeHeight(id) {
  var value = document.getElementById('next-step-text' + id).value
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
  //console.log(newHeight)
  document.getElementById('next-step-text' + id).style.height = newHeight
}
function showStepWritten(id) {
  let step = getStepWritten(id)
  if (step) {
    var complete = 0
    if (step.complete) {
      complete = 1
    }
    if (step.text) {
      document.getElementById('next-step-title' + id).innerHTML = step.title
      document.getElementById('next-step-text' + id).innerHTML = step.text
      document.getElementById('next-step-complete' + id).checked = complete
    }
  }
}

function stepTemplate(written) {
  console.log(written)
  let template = ` <div class="next-step-area" id="step#">
  <form id="next-step#">
		<textarea id="next-step-text#"
                 class="next-steps"
                 placeholder="I will ___ by ____(when)"
                 rows="3"
                 onkeyup="saveStepWritten(#)">{written}</textarea>

	<div class="action-progress">
	<div><input id="next-step-complete#" type="checkbox" {checked} onclick="saveStepWritten(#)" /> <label> Finished</label></div>
  <div><input id="next-step-delete#" type="checkbox" {checked} onclick="deleteStepWritten(#)" /> <label> Delete</label></div>

	<div><button onclick="shareStep(#)">Share</button></div>

	</div>
	</form>
	</div>`
  var id = 0
  var text = ''
  var checked = 'unchecked'
  if (written !== null) {
    id = written.id
    if (typeof written.text !== 'undefined') {
      text = written.text
    }
    if (written.complete == true) {
      checked = 'checked'
    }
  }
  if (written == null) {
    id = nextStepsNextId()
  }
  let temp = template.replace(/#/g, id )
  var temp2 = temp.replace('{written}', text)
  template = temp2.replace('{checked}', checked)
  return template
}
function nextStepsNextId(){
  var id = 0
  var stored = localStorage.getItem('cojournersStepsWritten')
  if (stored == null){
    return id
  }
  var written = JSON.parse(stored)
  for (var i = 0; i < written.length; i++) {
    if (written[i].id > id) {
      id = written[i].id
    }
  }
  id++
  console.log('next id is ' + id)
  return id
}
function shareStep(id) {
  let action = getStepWritten(id)
  console.log(action)
  let myText = 'My next step is: ' + action.text
  if (action.complete == true) {
    myText = 'I have completed by next step: ' + action.text
  }
  if ('share' in navigator) {
    navigator.share({
      title: 'Next Step for ' + action.title,
      text: myText,
    })
  } else {
    console.log('share is not in navigator')
  }
}


function getPartner() {
  var partner = null
  let stored = localStorage.getItem('cojournersPartner', null)
  if (stored) {
    partner = JSON.parse(stored)
  }
  return partner
}
function showPartner() {
  let stored = localStorage.getItem('cojournersPartner', null)
  if (stored) {
    let partner = JSON.parse(stored)
    document.getElementById('partner-name').value = partner.name
    document.getElementById('partner-email').value = partner.email
    document.getElementById('partner-phone').value = partner.phone
  }
}
function savePartner() {
  let partner = {
    name: document.getElementById('partner-name').value,
    email: document.getElementById('partner-email').value,
    phone: document.getElementById('partner-phone').value,
  }
  localStorage.setItem('cojournersPartner', JSON.stringify(partner))
}