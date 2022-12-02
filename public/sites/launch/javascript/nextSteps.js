document.addEventListener('DOMContentLoaded', () => {
  var past_action = document.getElementById('previous-step')
  if (past_action !== null) {
    let id = document.getElementById('previous-step').value
    showStepWritten(id)
  }
  var present_action = document.getElementById('next-step')
  if (present_action !== null) {
    let id = document.getElementById('next-step').value
    showStepWritten(id)
  }
})

function showStepWritten(id) {
  let step = getStepWritten(id)
  if (step) {
    var complete = 0
    if (step.complete) {
      complete = 1
    }
    if (step.text) {
      document.getElementById('next-step-text' + id).innerHTML = step.text
      document.getElementById('next-step-complete' + id).checked = complete
    }
  }
}

function savePartner() {
  let partner = {
    name: document.getElementById('partner-name').value,
    email: document.getElementById('partner-email').value,
    phone: document.getElementById('partner-phone').value,
  }
  localStorage.setItem('launchPartner', JSON.stringify(partner))
}
function getPartner() {
  var partner = null
  let stored = localStorage.getItem('launchPartner', null)
  if (stored) {
    partner = JSON.parse(stored)
  }
  return partner
}
function showPartner() {
  let stored = localStorage.getItem('launchPartner', null)
  if (stored) {
    let partner = JSON.parse(stored)
    document.getElementById('partner-name').value = partner.name
    document.getElementById('partner-email').value = partner.email
    document.getElementById('partner-phone').value = partner.phone
  }
}

function generateNextSteps() {
  const div = document.getElementById('next-steps-area')
  var content = ''
  var titles = seriesTitles()
  var answer = null
  var written = getStepsWritten()
  var unwritten = 0
  var complete = false
  var length = titles.length
  for (var i = 0; i < length; i++) {
    answer = null
    complete = false
    if (written) {
      if (typeof written[i] !== 'undefined') {
        answer = written[i]
        if (written[i].text == '') {
          unwritten++
        }
        if (written[i].complete == true) {
          complete = true
        }
      } else {
        unwritten++
      }
    }
    if (unwritten < 2 && complete != true) {
      content += stepTemplate(titles[i], answer)
    }
  }
  div.innerHTML = content
}
function getSeriesTitle(id) {
  let titles = seriesTitles()
  let length = titles.length
  for (var i = 0; i < length; i++) {
    if (titles[i].id == id) {
      return titles[i]
    }
  }
  return null
}
function getStepsWritten() {
  var empty = []
  var stored = localStorage.getItem('launchStepsWritten', empty)
  var parsed = JSON.parse(stored)
  if (parsed == null) {
    return empty
  }
  return parsed
}

function getStepWritten(id) {
  var empty = []
  var stored = localStorage.getItem('launchStepsWritten', empty)
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
  let typed = {
    id: id,
    text: document.getElementById('next-step-text' + id).value,
    complete: document.getElementById('next-step-complete' + id).checked,
  }
  let found = false
  let storing = []
  let stored = getStepsWritten()

  for (var i = 0; i < stored.length; i++) {
    if (stored[i].id == id) {
      storing[i] = typed
      found = true
    } else {
      storing[i] = stored[i]
    }
  }

  if (found == false) {
    storing.push(typed)
  }
  localStorage.setItem('launchStepsWritten', JSON.stringify(storing))
  console.log('storeStepWritten')
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
  console.log(newHeight)
  document.getElementById('next-step-text' + id).style.height = newHeight
}

function stepTemplate(title, written) {
  console.log(written)
  let template = ` <div class="next-step-area" id="step#">
	<h3>#. {title}</h3>

	<form id="next-step#">
		<textarea id="next-step-text#"
                 class="next-steps"
                 placeholder="I will ___ by ____(when)"
                 rows="3"
                 onkeyup="saveStepWritten(#)">{written}</textarea>

	<div class="action-progress">
	<div><input id="next-step-complete#" type="checkbox" {checked} onclick="saveStepWritten(#)" /> <label> Finished</label></div>

	<div><button onclick="shareStep(#)">Share</button></div>

	</div>
	</form>
	</div>`
  let temp = template.replace(/#/g, title.id)
  let temp2 = temp.replace('{title}', title.title)
  var text = ''
  var checked = ''
  if (written !== null) {
    console.log(written.complete)
    if (typeof written.text !== 'undefined') {
      text = written.text
    }
    if (written.complete == true) {
      checked = 'checked'
    }
  }
  temp = temp2.replace('{written}', text)
  template = temp.replace('{checked}', checked)

  return template
}
function shareStep(id) {
  let step = getSeriesTitle(id)
  let action = getStepWritten(id)
  console.log(action)
  let myText = 'My next step is: ' + action.text
  if (action.complete == true) {
    myText = 'I have completed by next step: ' + action.text
  }
  if ('share' in navigator) {
    navigator.share({
      title: 'Next Step for ' + step.title,
      text: myText,
      url: step.url,
    })
  } else {
    console.log('share is not in navigator')
    //var body = message + ': ' + url
    // Here we use the WhatsApp API as fallback; remember to encode your text for URI
    //location.href = getMailtoUrl('', subject, body)
  }
}

function seriesTitles() {
  let titles = [
    {
      id: '1',
      title: 'The Mission of Jesus',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch01.html',
    },
    {
      id: '2',
      title: 'Keeping in Step with the Spirit',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch02.html',
    },
    {
      id: '3',
      title: 'Becoming a CoJourner',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch03.html',
    },
    {
      id: '4',
      title: 'God-Prepared People',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch04.html',
    },
    {
      id: '5',
      title: 'Sharing Your Personal Story',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch05.html',
    },
    {
      id: '6',
      title: 'Sharing the Gospel Personally',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch06.html',
    },
    {
      id: '7',
      title: 'Building Bridges',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch07.html',
    },
    {
      id: '8',
      title: 'The Disciple-Making Pathway',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch08.html',
    },
    {
      id: '9',
      title: 'The Art of Neighboring',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch09.html',
    },
    {
      id: '10',
      title: 'Celebration & Commissioning',
      url: 'https://launch.sent67.com/content/U1/eng/launch/launch10.html',
    },
  ]
  return titles
}
