<template>
  <div>
    <NavBar called_by="library" />

    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      Library Index Edit says there was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div v-if="this.authorized">
        <h1>
          {{ this.bookmark.language.name }} -- {{ this.bookmark.country.name }}
          <a
            target="_blank"
            class="help"
            v-bind:href="
              this.prototype_url + 'HD/eng/help-1/library_index_preview.html'
            "
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>
        <p>
          <vue-ckeditor v-model="pageText" :config="config" />
        </p>
        <hr />
        <h2>Page Style</h2>
        <BaseSelect :options="styles" v-model="style" class="field" />
        <template v-if="style_error">
          <p class="errorMessage">Only .css files may be uploaded</p>
        </template>

        <label>
          Add new stylesheet&nbsp;&nbsp;&nbsp;&nbsp;
          <input
            type="file"
            v-bind:id="style"
            ref="style"
            v-on:change="handleStyleUpload()"
          />
        </label>
        <br />
        <br />
        <hr />
        <h2>Language Footer Text</h2>
        <p>
          <vue-ckeditor v-model="footerText" :config="config" />
        </p>
        <div class="version">
          <p class="version">Version 2.05</p>
        </div>
        <button class="button red" @click="saveForm">Save Changes</button>
      </div>
      <div v-if="!this.authorized">
        <p>
          You need to
          <a href="/login">login to make changes</a> here
        </p>
      </div>
      <div></div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import NavBar from '@/components/NavBarCountry.vue'
import './ckeditor/index.js'
import VueCkeditor from 'vue-ckeditor2'

import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
export default {
  mixins: [libraryUpdateMixin, authorizeMixin],
  props: ['country_code', 'language_iso'],
  components: {
    NavBar,
    VueCkeditor,
  },
  computed: mapState(['bookmark', 'cssURL']),
  data() {
    return {
      authorized: false,
      config: {
        contentsCss: this.$route.params.css,
        extraAllowedContent: [
          '*(*)[id]',
          'ol[*]',
          'span[*]',
          'align[*]',
          'webkitallowfullscreen',
          'mozallowfullscreen',
          'allowfullscreen',
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
          'About,Button,Checkbox,CreatePlaceholder,DocProps,Flash,Form,HiddenField,Iframe,NewPage,PageBreak,Preview,Print,Radio,Save,Scayt,Select,Smiley,SpecialChar,TextField,Textarea',
        stylesSet: this.$route.params.styles_set,
        templates_files: [
          '/sites/' +
            process.env.VUE_APP_SITE +
            '/ckeditor/templates/' +
            this.$route.params.styles_set +
            '.js',
        ],
        templates_replaceContent: false,
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
          {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'],
          },
          { name: 'colors', groups: ['colors'] },
          { name: 'about', groups: ['about'] },
        ],
      },
      content: {
        country_code: '',
        edit_date: '',
        edit_uid: '',
        filename: '',
        filetype: '',
        folder: '',
        language_iso: '',
        publish_date: '',
        publish_uid: '',
        recnum: '',
        text: '',
        title: '',
        version: '',
      },
      error: null,
      error_message: '',
      image_dir: '',
      languageDirectory: '',
      loaded: null,
      loading: false,
      pageText: '',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      publish: false,
      recnum: null,
      styles: [],
      style_error: false,
    }
  },
  methods: {
    goBack() {
      window.history.back()
    },
    async loadTemplate() {
      this.authorized = this.authorize('write', this.$route.params)
      this.loading = false
      this.loaded = true
      if (this.bookmark.book.template) {
        this.$route.params.template = this.bookmark.book.template
        LogService.consoleLogMessage('source','looking for template')
        LogService.consoleLogMessage('source',this.$route.params)
        var res = await AuthorService.getTemplate(this.$route.params)
        LogService.consoleLogMessage('source',res)
        if (res) {
          LogService.consoleLogMessage('source','I found template')
          this.pageText = res
        }
      }
    },
    async handleStyleUpload(code) {
      LogService.consoleLogMessage('source','code in handle Style:' + code)
      var checkfile = ''
      var i = 0
      var arrayLength = this.$refs.style.length
      LogService.consoleLogMessage('source',this.$refs.style)
      for (i = 0; i < arrayLength; i++) {
        checkfile = this.$refs.style[i]['files']
        if (checkfile.length == 1) {
          LogService.consoleLogMessage('source',checkfile[0])
          var type = AuthorService.typeStyle(checkfile[0])
          if (type) {
            LogService.consoleLogMessage('source',checkfile)
            var params = {}
            params.file = checkfile[0]
            params.country_code = this.$route.params.country_code
            type = await AuthorService.createStyle(params)
            var style = await AuthorService.getStyles(params)
            if (style) {
              this.styles = style
              this.style_error = false
            }
          } else {
            this.style_error = true
          }
        }
      }
    },

    async beforeCreate() {
      LogService.consoleLogMessage('source','before Create in LibraryIndexEdit')
      LogService.consoleLogMessage('source',this.$route.params)
      // set directory for custom images
      //see https://ckeditor.com/docs/ckfinder/ckfinder3-php/integration.html
      this.languageDirectory =
        '/content/' +
        this.$route.params.country_code +
        '/' +
        this.$route.params.language_iso +
        '/images/custom'

      this.$route.params.styles_set = process.env.VUE_APP_SITE_STYLES_SET
      this.$route.params.version = 'lastest'
      this.$route.params.filename = 'index'
      this.$route.params.library_code = 'index'
      this.$route.params.css = '/sites/default/styles/freeformGLOBAL.css'
      LogService.consoleLogMessage('source','final params')
      LogService.consoleLogMessage('source',this.$route.params)
    },
    async created() {
      try {
        LogService.consoleLogMessage('source','in Created')
        LogService.consoleLogMessage('source',this.$route)
        await this.getLibraryIndex()
        // get styles
        var param = {}
        param.route = JSON.stringify(this.$route.params)
        var style = await AuthorService.getStyles(param)
        if (typeof style !== 'undefined') {
          this.styles = style
        }

        this.authorized = this.authorize('write', this.$route.params)
        this.publish = false
        if (this.recnum && !this.publish_date) {
          this.publish = this.authorize('publish', this.$route.params)
        }
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in LanguageIndexEdit.vue:',
          error
        )
        this.$router.push({
          name: 'login',
        })
      }
    },
  },
}
</script>
