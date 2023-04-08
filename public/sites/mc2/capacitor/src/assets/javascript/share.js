import { Share } from '@capacitor/share';

export async function useShare(what, v1, v2) {
    if (what == 'lesson'){
        shareLesson(v1, v2)
    }
    if (what == 'android'){
        let url= 'https://app.mc2.online/content/M2/' + v1 + '/pages/apk.html'
        window.open(url);
    }
    if (what == 'languages'){
       window.open("https://app.mc2.online/content/index.html");
    }
    if (what == 'website'){
       window.open(v1);
    }
}

async function shareLesson(lesson, url){
    let site_url = 'https://app.mc2.online' + url
    console.log(site_url)
    await Share.share({
    title: 'Here is the link to MC2',
    text: lesson,
    url: site_url,
    dialogTitle: 'Share this page',
    });
}
