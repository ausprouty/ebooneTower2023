<template>
  <div :key="renderKey">
    <NavBar called_by="library" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      Library Edit says was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div v-if="this.authorized">
        <h1>
          Edit Library Books
          <a
            target="_blank"
            class="help"
            v-bind:href="this.prototype_url + 'HD/eng/help-1/library_edit.html'"
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>
        <div v-if="images">
          <img v-bind:src="this.header_image" class="header" />
          <br />
        </div>
        <div>
          <LibraryFormatTemplate />
          <LibraryText :value="libraryText" @input="libraryText = $event" />
          <LibraryBooks ref="libraryBooks" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBarAdmin.vue'
import LibraryFormatTemplate from '@/components/library/LibraryFormatTemplate.vue'
import LibraryText from '@/components/library/LibraryText.vue'
import LibraryBooks from '@/components/library/LibraryBooks.vue'

import LogService from '@/services/LogService.js'
// see https://stackoverflow.com/questions/55479380/adding-images-to-vue-select-dropdown
import '@/assets/css/vueSelect.css'

import { mapState } from 'vuex'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import {libraryGetMixin} from '@/mixins/library/LibraryGetMixin.js' // eslint-disable-line  

export default {
  mixins: [authorizeMixin, libraryGetMixin],
  components: {
    NavBar,
    LibraryBooks,
    LibraryFormatTemplate,
    LibraryText,
  },
  props: ['country_code', 'language_iso', 'library_code'],
  computed: {
    ...mapState(['bookmark', 'cssURL', 'standard']), // Existing Vuex state mappings

    // Add a computed property for libraryText with get and set
    libraryText: {
      get() {
        return this.$store.state.bookmark.library.text // Get libraryText from Vuex
      },
      set(newValue) {
        console.log('set libraryText to ' + newValue) // Alert the new value
        this.$store.commit('setLibraryText', newValue) // Commit new value to Vuex
      },
    },
  },
  data() {
    return {
      authorized: false,
      error: null,
      error_message: null,
      header_image: '',
      image_permission: false,
      images: false,
      languageDirectory: '',
      loaded: false,
      loading: false,
      prototype_date: null,
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish_date: null,
      recnum: null,
      renderKey: 0,
      site_dir: process.env.VUE_APP_SITE_DIR,
    }
  },
  beforeCreate() {
    // set directory for custom images
    //see https://ckeditor.com/docs/ckfinder/ckfinder3-php/integration.html
    this.languageDirectory =
      this.$route.params.country_code +
      '/' +
      this.$route.params.language_iso +
      '/images/custom'
    this.$route.params.version = 'latest'
    if (!this.$route.params.library_code) {
      this.$route.params.library_code = 'library'
    }
    this.$route.params.styles_set = 'default'
    this.$route.params.version = 'lastest'
    this.$route.params.filename = 'index'
    this.$route.params.css = '/sites/default/styles/freeformGLOBAL.css'
    console.log(this.$route.params)
  },
  async created() {
    await this.getBookmark()
    this.showForm()
    console.log('at end of  Created of  Library Edit')
  },
  methods: {
    async showForm() {
      try {
        this.error = null
        this.loaded = null
        this.loading = true
        this.publish_date = null
        this.recnum = null
        this.image_permission = this.authorize('write', this.$route.params)
        this.authorized = this.authorize('write', this.$route.params)
        this.loaded = true
        this.loading = false
        LogService.consoleLogMessage('source','after loading is true')
      } catch (error) {
        LogService.consoleLogError('There was an error in Library.vue:', error) // Logs out the error
        this.error_message = error + 'Library Edit - showForm()'
        this.error = true
      }
    },
    // Commit the new text to Vuex store
    XupdateLibraryTextInVuex(newText) {
      this.$store.commit('updateLibraryText', newText)
    },
  },
}
</script>
<style scoped>
img.select {
  width: 100px;
}
</style>
