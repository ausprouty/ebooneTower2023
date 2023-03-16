<script>
import {useShare} from "@/assets/javascript/share.js"

export default {
  
  methods:{
    vuePush(id){
      this.$router.push({
        name: id,
      })
    },
    share(what, v1, v2){
      useShare(what, v1, v2)
    }
  },
  mounted() {
    localStorage.setItem("returnpage", this.$route.name)
  }
}
</script>
<template>
  <!-- sdcard template from mc2 -->
  [[nav]]
  <div class="page_content" dir="{{ language.rldir }}">
    <div  class="app-series-header">
    <img src="@/assets/images/standard/header-front.png" class="app-series-header" />
  </div>
    <div>
      {{ library.text }}
    </div>
    <div>
      [[books]]
    </div>
    {{ footer }}
</div>
<!-- end default library -->
</template>
