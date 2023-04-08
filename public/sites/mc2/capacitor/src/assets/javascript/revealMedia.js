import { Filesystem } from '@capacitor/filesystem';
import { Diagnostic } from '@awesome-cordova-plugins/diagnostic';
import { Capacitor } from '@capacitor/core';

export async function useRevealMedia() {

  await Diagnostic.isExternalStorageAuthorized().then(async (response)=>{
    console.log ('revealMedia is looking for external storage')
     if (response == true){
       console.log ('You are authorized to see External Storage')
      setupMediaListeners()
      findExternalStoragePath()
     }
     else{
      console.log ('You are NOT authorized to see External Storage')
      askExternalMediaAuthorization()
      hideMedia();;
     }
  }).catch(error=>{
      hideMedia()
      console.log("error - You can not check External Storage")
      console.log(error);
  });

  async function askExternalMediaAuthorization(){
    Diagnostic.requestExternalStorageAuthorization().then((data)=>{
      //your permission
      console.log(data);

      }).catch(error=>{
      console.log(error);
      });
  }

  function setupMediaListeners(){
    setupMediaListener( 'external-movie')
    setupMediaListener( 'external-audio')
  }
  function setupMediaListener(class_name){
    var coll = document.getElementsByClassName(class_name)
    var i
    for (i = 0; i < coll.length; i++) {
      coll[i].addEventListener('click', async function () {
        console.log ('revealing Media');
        this.classList.toggle('active')
        let id= this.id
        var content = this.nextElementSibling
        let filePath= localStorage.getItem('sd_filepath') + '/'+ id
        await Filesystem.stat({path: filePath })
          .then(async (response) =>{
            console.log('this file is readable')
            var url = localStorage.getItem('sd_url') + '/'+ id
            if (this.classList.contains('active') ){
              let template =`
                <video id="video[i]" width="100%" controls="controls" preload="metadata" autoplay="autoplay"
                    webkit-playsinline="webkit-playsinline" class="videoPlayer">
                    <source src="[url]" type="video/mp4">  `
              if (id.includes("/audio/")){
                template =`
                  <audio id="audio[i]" width="100%" controls >
                        <source src="[url]" type="audio/mpeg">
                        Your browser does not support the audio element.
                  </audio>
                  `
              }
              let temp = template.replace('[url]', url)
              let media = temp.replace('[i]', i)
              content.innerHTML = media
              console.log (media)
              content.classList.remove('collapsed')
            } else {
              content.classList.add('collapsed')
              content.innerHTML =''
            }
        }).catch(error=>{
            if (this.classList.contains('active') ){
              content.innerHTML = filePath + 'was not found on SD Card'
              content.classList.remove('collapsed')
            }
            else{
              content.classList.add('collapsed')
              content.innerHTML =''
            }
        });

      });
    }

  }


  function hideMedia(){
    console.log ('I am hiding media')
    var coll = document.getElementsByClassName('external-movie')
    var i
    for (i = 0; i < coll.length; i++) {
      coll[i].remove();
    }
    var coll = document.getElementsByClassName('external-audio')
    var i
    for (i = 0; i < coll.length; i++) {
      coll[i].remove();
    }
  }

  async function findExternalStoragePath(){
    await Diagnostic.getExternalSdCardDetails().then(async (response)=>{
      var sd_path =  response[0].filePath;
      localStorage.setItem('sd_filepath', sd_path)
      var sd_url= Capacitor.convertFileSrc(sd_path)
      localStorage.setItem('sd_url', sd_url)
      return sd_url
    }).catch(error=>{
      localStorage.setItem('sd_filepath', null)
      console.log("error getExternalSdCardDetails")
      console.log(error);
    });
  }


}
