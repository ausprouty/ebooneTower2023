<template>
  <div>
    <NavBar called_by="language" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">There was an error... {{ this.error }}</div>
    <div class="content" v-if="loaded">
      <img
        v-bind:src="process.env.VUE_APP_SITE_IMAGE_DIR + 'languages.jpg'"
        class="app-img-header"
      />

      <h1>Languages Available</h1>
      <Language
        v-for="language in languages"
        :key="language.iso"
        :language="language"
      />
    </div>
  </div>
</template>

<script>
import Language from '@/components/LanguageAvailable.vue'
import NavBar from '@/components/NavBarHamburger.vue'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import { mapState } from 'vuex'

import { languageMixin } from '@/mixins/LanguageMixin.js'
export default {
  mixins: [languageMixin],
  props: ['country_code'],
  components: {
    Language,
    NavBar,
  },
  computed: mapState(['bookmark']),
  data() {
    return {
      bookmark: '',
      error: null,
      language: {
        country_name: null,
        flag: null,
        folder: null,
        language_iso: null,
        language_name: null,
      },
      languages: [],
      loaded: null,
      loading: false,
      ZZ: false,
    }
  },
  beforeCreate() {
    this.$route.params.version = 'current'
  },
  async created() {
    try {
      var params = {}
      this.languages = await AuthorService.getLanguagesAvailable(params)
      LogService.consoleLogMessage('source',this.languages)
      this.loaded = true
      this.loading = false
    } catch (error) {
      LogService.consoleLogError(
        'There was an error in LanguagesEdit.vue:',
        error
      )
    }
  },
}
</script>

<style></style>
