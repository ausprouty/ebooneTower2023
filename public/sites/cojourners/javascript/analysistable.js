async function analysisTableCreateTable() {
  var content = analysisTableFormHeader()
  var rows = analysisTableFormRowLabels()
  for (var i = 0; i < rows.length; i++) {
    if (rows[i].type == 'heading') {
      content += analysisTableRowHeader(rows[i])
    }
    if (rows[i].type == 'row') {
      content += await analysisTableRowData(rows[i])
    }
  }
  content += `</tbody>
</table>`
  return content
}

function analysisTableRowHeader(row) {
  var content =
    '<tr class="analysis-section" id="tableSection' + row.section + '">' + '\n'
  content += '<td colspan="8" >' + row.text + '</td>' + '\n'
  content += `</tr>` + '\n'
  return content
}

async function analysisTableRowData(row) {
  const template = `<td class="colC"><input id= "rR-C" name="rowR" type="radio" value="C" checked onclick="updateTable(R,C);" /></td>`
  var chosen = await analysisTableGetValueForRow(row.row)
  var content = '<tr>' + '\n'
  content +=
    '<td id="row' +
    row.row +
    '" class= "col' +
    chosen +
    '">' +
    row.text +
    '</td>' +
    '\n'

  for (var i = 1; i < 8; i++) {
    var temp = template.replace(/C/g, i)
    if (chosen != i) {
      var temp2 = temp.replace('checked', '')
      temp = temp2
    }
    content += temp.replace(/R/g, row.row)
  }
  content += '</tr>' + '\n'
  return content
}
async function analysisTableGetValueForRow(row) {
  var value = 0
  if (localStorage.getItem('evangelisticMovementAnalysis')) {
    var storage = localStorage.getItem('evangelisticMovementAnalysis')
    var data = JSON.parse(storage)
    for (var i = 0; i < data.length; i++) {
      if (data[i].row == row) {
        value = data[i].value
      }
    }
  } else {
    //console.log('I am saving note in database ' + key)
    let db = new Localbase('db')
    await db.collection('analysisTable').doc(row).get({
      value: note,
    })
  }
  return value
}

function analysisTableUpdateTable(row, newValue) {
  let selected = {
    row: row,
    value: newValue,
  }
  var found = false
  var stored = []
  var storage = localStorage.getItem('evangelisticMovementAnalysis')
  if (storage) {
    stored = JSON.parse(storage)
    if (stored !== null) {
      for (var i = 0; i < stored.length; i++) {
        if (stored[i].row == row) {
          stored[i].value = newValue

          found = true
        }
      }
    }
  }
  if (found == false) {
    stored.push(selected)
  }
  localStorage.setItem('evangelisticMovementAnalysis', JSON.stringify(stored))
  document.getElementById('row' + row).className = 'col' + newValue
}

async function getValuesForAllRows() {
  var notes = null
  let db = new Localbase('db')
  await db
    .collection('analysisTable')
    .get()
    .then((notes) => {
      console.log(notes)
    })
  return notes
}

async function analysisTableSaveRow(row, data) {
  //console.log('I am saving note in database ' + key)
  let db = new Localbase('db')
  db.collection('analysisTable').doc().set({
    value: notes,
  })
}

function analysisTableGetCanvas() {
  form.width(a4[0] * 1.33333 - 80).css('max-width', 'none')
  return html2canvas(form, {
    imageTimeout: 2000,
    removeContainer: true,
  })
}

function analysisTableShare() {
  document.getElementById('pdfTableDiv').innerHTML = analysisTableMakePdfTable()
  let pdf = new jsPDF('p', 'pt', 'letter')
  let srcwidth = document.getElementById('pdfTableDiv').scrollWidth
  pdf.html(document.getElementById('pdfTableDiv'), {
    html2canvas: {
      scale: 595.26 / srcwidth,
      scrollY: 0,
    },
    x: 5,
    y: 5,
    callback: function (pdf) {
      pdf.save('Outcomes-based Analysis.pdf')
      document.getElementById('pdfTableDiv').innerHTML = ''
    },
  })
}

async function analysisTablePdfRowData(row) {
  const labels = [
    'Unknown',
    'Absent',
    'Very Weak',
    'Somewhat Week',
    'Minimally Acceptable',
    'Somewhat Strong',
    'Very Strong',
    'Fully Developed',
  ]
  var chosen = await analysisTableGetValueForRow(row.row)
  var content = '<tr>' + '\n'
  content += '<td class= "col' + chosen + '">' + row.text + '</td>' + '\n'
  content += '<td class= "col' + chosen + '">' + labels[chosen] + '</td>' + '\n'
  content += '</tr>' + '\n'
  return content
}
function analysisTablecreatePDF() {
  getCanvas().then(function (canvas) {
    var img = canvas.toDataURL('image/png'),
      doc = new jsPDF({
        unit: 'px',
        format: 'a4',
      })
    doc.addImage(img, 'JPEG', 20, 20)
    doc.save('htmlTOpdf.pdf')
    form.width(cache_width)
  })
}

function analysisTablePdfRowHeader(row) {
  var content =
    '<tr class="analysis-section" id="pdfTableSection' +
    row.section +
    '">' +
    '\n'
  content += '<td colspan="2" >' + row.text + '</td>' + '\n'
  content += `</tr>` + '\n'
  return content
}
function analysisTablePDFFormat() {
  var today = new Date()
  var dd = String(today.getDate()).padStart(2, '0')
  var mm = String(today.getMonth() + 1).padStart(2, '0')
  var yyyy = today.getFullYear()
  var dateTaken = mm + '/' + dd + '/' + yyyy
  var content = '<div id= "pdfTableDiv">'
  content +=
    '<h5>An Outcomes-based Analysis ( ' +
    dateTaken +
    ') from cojourners.sent67.com/analysis</h5>'
  content += '<table id="pdfTable"><tbody>'
  var rows = analysisTableFormRowLabels()
  for (var i = 0; i < rows.length; i++) {
    if (rows[i].type == 'heading') {
      content += analysisTablePdfRowHeader(rows[i])
    }
    if (rows[i].type == 'row') {
      content += analysisTablePdfRowData(rows[i])
    }
  }
  content += `</tbody>
</table>
</div>`
  return content
}

function analysisTableFormHeader() {
  return `<table id="analysis-table">
 <tbody>
  <tr id="tableSection0">
   <td class="analysis-title">&nbsp;</td>
   <td class="col1 vertical">1 - Absent</td>
   <td class="col2 vertical">2 - Very Weak</td>
   <td class="col3 vertical">3 - Somewhat Weak</td>
   <td class="col4 vertical">4 - Minimally Acceptable</td>
   <td class="col5 vertical">5 - Somewhat Strong</td>
   <td class="col6 vertical">6 - Very Strong</td>
   <td class="col7 vertical">7 - Fully Developed Strength</td>
  </tr>`
}

function analysisTableFormRowLabels() {
  const data = [
    {
      type: 'heading',
      text: '1. Believers are experiencing the spiritual dynamics of witness',
      section: '1',
    },
    {
      type: 'row',
      row: '1',
      text: 'Intrinsically motivated by love for God and others',
    },
    {
      type: 'row',
      row: '2',
      text: 'Actively dependent on the power of the Spirit in sharing the gospel',
    },
    {
      type: 'row',
      row: '3',
      text: 'Intentionally participating in the Great Commission',
    },
    {
      type: 'row',
      row: '4',
      text: 'Commonly praying for the city and the lost',
    },
    {
      type: 'heading',
      text: '2. Gospel conversations are common',
      section: '2',
    },
    {
      type: 'row',
      row: '5',
      text: 'Witness is occurring as a way of life, as believers take advantage of unexpected opportunities and initiate within relationships. (Natural Mode)',
    },
    {
      type: 'row',
      row: '6',
      text: 'Gospel conversations are occurring as part of ministry outreach initiatives. (Ministry Mode)',
    },
    {
      type: 'row',
      row: '7',
      text: 'Believers are faithfully bringing others into the experience of gospel-centered community. (Body Mode)',
    },
    {
      type: 'heading',
      text: '3. Believers are growing in their ability to communicate the gospel with clarity and relevance',
      section: '3',
    },
    {
      type: 'row',
      row: '8',
      text: 'Believers are able to engage in significant conversations to discover the spiritual interest and experience of others (Explorer Role)',
    },
    {
      type: 'row',
      row: '9',
      text: 'Believers are able to communicate the gospel with clarity and relevance (Guide Role)',
    },
    {
      type: 'row',
      row: '10',
      text: 'Believers are able to constructively address the issues and obstacles in the spiritual journeys of others (Builder Role)',
    },
    {
      type: 'row',
      row: '11',
      text: 'Believers are able to encourage others along their spiritual journey (Mentor Role)',
    },
    {
      type: 'heading',
      text: '4. Leaders and laborers are working together in effective outreaches',
      section: '4',
    },
    {
      type: 'row',
      row: '12',
      text: 'Opportunities to hear and consider the gospel are being provided to others through regular ministry outreaches',
    },
    {
      type: 'row',
      row: '13',
      text: 'Ministry outreaches are effective in engaging individuals across a broad spectrum of spiritual interest and openness',
    },
    {
      type: 'row',
      row: '14',
      text: 'Opportunities to hear, understand and respond to the gospel are provided through a variety of venues or initiatives (i.e., gospel on-line; gospel bearing gifts; gospel conversations; gospel in action; gospel groups)',
    },
    {
      type: 'heading',
      text: '5. Leaders and laborers are working together in effective outreaches',
      section: '5',
    },
    {
      type: 'row',
      row: '15',
      text: 'Our movement/community is made up of a healthy percentage of new believers',
    },
    {
      type: 'row',
      row: '16',
      text: 'We know who the new believers are and their level of interest and involvement',
    },
    {
      type: 'row',
      row: '17',
      text: 'We are intentional in establishing new believers in the foundational concepts of the Christian life',
    },
  ]
  return data
}
