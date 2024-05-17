<template>
  <div>
    <NavBar called_by="library" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      Library Edit says was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <p>
          You have stumbled into a restricted page. Sorry I can not show it to
          you now
        </p>
        <p>
          You need to
          <a href="/login">login to make changes</a> here
        </p>
      </div>
      <div v-if="this.authorized">
        <h1>
          Library
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
        <br />
        <br />

       <!-- <LibraryFormatTemplate />
        <LibraryText v-bind:text="text" />-->
      </div>

      <h2>Books</h2>

       <LibraryBooks />
      <div>
        <button class="button" @click="prototypeAll">
          Select ALL to prototype
        </button>
        <button class="button" @click="publishAll">
          Select ALL to publish
        </button>

        <button class="button" @click="prototypeNone">
          Do NOT prototype ANY
        </button>
        <button class="button" @click="publishNone">Do NOT publish ANY</button>
      </div>

      <button class="button" @click="addNewBookForm">New Book</button>

      <button class="button red" @click="saveForm">Save Changes</button>

      <button class="button grey">Disabled</button>

      Please fill out the required field(s).
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
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'

export default {
  mixins: [
    authorizeMixin,
    libraryUpdateMixin,
    libraryGetMixin,
    libraryUploadMixin,
  ],
  components: {
    NavBar,
    LibraryFormatTemplate,
    LibraryText,
    LibraryBooks,
  },
  props: ['country_code', 'language_iso', 'library_code'],
  computed: mapState(['bookmark', 'cssURL', 'standard']),
  data() {
    return {
      authorized: false,
      error: null,
      error_message: null,
      images: false,
      image_permission: false,
      loaded: false,
      loading: false,
      prototype_date: null,
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish_date: null,
      text: null,
      recnum: null,
      // site_image_dir: process.env.VUE_APP_SITE_IMAGE_DIR,
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
  },
  async created() {
    await this.getBookmark()
    this.showForm()
    console.log ('at end of  Created of  Library Edit')
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
        LogService.consoleLogMessage('after loading is true')
      } catch (error) {
        LogService.consoleLogError('There was an error in Library.vue:', error) // Logs out the error
        this.error_message = error + 'Library Edit - showForm()'
        this.error = true
      }
    },
  },
}
</script>
<style scoped>
img.select {
  width: 100px;
}
</style>
