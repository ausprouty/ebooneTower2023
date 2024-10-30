<template>
  <div class="preview" v-bind:dir="rldir">
    <link rel="stylesheet" v-bind:href="style" />
    <NavBar called_by="library" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">There was an error... {{ error }}</div>
    <div class="content" v-if="loaded">
      <div v-if="publish">
        <button class="button" @click="localPublish('website')">
          {{ publish_text }}
        </button>
      </div>

      <div v-if="this.prototype">
        <div>
          <button class="button" @click="localPublish('prototype')">
            {{ prototype_text }}
          </button>
        </div>
        <div v-if="sdcard">
          <div>
            <button class="button" @click="localPublish('sdcard')">
              {{ sdcard_text }}
            </button>
          </div>
          <div>
            <button class="button" @click="localPublish('nojs')">
              {{ nojs_text }}
            </button>
          </div>
        </div>
      </div>
      <a
        target="_blank"
        class="help"
        v-bind:href="prototype_url + 'HD/eng/help-1/library_preview.html'"
      >
        <img class="help-icon" src="/sites/default/images/icons/help.png" />
      </a>
      <img
        @click="showPreview()"
        class="help-icon"
        src="/sites/default/images/icons/preview.png"
      />
      <hr class="border" />
      <a v-bind:href="this.back">
        <img v-bind:src="back_image" class="app-img-header" />
      </a>
      <div>
        <span v-html="pageText"></span>
      </div>

      <div v-if="!hide_cards">
        <Book
          v-for="book in bookmark.library.books"
          :key="book.title"
          :book="book"
        />
      </div>
      <div class="version">
        <p class="version">Version 2.05</p>
      </div>
      <hr class="border" />
    </div>
    <div v-if="write">
      <button class="button" @click="testLibrary">Test</button>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <button class="button" @click="editLibrary">Edit</button>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <button class="button" @click="sortLibrary">Sort</button>
    </div>
    <div v-if="readonly">
      <button class="button" @click="editLibrary">View Details</button>
    </div>
  </div>
</template>

<script>
import Book from '@/components/BookPreview.vue'
import NavBar from '@/components/NavBarAdmin.vue'
import LogService from '@/services/LogService.js'
import PrototypeService from '@/services/PrototypeService.js'
import PublishService from '@/services/PublishService.js'

import { mapState } from 'vuex'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { publishMixin } from '@/mixins/PublishMixin.js'
export default {
  mixins: [libraryGetMixin, authorizeMixin, publishMixin],
  props: ['country_code', 'language_iso', 'library_code'],
  computed: mapState(['bookmark', 'cssURL', 'standard', 'user']),
  components: {
    Book,
    NavBar,
  },

  data() {
    return {
      back: 'country',
      back_image: null,
      error: false,
      format: {},
      hide_cards: true,
      loaded: false,
      loading: false,
      nojs_text: 'Publish Library with No Javascript',
      pageText: null,
      prototype: false,
      prototype_text: 'Prototype Library',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish: false,
      publish_text: 'Publish Library',
      rldir: 'ltr',
      readonly: false,
      sdcard: false,
      sdcard_text: 'Publish Library for SDCard',
      site_directory: process.env.VUE_APP_SITE_DIR,
      style: null,
      write: false,
    }
  },
  methods: {
    editLibrary() {
      console.log(this.$route.params)
      this.$router.push({
        name: 'editLibrary',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
        },
      })
    },
    sortLibrary() {
      this.$router.push({
        name: 'sortLibrary',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
        },
      })
    },
    testLibrary() {
      this.$router.push({
        name: 'testComponent',
      })
    },
    goBack() {
      window.history.back()
    },
    showPreview() {
      var root = process.env.VUE_APP_PROTOTYPE_CONTENT_URL
      var page = this.$route.params.library_code
      if (this.$route.params.library_code == 'library') {
        page = 'index'
      }
      var link = ''
      var lin =
        root +
        this.$route.params.country_code +
        '/' +
        this.$route.params.language_iso +
        '/' +
        page
      if (page.includes('.html')) {
        link = lin
        console.log('page includes html')
      } else {
        link = lin + '.html'
        console.log('page DOES NOT include html')
      }
      //console.log('I want to go to ' + link)
      window.open(link, '_blank')
    },

    async localPublish(location) {
      var response = null
      var params = []
      params.recnum = this.recnum
      params.library_code = this.$route.params.library_code
      params.route = JSON.stringify(this.$route.params)
      if (location == 'prototype') {
        this.prototype_text = 'Prototyping'
        response = await PrototypeService.publish('library', params)
        this.prototype_text = 'Finished Prototyping'
      }
      if (location == 'website') {
        this.publish_text = 'Publishing'
        response = await PublishService.publish('library', params)
        this.publish_text = 'Finished Publishing'
      }
      if (!response['error']) {
        //  this.UnsetBookmarks()
        this.recnum = null
        this.loaded = false
        this.loading = true
        this.publish = false
        await this.loadView()
      }
      if (response['error']) {
        this.error = response['message']
        this.loaded = false
        if (location == 'website') {
          this.publish_text = 'Error Publishing'
        }
        if (location == 'prototype') {
          this.prototype_text = 'Error Prototyping'
        }
        if (location == 'sdcard') {
          this.sdcard_text = 'Error Publishing for SDCard'
        }
      }
    },
    async localBookmark(recnum) {
      var param = {}
      param.recnum = recnum
      param.library_code = this.$route.params.library_code
      var bm = await PrototypeService.publish('bookmark', param)
      LogService.consoleLogMessage('localBookmark')
      LogService.consoleLogMessage(bm)
    },
    async loadView() {
      try {
        this.recnum = null
        await this.getLibraryBookmark()
        this.back = '/preview/languages/' + this.$route.params.country_code
        //todo: allow this to backtrack
        // this is only true if the library goes back to a custom library
        //console.log(this.$route.params)
        if (typeof this.$route.params.library_code == 'undefined') {
          this.$route.params.library_code = ''
        }
        if (this.$route.params.library_code != 'library') {
          if (typeof this.bookmark.country.custom !== 'undefined') {
            this.back =
              '/preview/libraryIndex/' +
              this.$route.params.country_code +
              '/' +
              this.$route.params.language_iso
          } else {
            this.back =
              '/preview/library' +
              this.$route.params.country_code +
              '/' +
              this.$route.params.language_iso
          }
        }
        if (typeof this.bookmark.library.format.image !== 'undefined') {
          this.back_image = this.bookmark.library.format.image.image
        }
        this.style = this.bookmark.library.format.style
        this.hide_cards = this.bookmark.library.format.custom
        this.pageText = this.bookmark.library.text
        this.readonly = this.authorize('readonly', this.$route.params)
        this.write = this.authorize('write', this.$route.params)

        // authorize for prototype and publish
        this.prototype = false
        this.publish = false
        if (this.recnum) {
          this.prototype = this.mayPrototypeLibrary()
          if (this.prototype) {
            if (!this.prototype_date) {
              this.prototype_text = 'Prototype Library'
            } else {
              this.prototype_text = 'Prototype Library Again'
            }
          }
          if (this.prototype_date) {
            this.publish = this.mayPublishLibrary()
            this.sdcard = this.publish
            if (this.publish) {
              if (this.publish_date) {
                this.publish_text = 'Publish Library Again'
              } else {
                this.publish_text = 'Publish Library'
              }
            }
          }
        }
        // end of Prototype and Publish
        this.loaded = true
        this.loading = false
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in LibraryPreview.vue:',
          error
        )
      }
    },
  },
  beforeCreate() {
    this.$route.params.version = 'latest'
  },
  async created() {
    await this.loadView()
  },
}
</script>
