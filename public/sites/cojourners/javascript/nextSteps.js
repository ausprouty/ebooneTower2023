async function generateNextSteps() {
  //I start here
  const div = document.getElementById('next-steps-area')
  var content = await showStepsPendingAndNew()
  div.innerHTML = content
  content = await showStepsCompleted()
  document.getElementById('next-steps-completed').innerHTML = content
  return
}
async function addNewStep() {
  const div = document.getElementById('next-steps-area')
  var content = await showStepsPendingAndNew()
  div.innerHTML = content
  hideNewStepButton()
  return
}

async function showStepsPendingAndNew() {
  var unwrittenPending = false
  var content = ''
  var written = await getStepsWritten()
  console.log(written)
  if (typeof written === 'undefined') {
    content += await stepTemplate(null)
    return content
  }
  var length = written.length
  for (var i = 0; i < length; i++) {
    if (written[i].complete !== true) {
      content += await stepTemplate(written[i])
    }
    if (written[i].text.length < 2) {
      unwrittenPending = true
    }
  }
  if (unwrittenPending == false) {
    content += await stepTemplate(null)
  }
  return content
}
async function showStepsCompleted() {
  var content = '<hr><h3>Steps Completed</h3><ul>'
  var written = await getStepsWritten()
  if (written == null) {
    console.log('no steps completed')
    return content
  }
  console.log('steps completed')
  var length = written.length
  for (var i = 0; i < length; i++) {
    if (written[i].complete == true) {
      content += '<li>' + written[i].text + '</li>'
    }
  }
  content += '</ul>'
  return content
}

function showNewStepButton() {
  document.getElementById('add-new-step-button').classList.remove('hidden')
}
function hideNewStepButton() {
  document.getElementById('add-new-step-button').classList.add('hidden')
}

async function getStepsWritten() {
  let db = new Localbase('db')
  let steps = await db.collection('nextsteps').get()
  console.log(steps)
  return steps
}
async function getStepWritten(stepId) {
  var written = null
  console.log('get step written for ' + stepId)
  let db = new Localbase('db')
  await db
    .collection('nextsteps')
    .get()
    .then((response) => {
      for (var i = 0; i < response.length; i++) {
        console.log(response[i].id)
        if (response[i].id == stepId) {
          written = response
        }
      }
    })
  console.log(written.id)
  console.log(written.text)
  return written
}

async function saveStepWritten(id) {
  nextStepsChangeHeight(id)
  considerShowNewStepButton()
  let db = new Localbase('db')
  db.collection('nextsteps')
    .doc(id)
    .set({
      id: id,
      text: document.getElementById('next-step-text' + id).value,
      complete: document.getElementById('next-step-complete' + id).checked,
    })
}
function deleteStepWritten(id) {
  let db = new Localbase('db')
  db.collection('nextsteps').doc(id).delete()
  var element = document.getElementById('step' + id)
  element.parentNode.removeChild(element)
}
function considerShowNewStepButton() {
  var ShowNewStepButton = true
  var col = document.getElementsByClassName('next-steps')
  for (var i = 0; i < col.length; i++) {
    var step = col[i].value
    if (step.length < 1) {
      ShowNewStepButton = false
    }
  }
  if (ShowNewStepButton) {
    showNewStepButton()
  }
  return
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
async function showStepWritten(id) {
  let step = await getStepWritten(id)
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

async function stepTemplate(written) {
  let template = ` <div class="next-step-area" id="step#">
  <form id="next-step#">
		<textarea id="next-step-text#"
                 class="next-steps"
                 placeholder="I will ___ by ____(when)"
                 rows="3"
                 onkeyup="saveStepWritten('#')">{written}</textarea>

	<div class="action-progress">
	<div><input id="next-step-complete#" type="checkbox" {checked} onclick="saveStepWritten('#')" /> <label> Finished</label></div>
  <div><input id="next-step-delete#" type="checkbox" {checked} onclick="deleteStepWritten('#')" /> <label> Delete</label></div>

	<div><button onclick="shareStep(#)">Share</button></div>

	</div>
	</form>
	</div>`
  var id = 0
  var text = ''
  var checked = 'unchecked'
  console.log(written)
  if (written !== null) {
    console.log('I am assigning next step id')
    id = written.id
    if (typeof written.text !== 'undefined') {
      text = written.text
    }
    if (written.complete == true) {
      checked = 'checked'
    }
  }
  if (written == null) {
    console.log('I am going to get next step id')
    id = await getNextStepNextId()
    console.log(id)
  }
  console.log('I have finished with step ID of ' + id)
  let temp = template.replace(/#/g, id)
  var temp2 = temp.replace('{written}', text)
  template = temp2.replace('{checked}', checked)
  return template
}
async function getNextStepNextId() {
  var nextId = 1
  let db = new Localbase('db')
  var lastStep = await db
    .collection('nextsteps')
    .orderBy('id', 'desc')
    .limit(1)
    .get()
  console.log('Here is LastStep')
  console.log(lastStep[0])
  if (typeof lastStep[0].id !== 'undefined') {
    console.log(lastStep[0].id)
    nextId = Number(lastStep[0].id) + 1
  }
  console.log('I finished getNextStepNextId with ' + nextId)
  return nextId
}

async function shareStep(id) {
  console.log('share Step ' + id)
  let action = await getStepWritten(id)
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

async function showPartner() {
  let db = new Localbase('db')
  await db
    .collection('settings')
    .doc('partner')
    .get()
    .then((partner) => {
      if (partner != null) {
        document.getElementById('partner-name').value = partner.name
        document.getElementById('partner-email').value = partner.email
        document.getElementById('partner-phone').value = partner.phone
      }
    })
  partnerListenForChange()
}

function partnerListenForChange() {
  var partnerDetails = document.getElementsByClassName('partner')
  for (var i = 0; i < partnerDetails.length; i++) {
    partnerDetails[i].addEventListener('keyup', updatePartner)
  }
}

async function updatePartner() {
  let db = new Localbase('db')
  db.collection('settings')
    .doc('partner')
    .set({
      name: document.getElementById('partner-name').value,
      email: document.getElementById('partner-email').value,
      phone: document.getElementById('partner-phone').value,
    })
}
