<script>
import { useAddNote, useShowNotes} from "@/assets/javascript/notes.js"
import { useFindSummaries, useFindCollapsible, usePopUp} from "@/assets/javascript/revealText.js"
import { useGoToPageAndSetReturn, usePageGoBack } from "@/assets/javascript/travel.js"
import { useRevealVideo, useRevealAudio } from "@/assets/javascript/revealMedia.js"
import Footer from '@/components/FooterGlobal.vue'

export default {
  components: {
    Footer
  },

  methods:{
    addNote(){
      useAddNote(this.$route.name)
    },
    goToPageAndSetReturn(page){
      useGoToPageAndSetReturn(page)
    },
    pageGoBack(){
      usePageGoBack()
    },
    popUp(verse){
      usePopUp(verse)
    },
    vuePush(id){
      this.$router.push({
        name: id,
      })
    },
  },
  mounted() {
    useFindSummaries()
    useFindCollapsible()
    useRevealAudio()
    useRevealVideo()
    useShowNotes(this.$route.name)
  },
}
</script>
<template>
  <div>[html]</div>
  <Footer/>
</template>
