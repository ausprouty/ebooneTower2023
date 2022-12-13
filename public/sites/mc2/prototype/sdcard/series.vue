<script>
import Footer from '@/components/FooterGlobal.vue'

export default {
  components: {
    Footer
  },
  methods:{
    vuePush(id){
      this.$router.push({
        name: id,
      })
    },
  },
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
