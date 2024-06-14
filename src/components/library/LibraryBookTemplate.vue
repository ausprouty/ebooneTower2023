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
    <button class="button yellow" @click="createOrEditTemplate()">
      Edit or Create Template
    </button>
  </div>
</template>
<script>
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
import { mapMutations, mapState } from 'vuex'
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
  data() {
    return {
      libraryBookTemplate:
        this.$store.state.bookmark.library.books[this.index].template,
    }
  },
  computed: {
    ...mapState(['bookTemplate']),
    bookTemplates() {
      return this.$store.state.bookTemplates
    },
  },
  watch: {
    libraryBookTemplate(newValue, oldValue) {
      this.setBookTemplate(this.index, newValue)
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
      `saveForm`
    },
    setBookTemplate(index, value) {
      alert('setBookTemplate ' + index)
      this.$store.commit('setBookTemplate', { index, value })
    },
  },
}
</script>
