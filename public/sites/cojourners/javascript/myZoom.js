function zoomShow(id, image) {
  document.getElementById('pinch-zoom-image').src = image
  var div = document.getElementById('pinch-zoom-parent')
  div.classList.remove('offscreen')
  div.classList.add('overlay')
  document.getElementById('pinch-zoom-id').value = id
  var el = document.querySelector('div.pinch-zoom')
  new PinchZoom.default(el, {})
  document.getElementById('pinch-zoom' + id).classList.add('hidden')
}
function zoomClose() {
  var div = document.getElementById('pinch-zoom-parent')
  div.classList.add('offscreen')
  div.classList.remove('overlay')
  var id = document.getElementById('pinch-zoom-id').value
  document.getElementById('pinch-zoom' + id).classList.remove('hidden')
}

function zoomShowShort(img) {
  document.getElementById('pinch-zoom-image').src = img
  var div = document.getElementById('pinch-zoom-parent')
  div.classList.remove('offscreen')
  div.classList.add('overlay')
  var el = document.querySelector('div.pinch-zoom')
  new PinchZoom.default(el, {})
}

function zoomCloseShort() {
  var div = document.getElementById('pinch-zoom-parent')
  div.classList.add('offscreen')
  div.classList.remove('overlay')
}

function zoomCloseLong(id, img) {
  var html =
    '<div id = "pinch-zoom#id#" onclick="xzoomShow(\'#image#\', \'#id#\')">'
  html += '        <div id ="show-pinch-zoom#id#"></div>'
  html += '        <img id="pinch-zoom-image#id#"   src="#image#" />'
  html += '</div>'
  var temp = html.replace(/#id#/g, id)
  html = temp.replace(/#img#/g, img)
  console.log('zoomClose')
  console.log(html)
  document.getElementById('pinch-zoom' + id).innerHTML = html
  console.log(document.getElementById('pinch-zoom' + id))
  document.getElementById('pinch-zoom-parent').remove()
}
function zoomShowLong(img, id) {
  var html =
    '<div class="page pinch-zoom-parent overlay" id="pinch-zoom-parent">'
  html +=
    '   <div class="pinch-zoom-close" onclick="zoomClose(\'[id]\', \'#img#\')">'
  html +=
    '      <img class="close" src="/sites/cojourners/images/standard/close.png" />'
  html += '   </div>'
  html += '   <div class="pinch-zoom">'
  html += '       <img id="pinch-zoom-image" src="#img#"/>'
  html += '    </div>'
  html += '</div>'

  var temp = html.replace('[id]', id)
  html = temp.replace(/#img#/g, img)
  console.log('zoomShow')
  console.log(html)
  document.getElementById('pinch-zoom' + id).innerHTML = html
  var el = document.querySelector('div.pinch-zoom')
  new PinchZoom.default(el, {})
}
