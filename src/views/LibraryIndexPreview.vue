<template>
  <div class="preview">
    <NavBar text="pageText" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">There was an error... {{ this.error }}</div>
    <div class="content" v-if="loaded">
      <div v-if="this.publish">
        <button class="button" @click="localPublish('website')">
          {{ this.publish_text }}
        </button>
      </div>
      <div v-if="this.prototype">
        <button class="button" @click="localPublish('prototype')">
          {{ this.prototype_text }}
        </button>
      </div>
      <link rel="stylesheet" href="/sites/default/styles/appGLOBAL.css" />
      <link rel="stylesheet" v-bind:href="style" />
      <a
        target="_blank"
        class="help"
        v-bind:href="
          this.prototype_url + 'HD/eng/help-1/library_index_preview.html'
        "
      >
        <img class="help-icon" src="/sites/default/images/icons/help.png" />
      </a>
      <img
        @click="showPreview()"
        class="help-icon"
        src="/sites/default/images/icons/preview.png"
      />
      <hr class="border" />
      <span v-html="pageText"></span>
      <br />
      <span v-html="footerText"></span>
      <div class="version">
        <p class="language">Version 2.05</p>
      </div>
    </div>
    <hr class="border" />
    <div v-if="write">
      <button class="button" @click="editPage">Edit</button>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import LogService from '@/services/LogService.js'
import PrototypeService from '@/services/PrototypeService.js'
import PublishService from '@/services/PublishService.js'
import NavBar from '@/components/NavBarAdmin.vue'

import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { publishMixin } from '@/mixins/PublishMixin.js'
export default {
  mixins: [libraryUpdateMixin, authorizeMixin, publishMixin],
  props: ['country_code', 'language_iso'],
  components: {
    NavBar,
  },
  computed: mapState(['bookmark', 'cssURL', 'user']),
  data() {
    return {
      error: null,
      loaded: null,
      loading: false,
      prototype: false,
      prototype_text: 'Prototype',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish: false,
      publish_text: 'Publish',
      recnum: null,
      style: 'unknown',
      write: false,
    }
  },

  methods: {
    editPage() {
      this.$router.push({
        name: 'editLibraryIndex',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
        },
      })
    },
    /// preview/library/AU/eng/friends.html
    ///preview/series/AU/eng/family/youth-basics
    //  /preview/library/AU/eng/meet.html
    //  /preview/series/AU/eng/current/current
    // this does not seem to work
    vueJSRouter(link) {
      const linkArray = link.split('/')
      if (linkArray[1] == 'library') {
        this.$router.push({
          name: 'previewLibrary',
          params: {
            country_code: linkArray[2],
            language_iso: linkArray[3],
            library_code: linkArray[4],
          },
        })
      }
      if (linkArray[1] == 'series') {
        this.$router.push({
          name: 'previewSeries',
          params: {
            country_code: linkArray[2],
            language_iso: linkArray[3],
            library_code: linkArray[4],
            folder_name: linkArray[5],
          },
        })
      }
      if (linkArray[1] == 'page') {
        this.$router.push({
          name: 'previewPage',
          params: {
            country_code: linkArray[2],
            language_iso: linkArray[3],
            library_code: linkArray[4],
            folder_name: linkArray[5],
          },
        })
      }
    },
    goBack() {
      this.$router.push({
        name: 'previewCountries',
      })
    },
    showPreview() {
      var root = process.env.VUE_APP_PROTOTYPE_CONTENT_URL
      var link =
        root +
        this.$route.params.country_code +
        '/' +
        this.$route.params.language_iso +
        '/index.html'
      window.open(link, '_blank')
    },
    async localPublish(location) {
      if (location == 'website') {
        this.publish_text = 'Publishing'
      } else {
        this.prototype_text = 'Prototyping'
      }
      var params = {}
      var response = null
      params.recnum = this.recnum
      // params.bookmark = JSON.stringify(this.bookmark)
      params.route = JSON.stringify(this.$route.params)
      if (location == 'prototype') {
        response = await PrototypeService.publish('libraryIndex', params)
      } else {
        response = await PublishService.publish('libraryIndex', params)
      }
      if (typeof response['error'] != 'undefined') {
        this.error = response['message']
        this.loaded = false
      } else {
        //  this.UnsetBookmarks()
        this.recnum = null
        this.loaded = false
        this.loading = true
        this.publish = false
        await this.loadView()
      }
    },
    async localBookmark(recnum) {
      var param = {}
      param.recnum = recnum
      param.library_code = this.$route.params.library_code
      var bm = await PrototypeService.publish('bookmark', param)
      LogService.consoleLogMessage('source','localBookmark')
      LogService.consoleLogMessage('source',bm)
    },
    async loadView() {
      try {
        this.$route.params.css = '/sites/default/styles/freeformGLOBAL.css'

        await this.getLibraryIndex()
        if (this.recnum) {
          this.localBookmark(this.recnum)
        }
        this.write = this.authorize('write', this.$route.params)
        // authorize for prototype and publish
        this.publish = false
        this.prototype = false
        if (this.recnum && !this.prototype_date) {
          this.prototype = this.mayPrototypeLibrary()
          if (this.prototype) {
            this.prototype_text = 'Prototype'
          }
        }
        if (this.recnum && this.prototype_date) {
          this.publish = this.authorize('publish', this.$route.params)
          if (this.publish) {
            this.prototype = true
            this.prototype_text = 'Prototype Again'
            if (this.publish_date) {
              this.publish_text = 'Publish Again'
            }
          }
        }
        // end authorization for prototype and publish
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in LibraryIndexPreview.vue:',
          error
        )
      }
    },
  },
  beforeCreate() {
    this.$route.params.version = 'latest'
    this.$route.params.library_code = 'index'
  },
  async created() {
    this.loadView()
  },
}
</script>
