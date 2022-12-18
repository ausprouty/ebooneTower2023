<script>
import { useShareLesson} from "@/assets/javascript/share.js"

export default {
  
  methods:{
    vuePush(id){
      this.$router.push({
        name: id,
      })
    },
    shareLesson(title, url){
      useShareLesson(title, url)
    }
  },
  mounted() {
    localStorage.setItem("returnpage", this.$route.path)
  }
}
</script>
<template>
  <!-- sdcard template from mc2 -->
  [[nav]]
  <div class="page_content" dir="{{ language.rldir }}">
    <div>
      {{ library.text }}
    </div>
    <div>
      [[books]]
    </div>
    <Footer/>
</div>
<!-- end default library -->
</template>
