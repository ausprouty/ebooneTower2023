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
