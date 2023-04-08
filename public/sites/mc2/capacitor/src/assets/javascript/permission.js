import { Diagnostic } from '@awesome-cordova-plugins/diagnostic'


export async function useCheckPermissions() {
  console.log ('I am in useCheckPermissions')
  await Diagnostic.isExternalStorageAuthorized().then(async (response)=>{
    console.log ('revealMedia is looking for external storage')
     if (response == true){
       console.log ('You are authorized to see External Storage')
     }
     else{
      console.log ('You are NOT authorized to see External Storage')
      indexAskExternalMediaAuthorization()
     }
  }).catch(error=>{
      console.log("error - You can not check External Storage")
      console.log(error);
  });
}

async function indexAskExternalMediaAuthorization(){
    Diagnostic.requestExternalStorageAuthorization().then((data)=>{
      //your permission
      console.log(data);

      }).catch(error=>{
      console.log(error);
      });
  }