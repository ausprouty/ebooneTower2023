<script>
import { useAddNote, useShowNotes} from "@/assets/javascript/notes.js";
import { useFindSummaries, useFindCollapsible, usePopUp } from "@/assets/javascript/app.js";
import { useRevealVideo } from "@/assets/javascript/revealVideo.js";
import Footer from '@/components/FooterGlobal.vue';

export default {
  components: {
    Footer
  },

  methods:{
    addNote(){
      useAddNote(this.$route.name)
    },
    popUp(verse){
      usePopUp(verse)
    }
  },
  mounted () {
    useShowNotes(this.$route.name)
    useFindSummaries()
    useFindCollapsible()
    useRevealVideo()
  }
}
</script>
<template>
    <div> [html]</div>
 
</template>
