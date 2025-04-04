<template>
  <div>
    <NavBar called_by="language" />

    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      There was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div v-if="this.authorized">
        <h1>
          Languages for {{ this.$route.params.country_code }}
          <a
            target="_blank"
            class="help"
            v-bind:href="
              this.prototype_url + 'HD/eng/help-1/languages_sort.html'
            "
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>
        <div>
          <draggable v-model="languages">
            <transition-group>
              <div
                v-for="language in languages"
                :key="language.iso"
                :language="language"
              >
                <div class="shadow-card -shadow">
                  <img
                    src="/sites/default/images/icons/move2red.png"
                    class="sortable"
                  />
                  <span class="card-name">{{ language.name }}</span>
                </div>
              </div>
            </transition-group>
          </draggable>
        </div>
        <button class="button" @click="saveForm">Save</button>
      </div>
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBarAdmin.vue'
import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import draggable from 'vuedraggable'
import { mapState } from 'vuex'

import { languageMixin } from '@/mixins/LanguageMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
export default {
  mixins: [languageMixin, authorizeMixin],
  props: ['country_code'],
  components: {
    NavBar,
    draggable,
  },
  data() {
    return {
      authorized: false,
      bookmark: '',
      choose_language: '',
      content: {},
      error: false,
      error_message: '',
      image_dir: '',
      languages: [],
      loaded: false,
      loading: true,
      more_languages: '',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      rldir: 'ltr',
    }
  },
  computed: mapState(['bookmark']),

  methods: {
    addNewLanguageForm() {
      this.languages.push({
        id: '',
        folder: '',
        iso: '',
        name: '',
        image_dir: '',
        rldir: 'ltr',
      })
    },
    deleteLanguageForm(index) {
      this.languages.splice(index, 1)
    },
    async saveForm() {
      try {
        var output = {}
        output.languages = this.languages
        output.more_languages = this.more_languages
        output.choose_language = this.choose_language
        LogService.consoleLogMessage('source','output')
        LogService.consoleLogMessage('source',output)
        var valid = ContentService.validate(output)
        this.content.text = JSON.stringify(valid)
        this.$route.params.filename = 'languages'
        this.content.filetype = 'json'
        this.content.route = JSON.stringify(this.$route.params)
        var response = await AuthorService.createContentData(this.content)
        if (response.data.error != true) {
          this.$router.push({
            name: 'previewLanguages',
            params: {
              country_code: this.$route.params.country_code,
            },
          })
        } else {
          this.error = true
          this.loaded = false
          this.error_message = response.data.message
        }
      } catch (error) {
        LogService.consoleLogError('LANGUAGES SORT There was an error ', error)
        this.error = true
        this.loaded = false
        this.error_message = response.data.message
      }
    },
  },
  beforeCreate() {
    this.$route.params.version = 'latest'
  },
  async created() {
    try {
      await this.getLanguages(this.$route.params)
      this.authorized = this.authorize('write', this.$route.params)
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
