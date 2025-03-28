<template>
  <div class="preview">
    <NavBar called_by="country" />

    <div v-if="this.publish">
      <button class="button" @click="countryPublish('website')">
        {{ this.country_publish_text }}
      </button>
      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
      <button class="button" @click="languagePublish('website')">
        {{ this.language_publish_text }}
      </button>
    </div>
    <div v-if="this.prototype">
      <div>
        <button class="button" @click="countryPublish('prototype')">
          {{ this.country_prototype_text }}
        </button>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <button class="button" @click="languagePublish('prototype')">
          {{ this.language_prototype_text }}
        </button>
      </div>
      <div v-if="this.sdcard">
        <div>
          <button class="button" @click="languagePublish('sdcard')">
            {{ this.language_sdcard_text }}
          </button>
        </div>
      </div>
    </div>
    <h1>
      Select Country
      <a
        target="_blank"
        class="help"
        v-bind:href="this.prototype_url + 'HD/eng/help-1/countries_select.html'"
      >
        <img class="help-icon" src="/sites/default/images/icons/help.png" />
      </a>
      <img
        @click="showPreview('countries')"
        class="help-icon"
        src="/sites/default/images/icons/preview.png"
      />
      <img
        @click="showPreview('languages')"
        class="help-icon"
        src="/sites/default/images/icons/preview.png"
      />
    </h1>
    <Country
      v-for="country in countries"
      :key="country.code"
      :country="country"
    />
    <p class="version">Version 2.05</p>

    <div v-if="this.authorized">
      <button class="button" @click="editCountries">Edit</button>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <button class="button" @click="sortCountries">Sort</button>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import NavBar from '@/components/NavBarAdmin.vue'
import Country from '@/components/CountryPreview.vue'
import LogService from '@/services/LogService.js'
import PrototypeService from '@/services/PrototypeService.js'
import PublishService from '@/services/PublishService.js'
import { countriesMixin } from '@/mixins/CountriesMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { publishMixin } from '@/mixins/PublishMixin.js'

export default {
  mixins: [countriesMixin, authorizeMixin, publishMixin],
  components: {
    Country,
    NavBar,
  },
  data() {
    return {
      authorized: false,
      country_prototype_text: 'Prototype Countries',
      country_publish_text: 'Publish Countries',
      error: '',
      language_prototype_text: 'Prototype Languages',
      language_publish_text: 'Publish Languages',
      language_sdcard_text: 'Publish Languages to SD Card',
      loaded: false,
      loading: true,
      prototype: false,
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish: false,
      recnum: null,
      sdcard: false,
    }
  },
  computed: mapState(['user']),

  methods: {
    showPreview(destination) {
      var link = process.env.VUE_APP_PROTOTYPE_CONTENT_URL
      if (destination == 'countries') {
        link += 'index.html'
      } else {
        link += 'languages.html'
      }
      window.open(link, '_blank')
    },
    editCountries() {
      this.$router.push({
        name: 'editCountries',
      })
    },
    sortCountries() {
      this.$router.push({
        name: 'sortCountries',
      })
    },
    goBack() {
      window.history.back()
    },
    async countryPublish(location) {
      var response = null
      var params = {}
      params.recnum = this.recnum
      params.route = JSON.stringify(this.$route.params)
      if (location == 'prototype') {
        this.counry_prototype_text = 'Prototyping Countries'
        response = await PrototypeService.publish('countries', params)
        this.country_prototype_text = 'Prototyped Countries'
      }

      if (location == 'website') {
        this.country_publish_text = 'Publishing Countries'
        response = await PublishService.publish('countries', params)
        this.country_publish_text = 'Published Countries'
      }
      if (response['error']) {
        this.error = response['message']
        this.loaded = false
      } else {
        //this.UnsetBookmarks()
        this.recnum = null
        this.loaded = false
        this.loading = true
        this.publish = false
        await this.loadView()
      }
    },
    async languagePublish(location) {
      var response = null
      var params = {}
      params.recnum = this.recnum
      params.route = JSON.stringify(this.$route.params)
      if (location == 'prototype') {
        this.language_prototype_text = 'Prototyping Languages'
        response = await PrototypeService.publish('languagesAvailable', params)
        this.language_prototype_text = 'Prototyped Languages'
      }

      if (location == 'website') {
        this.language_publish_text = 'Publishing Languages'
        response = await PublishService.publish('languagesAvailable', params)
        this.language_publish_text = 'Published Languages'
      }
      if (response['error']) {
        this.error = response['message']
        this.loaded = false
      } else {
        //this.UnsetBookmarks()
        this.recnum = null
        this.loaded = false
        this.loading = true
        this.publish = false
        await this.loadView()
      }
    },
    async loadView() {
      try {
        await this.getCountries()
        this.authorized = this.authorize('write', this.$route.params)
        // console.log('my authorization value is  ' + this.authorized)
        // authorize for prototype and publish
        this.publish = false
        this.prototype = false
        if (this.recnum && !this.prototype_date) {
          this.prototype = this.mayPrototypeCountries
          if (this.prototype) {
            this.language_prototype_text = 'Prototype Languages'
            this.country_prototype_text = 'Prototype Countries'
          }
        }
        if (this.recnum && this.prototype_date) {
          LogService.consoleLogMessage('source','I am checking to see if I can publish')
          this.publish = this.mayPublishCountries
          if (this.publish) {
            LogService.consoleLogMessage('source','I can publish and prototype again')
            this.prototype = true
            this.language_prototype_text = 'Prototype Languages Again'
            this.country_prototype_text = 'Prototype Countrties Again'
            if (this.publish_date) {
              this.language_publish_text = 'Publish Languages Again'
              this.country_publish_text = 'Publish Countries Again'
            }
          }
        }
        console.log(' I have loaded view')
        // end authorization for prototype and publish
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in Countries.vue:',
          error
        )
      }
      console.log(' I have loaded ALL OF Countries View')
    },
  },
  beforeCreate() {
    this.$route.params.version = 'latest'
  },
  created() {
    this.loadView()
    console.log('end of created')
  },
}
</script>

<style></style>
