<template>
  <div>
    <NavBar called_by="template" />

    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      There was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div v-if="this.authorized">
        <BaseInput
          label="Filename:"
          v-model="templateFilename"
          type="text"
          class="field"
        />

        <h2>Shortcuts you can use in templates</h2>
        <ul>
          <li>[BibleBlock]</li>
          <li>[Reference]</li>
        </ul>

        <vue-ckeditor v-model="pageText" :config="config" />

        <div class="version">
          <p class="version">Version 2.05 {{ this.languageDirectory }}</p>
        </div>
        <button class="button red" @click="saveForm">Save Changes</button>
      </div>
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div></div>
    </div>
  </div>
</template>

<script>
import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import NavBar from '@/components/NavBarAdmin.vue'
import './ckeditor/index.js'
import VueCkeditor from 'vue-ckeditor2'

import { pageMixin } from '@/mixins/PageMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
export default {
  mixins: [pageMixin, authorizeMixin],
  props: ['country_code', 'language_iso', 'library_code', 'book_index'],
  components: {
    NavBar,
    VueCkeditor,
  },
  data() {
    return {
      authorized: false,
      templateFilename: '',
      config: {
        extraAllowedContent: [
          '*(*)[id]',
          'ol[*]',
          'span[*]',
          'align[*]',
          'webkitallowfullscreen',
          'mozallowfullscreen',
          'allowfullscreen',
          '*(*)div',
          'label',
          'div[id]',
          'div[*]',
          'form[*]',
          'textarea[*]',
          'textarea[id*]',
          'button[*]',
        ],
        extraPlugins: [
          'bidi',
          'uploadimage',
          'iframe',
          'uploadwidget',
          'clipboard',
          'videoembed',
          'templates',
          'panelbutton',
          'floatpanel',
          'colorbutton',
          'justify',
        ],
        filebrowserBrowseUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL + 'ckfinder.html',
        filebrowserUploadUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL +
          'core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=' +
          this.languageDirectory,
        height: 600,
        removeButtons:
          'About,Button,Checkbox,CreatePlaceholder,DocProps,Flash,Form,' +
          'HiddenField,Iframe,NewPage,PageBreak,Preview,Print,Radio,Save,' +
          'Scayt,Select,Smiley,SpecialChar,TextField,Textarea',
        toolbarGroups: [
          { name: 'styles', groups: ['styles'] },
          { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
          {
            name: 'editing',
            groups: ['find', 'selection', 'spellchecker', 'editing'],
          },
          { name: 'links', groups: ['links'] },
          { name: 'insert', groups: ['insert'] },
          { name: 'forms', groups: ['forms'] },
          { name: 'tools', groups: ['tools'] },
          { name: 'document', groups: ['mode', 'document', 'doctools'] },
          { name: 'clipboard', groups: ['clipboard', 'undo'] },
          { name: 'others', groups: ['others'] },
          '/',
          {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'],
          },
          { name: 'colors', groups: ['colors'] },
          { name: 'about', groups: ['about'] },
        ],
      },
      content: {},
      templates_replaceContent: false,
    }
  },
  computed: {
    book() {
      return this.$store.state.bookmark.library.books[this.book_index]
    },
    template_filename: {
      get() {
        return this.book ? this.book.template : ''
      },
      set(value) {
        if (this.book) {
          console.log('Setter called with value:', value)
          this.book.template = value
        }
      },
    },
    bookFormat() {
      return this.book ? this.book.format : ''
    },
    contentsCss() {
      return this.book ? this.book.style : ''
    },
    stylesSet() {
      return this.book ? this.book.styles_set : ''
    },
    templates_files() {
      return this.stylesSet
        ? [
            `/sites/${process.env.VUE_APP_SITE}/ckeditor/templates/${this.stylesSet}.js`,
          ]
        : []
    },
  },

  methods: {
    goBack() {
      window.history.back()
    },
    async loadTemplate() {
      var params = this.$route.params
      params.template = this.template_filename
      console.log(params)
      var res = await AuthorService.getTemplate(params)
      if (res) {
        this.pageText = res
      }
    },
    async saveForm() {
      try {
        console.log('saveForm called')
        this.content.text = ContentService.validate(this.pageText)
        console.log('Finsihed validating')
        console.log(this.templateFilename)
        //todo: save all of library data
        const safeName = this.templateFilename
        var params = this.$route.params
        params.text = this.pageText
        params.book_format = this.book.format
        if (this.book.format == 'series') {
          params.series = this.book.code
        }
        console.log(this.params)
        params.template = this.templateFilename
        params.destination = 'edit'
        console.log('TemplateViesw saveForm')
        console.log(params)
        await AuthorService.editTemplate(params)
        this.book.template = safeName
        this.$router.push({
          name: 'editLibrary',
          params: {
            country_code: this.$route.params.country_code,
            language_iso: this.$route.params.language_iso,
            library_code: this.$route.params.library_code,
          },
        })
      } catch (error) {
        LogService.consoleLogError('LIBRARY EDIT There was an error ', error)
        this.error = true
        this.loaded = false
        this.error_message = error
      }
    },
  },
  async beforeCreate() {
    this.$route.params.version = 'lastest'
    LogService.consoleLogMessage('source', this.$route.params)
    // set directory for custom images
    //see https://ckeditor.com/docs/ckfinder/ckfinder3-php/integration.html
    this.languageDirectory =
      '/content/' +
      this.$route.params.country_code +
      '/' +
      this.$route.params.language_iso +
      '/images/custom'
  },
  async created() {
    try {
      this.authorized = this.authorize('write', this.$route.params)
      this.templateFilename = this.book ? this.book.template : ''
      await this.loadTemplate()
      this.loaded = true
      this.loading = false
    } catch (error) {
      LogService.consoleLogError('There was an error in Template.vue:', error)
    }
  },
}
</script>
