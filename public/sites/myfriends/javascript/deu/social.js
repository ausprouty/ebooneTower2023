function getMailtoUrl(to, subject, body) {
  var args = []
  if (typeof subject !== 'undefined') {
    args.push('subject=' + encodeURIComponent(subject))
  }
  if (typeof body !== 'undefined') {
    args.push('body=' + encodeURIComponent(body))
  }
  var url = 'mailto:' + encodeURIComponent(to)
  if (args.length > 0) {
    url += '?' + args.join('&')
  }
  return url
}


function languages() {
  location.href = '/content/languages.html'
}
function share() {
  var subject = 'MyFriends'
  var message = 'Hier ist der Link zu unserer Diskussion:'
  var url = location.href
  if ('share' in navigator) {
    navigator.share({
      title: subject,
      text: message,
      url: url,
    })
  } else {
    var body = message + ': ' + url
    // Here we use the WhatsApp API as fallback; remember to encode your text for URI
    //location.href = 'mailto:?body=' + encodeURIComponent(text + ' - ') + location.href + ';subject=' + encodeURIComponent(title);
    location.href = getMailtoUrl('', subject, body)
  }
}
