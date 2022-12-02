CKEDITOR.addTemplates('default', {
  imagesPath: CKEDITOR.getUrl('/sites/cojourners/ckeditor/templates/images/'),
  templates: [
    {
      title: 'Reveal',
      image: 'reveal.png',
      description:
        'Students can reveal content by pressing on area. Best used for summaries',
      html: '\x3cdiv class\x3d"reveal"\x3e&nbsp;\x3chr\x3e Put summary or other text to reveal here \x3chr\x3e\x3c/div\x3e',
    },
    {
      title: 'Note Area',
      image: 'notes.png',
      description: 'Area for students to write NOTES',
      html: '\x3cdiv class\x3d"note-area" id\x3d"note#"\x3e\x3cform id \x3d "note#"\x3e\x3ctextarea rows\x3d"5"\x3e\x3c/textarea\x3e\x3c/form\x3e\x3c/div\x3e',
    },
    {
      title: 'Previous Step',
      image: 'previousstep.png',
      description: 'Previous Step',
      html: '\x3cdiv class\x3d"previous-step-area"\x3e \x3ch3\x3ePrevious Goal\x3c/h3\x3e \x3cform\x3e\x3cinput id\x3d"previous-step" type\x3d"hidden" value\x3d"#" /\x3e\x3ctextarea class\x3d"next-steps" id\x3d"next-step-text#" onkeyup\x3d"saveStepWritten(#)" placeholder\x3d"My next step from the previous session" rows\x3d"3"\x3e\x3c/textarea\x3e \x3cdiv class\x3d"action-progress"\x3e \x3cdiv\x3e\x3cinput id\x3d"next-step-complete#" type\x3d"checkbox" onclick\x3d"saveStepWritten(#)" /\x3e \x3clabel\x3e Finished\x3c/label\x3e\x3c/div\x3e \x3cdiv\x3e\x3cbutton onclick\x3d"shareStep(#)"\x3eShare\x3c/button\x3e\x3c/div\x3e \x3c/div\x3e \x3c/form\x3e \x3c/div\x3e',
    },
    {
      title: 'Next Step',
      image: 'nextstep.png',
      description: 'Next Step',
      html: '\x3cdiv class\x3d"next-step-area"\x3e \x3ch3\x3eNext Step Goal\x3c/h3\x3e \x3cform\x3e\x3cinput id\x3d"next-step" type\x3d"hidden" value\x3d"#" /\x3e\x3ctextarea class\x3d"next-steps" id\x3d"next-step-text#" onkeyup\x3d"saveStepWritten(#)" placeholder\x3d"I will ___ by ____(when)" rows\x3d"3"\x3e\x3c/textarea\x3e \x3cdiv class\x3d"action-progress"\x3e \x3cdiv\x3e\x3cinput id\x3d"next-step-complete#" type\x3d"checkbox" onclick\x3d"saveStepWritten(#) /\x3e \x3clabel\x3e Finished\x3c/label\x3e\x3c/div\x3e \x3cdiv\x3e\x3cbutton onclick\x3d"shareStep(#)"\x3eShare\x3c/button\x3e\x3c/div\x3e \x3c/div\x3e \x3c/form\x3e \x3c/div\x3e',
    },

    {
      title: 'Trainer Notes',
      image: 'trainer.png',
      description: 'Only shown to trainers',
      html: '\x3cdiv class\x3d"trainer"\x3e&nbsp;\x3chr\x3ePut Trainer Notes Here \x3chr\x3e\x3c/div\x3e',
    },
    {
      title: 'Answer',
      image: 'answer.png',
      description: 'Short Answer',
      html: '\x3cdiv class\x3d"note-area" id\x3d"note#"\x3e\x3cform id \x3d "note#"\x3e答:\x3cbr\x3e\x3ctextarea rows\x3d"1"\x3e\x3c/textarea\x3e\x3c/form\x3e\x3c/div\x3e',
    },
    {
      title: 'Video',
      image: 'video.png',
      description: 'Video Block',
      html: '\x3chr /\x3e\x3c/div\x3e\x3cdiv class\x3d"reveal film"\x3e&nbsp;\x3chr /\x3e\x3ctable class\x3d"video" border\x3d"1"\x3e\x3ctbody  class\x3d"video"\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3e\x3cstrong\x3eTitle:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"video" \x3e\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3e\x3cstrong\x3eURL:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"video" \x3ehttps://api.arclight.org/videoPlayerUrl?refId\x3d\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video instruction"  colspan\x3d"2" style\x3d"text-align:center"\x3e\x3ch2\x3e\x3cstrong\x3eSet times if you do not want to play the entire video\x3c/strong\x3e\x3c/h2\x3e\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3eStart Time (seconds) :\x3c/td\x3e\x3ctd class\x3d"video" \x3estart\x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"video" \x3e\x3ctd class\x3d"video label" \x3eEnd Time (seconds):\x3c/td\x3e\x3ctd class\x3d"video" \x3eend\x3c/td\x3e\x3c/tr\x3e\x3c/tbody\x3e\x3c/table\x3e\x3chr /\x3e\x3c/div\x3e',
    },
    {
      title: 'Audio',
      image: 'audio.png',
      description: 'Audio Block',
      html: '\x3cdiv class\x3d"reveal audio"\x3e&nbsp;\x3chr /\x3e\x3ctable class\x3d"" border\x3d"1"\x3e\x3ctbodyclass\x3d"audio"\x3e\x3ctr class\x3d"audio" \x3e\x3ctd class\x3d"audio label" \x3e\x3cstrong\x3eTitle:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"audio" \x3e \x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"audio" \x3e\x3ctd class\x3d"audio label" \x3e\x3cstrong\x3eURL:\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"audio" \x3e \x3c/td\x3e\x3c/tr\x3e\x3ctr class\x3d"audio" \x3e\x3ctd class\x3d"audio label" \x3e\x3cstrong\x3eOptional Text\x3c/strong\x3e\x3c/td\x3e\x3ctd class\x3d"audio" \x3e \x3c/td\x3e\x3c/tr\x3e\x3c/tbody\x3e\x3c/table\x3e\x3chr /\x3e\x3c/div\x3e',
    },

    {
      title: 'Bible Passage',
      image: 'bible.png',
      description: 'Bible Passage Set',
      html: '\x3cdiv class\x3d"reveal bible"\x3e&nbsp;\x3chr\x3e[BiblePassage]\x3chr\x3e\x3c/div\x3e',
    },

    {
      title: 'Looking Back',
      image: 'back.png',
      description: 'Image and Title',
      html: '\x3cdiv class\x3d"lesson"\x3e\x3cimg class\x3d"lesson-icon" src\x3d"/sites/default/images/back.png" /\x3e\x3cdiv class\x3d"lesson-subtitle"\x3e\x3cspan class="back"\x3eLOOKING BACK\x3c/span\x3e\x3c/div\x3e\x3c/div\x3e',
    },
    {
      title: 'Looking Up',
      image: 'up.png',
      description: 'Image and Title',
      html: '\x3cdiv class\x3d"lesson"\x3e\x3cimg class\x3d"lesson-icon" src\x3d"/sites/default/images/up.png" /\x3e\x3cdiv class\x3d"lesson-subtitle"\x3e\x3cspan class="up"\x3eLOOKING UP\x3c/span\x3e\x3c/div\x3e\x3c/div\x3e',
    },
    {
      title: 'Looking Forward',
      image: 'forward.png',
      description: 'Image and Title',
      html: '\x3cdiv class\x3d"lesson"\x3e\x3cimg class\x3d"lesson-icon" src\x3d"/sites/default/images/forward.png" /\x3e\x3cdiv class\x3d"lesson-subtitle"\x3e\x3cspan class="forward"\x3eLOOKING FORWARD\x3c/span\x3e\x3c/div\x3e\x3c/div\x3e',
    },
    {
      title: 'Javascript Block',
      image: 'javascript.png',
      description: 'Javascript for this page only',
      html: '\x3cdiv class\x3d"javascript"\x3e&nbsp;\x3chr\x3e Put Javascript for this file only here \x3chr\x3e\x3c/div\x3e',
    },
  ],
})
