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
        <LibraryFormatTemplate v-bind:format="library_format" />
        <LibraryText v-bind:text="text" />
      </div>

      <h2>Books</h2>

      <LibraryBook v-for="book in books" :key="book.id" :book="book" />
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
      <div v-if="!$v.$anyError">
        <button class="button red" @click="saveForm">Save Changes</button>
      </div>
      <div v-if="$v.$anyError">
        <button class="button grey">Disabled</button>
        <p v-if="$v.$anyError" class="errorMessage">
          Please fill out the required field(s).
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBarAdmin.vue'
import LibraryFormatTemplate from '@/components/library/LibraryFormatTemplate.vue'
import LibraryText from '@/components/library/LibraryText.vue'
import LibraryBook from '@/components/library/LibraryBook.vue'

import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'

// see https://stackoverflow.com/questions/55479380/adding-images-to-vue-select-dropdown
import '@/assets/css/vueSelect.css'

import { mapState } from 'vuex'
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { required } from 'vuelidate/lib/validators'
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
    LibraryBook,
  },
  props: ['country_code', 'language_iso', 'library_code'],
  computed: mapState(['bookmark', 'cssURL', 'standard']),
  data() {
    return {
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      // site_image_dir: process.env.VUE_APP_SITE_IMAGE_DIR,
      site_dir: process.env.VUE_APP_SITE_DIR,

      pages: ['one', 'many'],
      header_image: null,
      book_images: [],
      images: [],
      folders: [],

      bookcodes: [],
      authorized: false,
      image_permission: false,
      isHidden: true,
      text: 'What will you write?',
      image: 'withFriends.png',
      style_error: false,
      template_error: false,
    }
  },
  validations: {
    books: {
      required,
      $each: {
        id: { required },
        code: {},
        title: { required },
        style: {},
        styles_set: {},
        image: { required },
        format: { required },
        template: '',
        hide: '',
        password: '',
        prototype: '',
        publish: '',
      },
    },
  },
  methods: {
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
      this.library = []
      this.text = ''
      this.image = ''
      this.showForm()
    },

    async showForm() {
      try {
        this.error = null
        this.loaded = null
        this.loading = true
        this.publish_date = null
        this.recnum = null
        this.library = await this.getLibrary(this.$route.params)
        this.bookmark = await this.getBookmark()
        this.books = this.bookmark.library.books
        this.text = this.bookmark.library.text
        this.library_format = this.bookmark.library.format
        this.header_image = this.bookmark.library.format.image.image

        if (!this.image) {
          this.image = this.$route.params.library_code + '.png'
        }
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

  prototypeAll() {
    var arrayLength = this.books.length
    LogService.consoleLogMessage(' Item count:' + arrayLength)
    for (var i = 0; i < arrayLength; i++) {
      this.$v.books.$each.$iter[i].prototype.$model = true
    }
  },
  prototypeNone() {
    var arrayLength = this.books.length
    LogService.consoleLogMessage(' Item count:' + arrayLength)
    for (var i = 0; i < arrayLength; i++) {
      this.$v.books.$each.$iter[i].prototype.$model = false
    }
  },
  publishAll() {
    var arrayLength = this.books.length
    LogService.consoleLogMessage(' Item count:' + arrayLength)
    for (var i = 0; i < arrayLength; i++) {
      this.$v.books.$each.$iter[i].publish.$model = true
    }
  },
  publishNone() {
    var arrayLength = this.books.length
    LogService.consoleLogMessage(' Item count:' + arrayLength)
    for (var i = 0; i < arrayLength; i++) {
      this.$v.books.$each.$iter[i].publish.$model = false
    }
  },
  async revert() {
    var params = {}
    params.recnum = this.recnum
    params.route = JSON.stringify(this.$route.params)
    params.scope = 'library'
    var res = await AuthorService.revert(params)
    //console.log(res.content)
    this.seriesDetails = res.content.text
    this.recnum = res.content.recnum
  },

  async saveForm(action = null) {
    try {
      /* create index files
        var check = ''
        var params = {}
        var route = this.$route.params
        var arrayLength = this.books.length
        for (var i = 0; i < arrayLength; i++) {
          check = this.books[i]
          if (check.format == 'series') {
            route.folder_name = check.code
            route.filename = 'index'
            params.route = JSON.stringify(route)
            AuthorService.createSeriesIndex(params)
          }
        }
        */

      // update library file
      var output = {}
      output.books = this.books
      console.log(output.books)
      output.format = this.library_format
      output.text = this.text
      //console.log('see library')
      //console.log(output)
      var valid = ContentService.validate(output)
      this.content.text = JSON.stringify(valid)
      this.$route.params.filename = this.$route.params.library_code
      delete this.$route.params.folder_name
      this.content.route = JSON.stringify(this.$route.params)
      this.content.filetype = 'json'
      //this.$store.dispatch('newBookmark', 'clear')

      var response = await AuthorService.createContentData(this.content)
      //console.log(response)
      if (response.data.error != true && action != 'stay') {
        this.$router.push({
          name: 'previewLibrary',
          params: {
            country_code: this.$route.params.country_code,
            language_iso: this.$route.params.language_iso,
            library_code: this.$route.params.library_code,
          },
        })
      }
      if (response.data.error == true) {
        this.error = true
        this.loaded = false
        this.error_message = response.data.message
      }
    } catch (error) {
      LogService.consoleLogError(
        'LIBRARY EDIT There was an error in Saving Form ',
        error
      )
      this.loaded = false
      this.error_message = error
      this.error = true
    }
  },
}
</script>
<style scoped>
img.select {
  width: 100px;
}
</style>
