<template>
  <div>
    <BaseSelect
      label="Template:"
      :options="templates"
      v-model="book.template.$model"
      class="field"
      :class="{ error: book.template.$error }"
      @mousedown="book.template.$touch()"
    />
    <label>
      Add new template&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input
        type="file"
        v-bind:id="book.title.$model"
        ref="template"
        v-on:change="handleTemplateUpload(book.code.$model)"
      />
    </label>
    <template v-if="template_error">
      <p class="errorMessage">Only .html files may be uploaded as templates</p>
    </template>
    <button
      class="button yellow"
      @click="
        createTemplate(
          book.template.$model,
          book.style.$model,
          book.styles_set.$model,
          book.title.$model,
          book.code.$model,
          book.format.$model
        )
      "
    >
      Edit or Create Template
    </button>
  </div>
</template>
<script>
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
export default {
  mixins: [libraryUploadMixin],
  data() {
    return {
      templates: [],
    }
  },
  methods: {
    async createTemplate(
      template,
      css,
      styles_set,
      title,
      book_code,
      book_format
    ) {
      await this.saveForm('stay')
      // creating a new template
      if (typeof template == 'undefined') {
        template = 'new'
      }
      if (typeof styles_set == 'undefined') {
        styles_set = 'default'
      }
      // use default style if not set
      if (typeof css == 'undefined') {
        css = this.style
      }
      LogService.consoleLogMessage(template)
      LogService.consoleLogMessage(css)
      this.$router.push({
        name: 'createTemplate',
        params: {
          country_code: this.$route.params.country_code,
          language_iso: this.$route.params.language_iso,
          library_code: this.$route.params.library_code,
          title: title,
          template: template,
          cssFORMATTED: css,
          styles_set: styles_set,
          book_code: book_code,
          book_format: book_format,
        },
      })
    },
    
  },
}
</script>
