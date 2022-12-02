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
