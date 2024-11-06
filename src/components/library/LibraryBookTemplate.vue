<template>
  <div>
    <hr />
    <p>Template:</p>

    <v-select
      label="Template:"
      :options="bookTemplates"
      v-model="libraryBookTemplate"
      class="field"
    />
    <label>
      Add new template&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input
        type="file"
        ref="template"
        v-on:change="handleTemplateUpload(book.code)"
      />
    </label>
    <button class="button grey" @click="createOrEditTemplate()">
      Edit or Create Template
    </button>
  </div>
</template>
<script>
import AuthorService from '@/services/AuthorService.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
import { mapState } from 'vuex'
import vSelect from 'vue-select'
export default {
  props: {
    index: {
      type: Number,
      required: true,
    },
  },
  components: {
    'v-select': vSelect,
  },
  mixins: [libraryUploadMixin],
  computed: {
    ...mapState(['bookTemplate']),
    bookTemplates() {
      return this.$store.state.bookTemplates
    },
    libraryBookTemplate: {
      get() {
        return this.$store.getters.getBookProperty(this.index, 'template')
      },
      set(value) {
        this.$store.commit('setBookProperty', {
          index: this.index,
          property: 'template',
          value,
        }) // Update book template in store
      },
    },
  },

  methods: {
    async createOrEditTemplate() {
      await this.saveForm('stay')
      // creating a new template
      this.$router.push({
        name: 'createTemplate',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
          book_index: this.index,
        },
      })
    },
    async saveForm(action = null) {
      if (this.saving) return // Prevent re-trigger
      this.saving = true // Disable button

      try {
        console.log('saveForm')

        // Prepare content data
        const library = this.$store.state.bookmark.library
        const { country_code, language_iso, library_code } = this.$route.params
        const route = { ...this.$route.params, filename: library_code }
        delete route.folder_name

        this.content = {
          text: JSON.stringify(library),
          route: JSON.stringify(route),
          filetype: 'json',
        }

        console.log(this.content)

        // Send content to AuthorService
        const response = await AuthorService.createContentData(this.content)
        console.log(response)

        if (response.data.error) {
          this.handleError(response.data.message)
        } else if (action !== 'stay') {
          this.navigateToPreview({ country_code, language_iso, library_code })
        }
      } catch (error) {
        this.handleException(error)
      } finally {
        this.saving = false // Re-enable button
      }
    },
    navigateToPreview(params) {
      this.$router.push({ name: 'previewLibrary', params })
    },

    handleError(message) {
      this.error = true
      this.loaded = false
      this.error_message = message
    },

    handleException(error) {
      this.error_message = error
      this.error = true
      this.loaded = false
    },
  },
}
</script>
<style scoped>
.margin-left {
  margin-left: 10px;
}
</style>
