<template>
  <div>
    <NavBar called_by="CapacitorMaker" />
    <div v-if="!this.authorized">
      <BaseNotAuthorized />
    </div>
    <div v-if="this.authorized">
      <div>
        <h1>Capacitor for {{ this.country_name }}</h1>
        <p>
          This page allows you to create an Capacitor App which will have all
          the content and videos.
        </p>
        <p>For sensitive countries be sure to click "Remove External Links"</p>
        <p>
          You will find all content in {{ this.capacitor_root }}/{{
            this.capacitor.subDirectory
          }}
        </p>
      </div>
      <div>
        <h3>Language</h3>
        <v-select
          label="language_name"
          :options="language_data"
          placeholder="Select"
          v-model="$v.capacitor.language.$model"
          v-on:change="setCapacitorSubDir($v.capacitor.language.$model)"
        />
        <label for="remove_external_links">
          <h3>Remove External Links</h3>
        </label>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <input
          type="checkbox"
          id="remove_external_links"
          v-model="$v.capacitor.remove_external_links.$model"
        />
        <h3>Footer</h3>
        <v-select
          :options="footers"
          placeholder="Select"
          v-model="$v.capacitor.footer.$model"
        />
        <button
          class="button"
          v-if="!this.show_progress"
          @click="showProgress()"
        >
          Show Progress
        </button>
      </div>
      <div class="spacer"></div>
      <div class="row" v-if="this.show_progress">
        <div class="column">
          <h3>Common Files</h3>
          <button
            class="button"
            v-bind:class="progress.common_files.progress"
            @click="createCommonFiles()"
          >
            {{ this.common_text }}
          </button>
        </div>
      </div>
      <div class="row">
        <span v-html="progress.common_files.message"></span>
      </div>
      <div v-if="this.show_progress">
        <CapacitorBooks :language="$v.capacitor.language.$model" />
        <p>
          The MediaList Files. Media.bat files will be at mc2.media/batfiles
        </p>
        <p>After you make the Media List Bat files:</p>
        <ul>
          <li>Correct errors in html and republish</li>
          <li>Download any missing files</li>
          <li>Update videoReference.phpe</li>
          <li>
            Download, move to M:MC2/Media/M2/
            {{ this.$route.params.country_code }}, unzip and run the bat files -
            (They take too much processing time to run on the web.)
          </li>
          <li>Copy the media files to mc2.media/MC2/LANGUAGE_ISO</li>
          <li>Verify that all media files are in this directory</li>
        </ul>
        <p>
          Router will be found at {{ this.capacitor_root }}/{{
            this.capacitor.subDirectory
          }}/src/router
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import CapacitorBooks from '@/components/CapacitorBooks.vue'
import CapacitorService from '@/services/CapacitorService.js'
import AuthorService from '@/services/AuthorService.js'
import NavBar from '@/components/NavBarAdmin.vue'
import { required } from 'vuelidate/lib/validators'
import vSelect from 'vue-select'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
export default {
  mixins: [authorizeMixin],
  props: ['country_code'],
  components: {
    NavBar,
    CapacitorBooks,
    'v-select': vSelect,
  },
  data() {
    return {
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      capacitor_root: process.env.VUE_APP_ROOT_CAPACITOR,
      authorized: false,
      videolist_text: 'Create Media List for SD Card',
      common_text: 'Check Common Files',
      language_text: 'Create Language Index',
      languages: [],
      country_name: null,
      show_progress: false,
      site: process.env.VUE_APP_SITE,
      language_data: [],
      footers: [],
      bat_text: 'Download Media Batch Files',
      progress: {
        common_files: {
          progress: 'undone',
          message: null,
        },
      },
      capacitor: {
        language: null,
        footer: null,
        remove_external_links: false,
        action: 'capacitor',
        series: null,
        subDirectory: null,
      },
    }
  },
  computed: {
    bookmark() {
      return this.$store.state.bookmark
    },
  },
  validations: {
    capacitor: {
      language: { required },
      footer: { required },
      remove_external_links: { required },
      actions: { required },
      series: { required },
      subDirectory: {},
    },
  },
  methods: {
    async showProgress() {
      this.show_progress = true
      await this.verifyCommonFiles()
    },
    async verifyLanguageIndex() {
      this.language_text = 'Verifying'
      var params = this.$route.params
      var response = await CapacitorService.verifyLanguageIndex(params)
      console.log(response)
      this.language_text = 'Verified'
    },
    async verifyCommonFiles() {
      this.common_text = 'Verifying'
      var params = this.$route.params
      var response = await CapacitorService.verifyCommonFiles(params)
      console.log(response)
      this.progress = response
      this.common_text = 'Checked'
    },
    async createCommonFiles() {
      this.common_text = 'Creating'
      var params = this.$route.params
      var response = await CapacitorService.createCommonFiles(params)
      console.log(response)
      this.progress = response
      this.common_text = 'Created'
    },
    async zipMediaBatFiles() {
      this.bat_text = 'Downloading'
      var params = this.$route.params
      var response = await CapacitorService.zipMediaBatFiles(params)
      console.log(response)
      var filename = response
      this.bat_text = 'Finished'
      this.downloadMediaBatFiles(filename)
    },
    setCapacitorSubDir(selected_language) {
      this.capacitor.subDirectory = selected_language.language_iso + '/'
      this.$store.dispatch('setCapacitorSettings', this.capacitor)
    },
  },
  async created() {
    this.authorized = this.authorize('write', this.$route.params)
    if (this.authorized) {
      await AuthorService.bookmark(this.$route.params)
      this.country_name = this.bookmark.country.name
      this.language_data = await CapacitorService.getLanguages(
        this.$route.params
      )
      this.$store.dispatch('setLanguages', [this.language_data])
      var len = this.language_data.length
      for (var i = 0; i < len; i++) {
        this.languages[i + 1] = this.language_data[i].language_name
      }
      this.footers = await CapacitorService.getFooters(this.$route.params)
    }
  },
}
</script>
<style scoped>
div.spacer {
  height: 30px;
}
.undone {
  background-color: black;
  padding: 10px;
  color: white;
}
.error {
  background-color: red;
  padding: 10px;
  color: white;
}
.ready {
  background-color: yellow;
  padding: 10px;
  color: black;
}
.invisible {
  background-color: white;
  padding: 10px;
  color: white;
}

.done {
  background-color: green;
  padding: 10px;
  color: white;
}
</style>
