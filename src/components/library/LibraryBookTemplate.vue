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
    saveForm() {
      alert('Is there a way to save the form?')
    },
  },
}
</script>
