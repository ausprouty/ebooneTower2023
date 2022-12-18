<script>


export default {
  methods:{
    pageGoBack(returnto){
      if (localStorage.getItem("returnpage")) {
        returnto = localStorage.getItem("returnpage");
        localStorage.removeItem("returnpage")
      }
      this.$router.push({
        name: returnto,
      })
    },
    vuePush(id){
      this.$router.push({
        name: id,
      })
    }
  },
  mounted() {
    localStorage.setItem("returnpage", this.$route.path)
  }
}
</script>
<template>
  <!-- sdcard template from mc2 -->

<div class="page_content" dir="{{ language.rldir }}">
  [[nav]]
  <div  class="app-series-header">
    <img src="{{ book.image }}" class="app-series-header" />
  </div>
  <div>
    {{ book.description }}
  </div>
  <br />
  <br />
  <!-- begin chapters -->
    [[chapters]]
   <!-- end chapters -->
  <div>
    <Footer/>>
  </div>
</div>
</template>
