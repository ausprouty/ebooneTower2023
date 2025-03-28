<template>
  <div class="preview" v-bind:dir="this.rldir">
    <NavBar called_by="series" />
    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">There was an error...{{ this.error }}</div>
    <div class="content" v-if="loaded">
      <div v-if="this.publish">
        <button
          class="button"
          :class="{ warning: publishIncomplete }"
          @click="localPublish('website')"
        >
          {{ this.publish_text }}
        </button>
      </div>
      <div v-if="this.prototype">
        <div>
          <button
            class="button"
            :class="{ warning: prototypeIncomplete }"
            @click="localPublish('prototype')"
          >
            {{ this.prototype_text }}
          </button>
        </div>
        <div v-if="this.sdcard">
          <div>
            <button
              class="button"
              :class="{ warning: sdcardIncomplete }"
              @click="localPublish('sdcard')"
            >
              {{ this.sdcard_text }}
            </button>
          </div>
          <div>
            <button class="button" @click="localPublish('video_list')">
              {{ this.videolist_text }}
            </button>
          </div>
        </div>
        <div v-if="this.pdf">
          <div>
            <button class="button" @click="localPublish('pdf')">
              {{ this.pdf_text }}
            </button>
          </div>
        </div>
        <div>
          <button class="button" @click="localPublish('restore')">
            {{ this.restore_text }}
          </button>
        </div>
      </div>
      <div>
        <a
          target="_blank"
          class="help"
          v-bind:href="this.prototype_url + 'HD/eng/help-1/series_preview.html'"
        >
          <img class="help-icon" src="/sites/default/images/icons/help.png" />
        </a>
        <img
          @click="showPreview()"
          class="help-icon"
          src="/sites/default/images/icons/preview.png"
        />
        <link rel="stylesheet" v-bind:href="this.style" />

        <div class="app-series-header">
          <img
            v-on:click="returnToIndex()"
            v-bind:src="this.book_image"
            class="app-series-header"
          />
        </div>

        <div v-if="!bookmark.language.titles">
          <h2>{{ bookmark.book.title }}</h2>
        </div>
        <div v-if="this.description">{{ this.description }}</div>
        <br />
        <p id="offline-ready">{{ this.bookmark.language.download_ready }}</p>

        <br />

        <Chapter
          v-for="chapter in chapters"
          :key="chapter.id"
          :chapter="chapter"
        />
      </div>
      <div>
        <p class="button">
          <button id="offline-request" class="cache-series">
            {{ this.bookmark.language.download }}
          </button>
        </p>
      </div>
      <div class="version">
        <p class="version">Version 2.05</p>
      </div>
    </div>
    <hr />
    <div v-if="this.write">
      <button class="button" @click="editSeries">Edit</button>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <button class="button" @click="sortSeries">Sort</button>
    </div>
    <div v-if="this.readonly">
      <button class="button" @click="editSeries">View Details</button>
    </div>
    <br />
    <br />
    <br />
  </div>
</template>

<script>
import { mapState } from 'vuex'
import Chapter from '@/components/ChapterPreview.vue'
import LogService from '@/services/LogService.js'
import PrototypeService from '@/services/PrototypeService.js'
import PublishService from '@/services/PublishService.js'
import AuthorService from '@/services/AuthorService.js'
import SDCardService from '@/services/SDCardService.js'
import NavBar from '@/components/NavBarAdmin.vue'

import { seriesMixin } from '@/mixins/SeriesMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { publishMixin } from '@/mixins/PublishMixin.js'
export default {
  mixins: [seriesMixin, authorizeMixin, publishMixin],
  props: ['country_code', 'language_iso', 'library_code', 'folder_name'],
  computed: mapState(['bookmark', 'user']),
  components: {
    Chapter,
    NavBar,
  },
  data() {
    return {
      book_image: null,
      description: '',
      download_now: '',
      download_ready: '',
      error: '',
      loaded: false,
      loading: false,
      pdf: false,
      pdf_text: 'Publish Series and Chapters to PDF',
      prototype: false,
      prototypeIncomplete: false,
      prototype_date: '',
      prototype_text: 'Prototype Series and Chapters',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish: false,
      publishIncomplete: false,
      publish_date: '',
      publish_text: 'Publish Series and Chapters',
      readonly: false,
      recnum: null,
      restore_text: 'Restore Series and Chapters from Website',
      sdcard: false,
      sdcardIncomplete: false,
      sdcard_text: 'Publish Series and Chapters to SD Card',
      series_image_dir: '',
      style: '',
      videolist_text: null,
      write: false,
    }
  },

  methods: {
    editSeries() {
      this.$router.push({
        name: 'editSeries',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
          folder_name: this.$route.params.folder_name,
        },
      })
    },
    sortSeries() {
      this.$router.push({
        name: 'sortSeries',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
          folder_name: this.$route.params.folder_name,
        },
      })
    },
    goBack() {
      window.history.back()
    },
    showPreview() {
      var root = process.env.VUE_APP_PROTOTYPE_CONTENT_URL
      var link =
        root +
        this.$route.params.country_code +
        '/' +
        this.$route.params.language_iso +
        '/' +
        this.$route.params.folder_name +
        '/index.html'
      window.open(link, '_blank')
    },
    returnToIndex() {
      this.$router.push({
        name: 'previewLibrary',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
        },
      })
    },
    async localPublish(location) {
      var response = null
      var params = []
      params.recnum = this.recnum
      params.route = JSON.stringify(this.$route.params)
      if (location == 'prototype') {
        this.prototype_text = 'Prototyping'
        params.resume = this.prototypeIncomplete
        response = await PrototypeService.publish('seriesAndChapters', params)
        this.prototype_text = 'Prototyped'
      }
      if (location == 'website') {
        this.publish_text = 'Publishing'
        params.resume = this.prototypeIncomplete
        response = await PublishService.publish('seriesAndChapters', params)
        this.publish_text = 'Published'
      }
      if (location == 'restore') {
        this.restore_text = 'Restoring'
        response = await PublishService.restore('chapters', params)
        this.restore_text = 'Restored'
      }
      if (location == 'sdcard') {
        console.log('location is sdcard')
        this.sdcard_text = 'Publishing'
        params.resume = this.sdcardIncomplete
        response = await SDCardService.publish('seriesAndChapters', params)
        console.log('finsihed publishing to  sdcard')
        this.sdcard_text = 'Published to SD Card'
      }
      if (location == 'video_list') {
        this.videolist_text = 'Publishing'
        params.resume = this.prototypeIncomplete
        response = await PublishService.publish('seriesAndChapters', params)
        this.publish_text = 'Published'
      }
      if (response['error']) {
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
    async checkPublish() {
      if (this.prototype_date) {
        this.publish = this.mayPublishSeries()
        if (this.publish) {
          this.sdcard = true
          if (this.publish_date) {
            this.publish_text = 'Publish Series and Chapters Again'
          } else {
            this.publish_text = 'Publish Series and Chapters'
          }
          var params = {}
          params.route = JSON.stringify(this.$route.params)
          params.destination = 'website'
          var cache = await AuthorService.checkCache(params)
          console.log(cache)
          if (cache == 'website') {
            this.publishIncomplete = true
            this.publish_text = 'Resume Publishing Series and Chapters'
          }
        }
      }
    },
    async checkPrototype() {
      this.prototype = this.mayPrototypeSeries()
      if (this.prototype) {
        if (this.prototype_date) {
          this.prototype_text = 'Prototype Series and Chapters Again'
        } else {
          this.prototype_text = 'Prototype Series and Chapters'
        }
        var params = {}
        params.route = JSON.stringify(this.$route.params)
        params.destination = 'staging'
        var cache = await AuthorService.checkCache(params)
        console.log(cache)
        if (cache == 'staging') {
          this.prototypeIncomplete = true
          this.prototype_text = 'Resume Prototyping Series and Chapters'
        }
      }
    },
    async checkSDCard() {
      if (this.prototype_date) {
        this.publish = this.mayPublishSeries()
        if (this.publish) {
          if (this.publish_date) {
            this.sdcard = true
            this.sdcard_text = 'Publish to SDCard'
            var params = {}
            params.route = JSON.stringify(this.$route.params)
            params.destination = 'sdcard'
            var cache = await AuthorService.checkCache(params)
            console.log(cache)
            if (cache == 'sdcard') {
              this.sdcardIncomplete = true
              this.sdcard_text = 'Resume Publishing to SD Card'
              this.videolist_text = null
            }
            this.videolist_text = 'Publish VideoList'
            this.pdf_text = 'Publish PDF'
          }
        }
      }
    },
    async loadView() {
      try {
        LogService.consoleLogMessage('source',this.$route.params)
        await this.getSeries(this.$route.params)
        this.series_image_dir =
          this.$route.params.country_code +
          '/' +
          this.$route.params.language_iso +
          '/' +
          this.$route.params.folder_name
        this.readonly = this.authorize('readonly', this.$route.params)
        this.write = this.authorize('write', this.$route.params)
        this.prototype = false
        this.publish = false
        this.pdf = false
        if (this.recnum) {
          this.checkPrototype()
          this.checkPublish()
        }
        this.loaded = true
        this.loading = false
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in SeriesEdit.vUe:',
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
<style scoped>
.warning {
  background-color: red;
}
</style>
