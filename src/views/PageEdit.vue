<template>
  <div>
    <NavBar called_by="page" />

    <div class="loading" v-if="loading">Loading...</div>
    <div class="error" v-if="error">
      There was an error... {{ this.error_message }}
    </div>
    <div class="content" v-if="loaded">
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div v-if="this.authorized">
        <div class="app-link">
          <div class="app-card -shadow">
            <div v-on:click="goBack()">
              <img v-bind:src="this.bookmark.book.image.image" class="book" />

              <div class="book">
                <span class="title">{{ this.bookmark.book.title }}</span>
              </div>
            </div>
          </div>
        </div>
        <h1 v-if="this.bookmark.page.count">
          {{ this.bookmark.page.count }}. {{ this.bookmark.page.title }}
          <a
            target="_blank"
            class="help"
            v-bind:href="this.prototype_url + 'HD/eng/help-1/page_edit.html'"
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>

        <h1 v-else>
          {{ this.bookmark.page.title }}
          <a
            target="_blank"
            class="help"
            v-bind:href="this.prototype_url + 'HD/eng/help-1/page_edit.html'"
          >
            <img class="help-icon" src="/sites/default/images/icons/help.png" />
          </a>
        </h1>
        <div>
          <div class="form">
            <BaseInput
              v-model="reference"
              label="Passages to Insert into  [BibleBlock]"
              type="text"
              placeholder
              class="field"
            />
          </div>
          <button class="button green" @click="insertBibleBlock">
            Insert Bible Blocks
          </button>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <button class="button green" @click="insertBiblePopups">
            Insert Bible Popups
          </button>
          <div>
            <button class="button green" @click="insertBibleLinks">
              Insert Bible Links
            </button>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button class="button green" @click="removeBiblePopupsAndBlocks">
              Remove Bible Popups and Blocks
            </button>
          </div>
          <div>
            <button class="button green" @click="revertPage">
              Revert to Previous Edition
            </button>
          </div>
          <p>
            <vue-ckeditor v-model="pageText" :config="config" />
          </p>
          <button class="button red" @click="saveForm">Save Changes</button>
        </div>
      </div>
      <div v-if="!this.authorized">
        <BaseNotAuthorized />
      </div>
      <div></div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import ContentService from '@/services/ContentService.js'
import AuthorService from '@/services/AuthorService.js'
import BibleService from '@/services/BibleService.js'
import LogService from '@/services/LogService.js'
import NavBar from '@/components/NavBarAdmin.vue'
import './ckeditor/index.js'
import VueCkeditor from 'vue-ckeditor2'
import { pageMixin } from '@/mixins/PageMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
export default {
  mixins: [pageMixin, authorizeMixin],
  props: [
    'country_code',
    'language_iso',
    'folder_name',
    'filename',
    'styles_set',
  ],
  components: {
    NavBar,
    VueCkeditor,
  },
  computed: mapState(['bookmark', 'cssURL', 'standard']),
  data() {
    return {
      authorized: false,
      config: {
        contentsCss: this.$route.params.css,
        extraAllowedContent: [
          '*(*)[id]',
          'ol[*]',
          'li[*]',
          'span[*]',
          'audio[*]',
          'align[*]',
          'webkitallowfullscreen',
          'mozallowfullscreen',
          'allowfullscreen',
          '*(*)div',
          'label',
          'div[id]',
          'div[*]',
          'form[*]',
          'script[*]',
          'textarea[*]',
          'textarea[id*]',
          'checkbox[*]',
          'button[*]',
          'select[*]',
          'option',
        ],
        extraPlugins: [
          'bidi',
          'uploadimage',
          'forms',
          'uploadwidget',
          'clipboard',
          'templates',
          'panelbutton',
          'floatpanel',
          'colorbutton',
          'justify',
          'liststyle',
        ],
        filebrowserBrowseUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL + 'ckfinder.html',
        filebrowserUploadUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL +
          'core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=' +
          this.languageDirectory,
        height: 600,
        removeButtons:
          'About,CreatePlaceholder,DocProps,Flash,Iframe,' +
          'MediaEmbed,NewPage,PageBreak,Preview,Print,Save,Scayt,Smiley,SpecialChar',
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
          '/',
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
        filetype: '',
        filename: '',
        folder: '',
        language_iso: '',
        publish_date: '',
        publish_uid: '',
        recnum: '',
        text: '',
        title: '',
        version: '',
      },
      languageDirectory: '',
      pageText: '',
      prototype_url: process.env.VUE_APP_PROTOTYPE_CONTENT_URL,
      reference: null,
      request_passage: false,
      templates: [],
    }
  },
  methods: {
    goBack() {
      window.history.back()
    },
    async insertBibleLinks() {
      var params = {}
      params.text = ContentService.validate(this.pageText)
      params.route = JSON.stringify(this.$route.params)
      //console.log(params.text)
      //params.recnum = this.recnum
      await BibleService.bibleLinkMaker(params)
      await this.showPage()
    },
    async insertBiblePopups() {
      var params = {}
      params.bookmark = JSON.stringify(this.bookmark)
      //console.log(params.text)
      params.text = ContentService.validate(this.pageText)
      params.route = JSON.stringify(this.$route.params)
      //console.log('going to biblePopupMaker')
      await BibleService.biblePopupMaker(params)
      await this.showPage()
    },
    async insertBibleBlock() {
      var params = {}
      params.language_iso = this.$route.params.language_iso
      params.entry = this.reference
      params.version_ot = this.bookmark.language.bible_ot
      params.version_nt = this.bookmark.language.bible_nt
      params.read_more = this.bookmark.language.read_more
      var bible = await BibleService.getBibleBlockToInsert(params)
      //console.log('this is Bible')
      //console.log(bible)
      if (typeof bible !== 'undefined') {
        var temp = this.pageText
        this.pageText = temp.replace('[BibleBlock]', bible.bible_block)
        temp = this.pageText
        this.pageText = temp.replace('[Reference]', bible.reference)
        this.reference = ''
      }
    },
    async removeBiblePopupsAndBlocks() {
      var params = {}
      params.text = ContentService.validate(this.pageText)
      params.route = JSON.stringify(this.$route.params)
      await BibleService.removeBiblePopupsAndBlocks(params)
      await this.showPage()
    },
    async revertPage() {
      var params = {}
      params.recnum = this.recnum
      params.route = JSON.stringify(this.$route.params)
      params.scope = 'page'
      var res = await AuthorService.revert(params)
      //console.log(res)
      this.pageText = res.text
      this.recnum = res.recnum
    },
    async saveForm() {
      try {
        this.content.text = ContentService.validate(this.pageText)
        this.content.route = JSON.stringify(this.$route.params)
        this.content.filetype = 'html'
        var response = await AuthorService.createContentData(this.content)
        //console.log(response)
        if (response.data == 'Success') {
          this.$router.push({
            name: 'previewPage',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: this.$route.params.library_code,
              folder_name: this.$route.params.folder_name,
              filename: this.$route.params.filename,
            },
          })
        } else {
          alert('there is an error in saving your edits')
          this.error = true
          this.loaded = false
          this.error_message = response.data.message
        }
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in saveForm of PageEdit ',
          error
        )
        this.error = true
        this.loaded = false
        this.error_message = error
      }
    },
    async showPage() {
      try {
        this.authorized = this.authorize('write', this.$route.params)
        //console.log('I am about to get page or template')
        await this.getPageorTemplate('either')
      } catch (error) {
        LogService.consoleLogError(
          'There was an error in PageEdit.vue during showPage:',
          error
        )
      }
    },
  },
  async beforeCreate() {
    // set directory for custom images
    //see https://ckeditor.com/docs/ckfinder/ckfinder3-php/integration.html
    this.languageDirectory =
      this.$route.params.country_code +
      '/' +
      this.$route.params.language_iso +
      '/images/custom'
    //console.log('This Language Directory' + this.languageDirectory)
    this.$route.params.version = 'lastest'
    if (typeof this.$route.params.styles_set == 'undefined') {
      this.$route.params.styles_set = process.env.VUE_APP_SITE_STYLES_SET
    }
    var css = this.$route.params.cssFORMATTED
    var clean = css.replace(/@/g, '/')
    this.$route.params.css = clean
  },
  async created() {
    await this.showPage()
  },
}
</script>
