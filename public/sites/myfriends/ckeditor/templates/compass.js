CKEDITOR.addTemplates('default', {
  imagesPath: CKEDITOR.getUrl(
    '/sites/myfriends/ckeditor/templates/images/compass'
  ),
  templates: [
    {
      title: 'Note Area',
      image: 'notes.png',
      description: 'Area for students to write NOTEs',
      html: '\x3cdiv class\x3d"note-area" id\x3d"note#"\x3e\x3cform id \x3d "note#"\x3eNotes: (click outside box to save)\x3cbr\x3e\x3ctextarea rows\x3d"5"\x3e\x3c/textarea\x3e\x3c/form\x3e\x3c/div\x3e',
    },
    {
      title: 'Answer',
      image: 'answer.png',
      description: 'Short Answer',
      html: '\x3cdiv class\x3d"note-area" id\x3d"note#"\x3e\x3cform id \x3d "note#"\x3e答:\x3cbr\x3e\x3ctextarea rows\x3d"1"\x3e\x3c/textarea\x3e\x3c/form\x3e\x3c/div\x3e',
    },
    {
      title: 'Reveal',
      image: 'reveal.png',
      description:
        'Students can reveal content by pressing on area. Best used for summaries',
      html: '\x3cdiv class\x3d"reveal"\x3e&nbsp;\x3chr\x3e Put summary or other text to reveal here \x3chr\x3e\x3c/div\x3e',
    },
    {
      title: 'Bible Passage',
      image: 'bible.png',
      description: 'Bible Passage Set',
      html: '\x3cdiv class\x3d"reveal bible"\x3e&nbsp;\x3chr\x3e[BiblePassage]\x3chr\x3e\x3c/div\x3e',
    },
    {
      title: 'Video',
      image: 'video.png',
      description: 'Video Block',
      html: '\x3chr /\x3e\x3c/div\x3e\x3cdiv class\x3d"reveal film"\x3e&nbsp;\x3chr /\x3e\x3ctable class\x3d"video" border\x3d"1"\x3e\x3ctbody  class\x3d"video"\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3e\x3cstrong\x3eTitle:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"video" \x3e\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3e\x3cstrong\x3eURL:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"video" \x3ehttps://api.arclight.org/videoPlayerUrl?refId\x3d\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video instruction"  colspan\x3d"2" style\x3d"text-align:center"\x3e\x3ch2\x3e\x3cstrong\x3eSet times if you do not want to play the entire video\x3c/strong\x3e\x3c/h2\x3e\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3eStart Time (seconds) :\x3c/td\x3e\x3ctd class\x3d"video" \x3estart\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3eEnd Time (seconds):\x3c/td\x3e\x3ctd class\x3d"video" \x3eend\x3c/td\x3e\x3c/tr\x3e\x3c/tbody\x3e\x3c/table\x3e\x3chr /\x3e\x3c/div\x3e',
    },
    {
      title: 'FLASHBACK',
      image: 'FlashBack.png',
      description: 'Image and Title',
      html: `\x3cdiv class\x3e"lesson"\x3e\x3cimg class\x3e"lesson-icon" src\x3e"/images/compass/sharing-life.png" /\x3e
\x3cdiv class\x3e"lesson-subtitle"\x3eFLASHBACK\x3c/div\x3e
\x3c/div\x3e`,
    },
    {
      title: 'BIBLE STUDY',
      image: 'BibleStudy.png',
      description: 'Image and Title',
      html: `\x3cdiv class\x3e"lesson"\x3e\x3cimg class\x3e"lesson-icon" src\x3e"/images/compass/bible-study.png" /\x3e
\x3cdiv class\x3e"lesson-subtitle"\x3eBIBLE STUDY\x3c/div\x3e
\x3c/div\x3e`,
    },
    {
      title: 'SPECIFIC QUESTIONS',
      image: 'SpecificQuestions.png',
      description: 'Image and Title',
      html: `\x3cdiv class\x3e"lesson"\x3e\x3cimg class\x3e"lesson-icon" src\x3e"/images/compass/challenges.png" /\x3e
\x3cdiv class\x3e"lesson-subtitle"\x3eSPECIFIC QUESTIONS &amp; ACTION STEPS\x3c/div\x3e
\x3c/div\x3e`,
    },
    {
      title: 'SCRIPTURE COMMENTS',
      image: 'ScriptureComments.png',
      description: 'Image and Title',
      html: `\x3cdiv class\x3e"lesson"\x3e\x3cimg class\x3e"lesson-icon" src\x3e"/images/compass/background.png" /\x3e
\x3cdiv class\x3e"lesson-subtitle"\x3eSCRIPTURE COMMENTS\x3c/div\x3e
\x3c/div\x3e`,
    },
  ],
})
