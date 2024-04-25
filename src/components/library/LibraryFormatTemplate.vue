<template>
  <div class="app-card -shadow">
    <h2>Library Setup</h2>

    <hr />
    <div>
      <h3>Library Image</h3>
      <v-select :options="images" label="title" v-model="libraryFormatImage">
        <template slot="option" slot-scope="option">
          <img :src="option.image" class="select" />
          <br />
          {{ option.title }}
        </template>
      </v-select>
    </div>
    <div>
      <input type="checkbox" id="checkbox" v-model="libraryFormatNoRibbon" />
      <label for="checkbox">
        <p>Image contains back button image</p>
      </label>
    </div>

    <br />
    <br />

    <div v-if="image_permission">
      <label>
        Add new Image&nbsp;&nbsp;&nbsp;&nbsp;
        <input
          type="file"
          v-bind:id="libraryFormatImage"
          ref="imageHeader"
          v-on:change="handleHeaderUpload(image)"
        />
      </label>
    </div>

    <div>
      <br />
      <br />
      <BaseSelect
        label="Library Style Sheet:"
        :options="styles"
        v-model="libraryFormatStyle"
        class="field"
      />
      <template v-if="style_error">
        <p class="errorMessage">Only .css files may be uploaded</p>
      </template>

      <label>
        Add new stylesheet&nbsp;&nbsp;&nbsp;&nbsp;
        <input
          type="file"
          v-bind:id="libraryFormatStyle"
          ref="style"
          v-on:change="handleStyleUpload(format.style)"
        />
      </label>
    </div>
    <div>
      <p>Back Button for Chapters</p>
      <v-select
        :options="back_buttons"
        label="title"
        v-model="libraryFormatBackButton"
      >
        <template slot="option" slot-scope="option">
          <img :src="option.image" class="select" />
          <br />
          {{ option.title }}
        </template>
      </v-select>
    </div>

    <div v-if="image_permission">
      <div v-if="libraryFormatBackButton">
        <img v-bind:src="option.image" class="header" />
      </div>
      <label>
        Add new Image&nbsp;&nbsp;&nbsp;&nbsp;
        <input
          type="file"
          v-bind:id="libraryFormatBackButton"
          ref="imageBackButton"
          v-on:change="handleBackButtonUpload(format.back_button)"
        />
      </label>
    </div>
    <div>
      <input type="checkbox" id="checkbox" v-model="libraryFormatCustom" />
      <label for="checkbox">
        <h2>Use ONLY Preliminary Text for library (not cards)?</h2>
      </label>
    </div>
  </div>
</template>
<script>
import vSelect from 'vue-select'
// see https://stackoverflow.com/questions/55479380/adding-images-to-vue-select-dropdown
import { mapGetters, mapMutations } from 'vuex'
import '@/assets/css/vueSelect.css'
import AuthorService from '@/services/AuthorService.js'
import LogService from '@/services/LogService.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUpdateMixin } from '@/mixins/library/LibraryUpdateMixin.js'

export default {
  mixins: [authorizeMixin, libraryGetMixin, libraryUpdateMixin],
  components: {
    'v-select': vSelect,
  },
  data() {
    return {
      back_buttons: [],
      custom_format: false,
      format: {},
      image_dir: null,
      image: null,
      image_permission: false,
      images: [],
      option: [],
      options: [],
      styles: [],
      style_error: false,
    }
  },

  computed: {
    ...mapGetters([
      'getLanguageImageDirectory', // value can not be changed here
      'getLibraryFormatBackButton',
      'getLibraryFormatCustom',
      'getLibraryFormatImage',
      'getLibraryFormatNoRibbon',
      'getLibraryFormatStyle',
    ]),

    libraryFormatBackButton: {
      get() {
        return this.getLibraryFormatBackButton
      },
      set(value) {
        this.setLibraryFormatBackButton(value)
      },
    },
    libraryFormatCustom: {
      get() {
        return this.getLibraryFormatCustom
      },
      set(value) {
        this.setLibraryFormatCustom(value)
      },
    },
    libraryFormatImage: {
      get() {
        return this.getLibraryFormatImage
      },
      set(value) {
        this.setLibraryFormatImage(value)
      },
    },
    libraryFormatNoRibbon: {
      get() {
        return this.getLibraryFormatNoRibbon
      },
      set(value) {
        this.setLibraryFormatNoRibbon(value)
      },
    },
    libraryFormatStyle: {
      get() {
        return this.getLibraryFormatStyle
      },
      set(value) {
        this.setLibraryFormatStyle(value)
      },
    },
  },

  async created() {
    await this.showForm()
  },
  methods: {
    ...mapMutations([
      'setLibraryFormatBackButton',
      'setLibraryFormatCustom',
      'setLibraryFormatImage',
      'setLibraryFormatNoRibbon',
      'setLibraryFormatStyle',
    ]),
    async showForm() {
      await this.getBookmark()
      var params = {}
      params.route = JSON.stringify(this.$route.params)
      params.image_dir = this.getLanguageImageDirectory
      this.styles = await this.getStyles(params)
      this.image_permission = this.authorize('write', this.$route.params)
      this.images = await this.getImages(
        'content',
        this.getLanguageImageDirectory
      )
      this.back_buttons = await this.getImages('site', 'images/ribbons')
    },
    async handleHeaderUpload(code) {
      LogService.consoleLogMessage('handleImageUpload: ' + code)
      var checkfile = {}
      checkfile = this.$refs.imageHeader['files']
      if (checkfile.length == 1) {
        LogService.consoleLogMessage(checkfile)
        LogService.consoleLogMessage(checkfile[0])
        var type = AuthorService.typeImage(checkfile[0])
        if (type) {
          var params = {}
          params.directory = 'content/' + this.bookmark.language.image_dir
          params.name = code
          LogService.consoleLogMessage(params)
          await AuthorService.imageStore(params, checkfile[0])
          var filename = checkfile[0].name
          LogService.consoleLogMessage('setting this image to ' + filename)
          this.image = filename
          this.images = await this.getImages(
            'content',
            this.bookmark.language.image_dir
          )
        }
      }
    },
    async handleBackButtonUpload(code) {
      LogService.consoleLogMessage('handleImageUpload' + code.title)
      var checkfile = {}
      checkfile = this.$refs.imageBackButton['files']
      if (checkfile.length == 1) {
        LogService.consoleLogMessage(checkfile)
        LogService.consoleLogMessage(checkfile[0])
        var type = AuthorService.typeImage(checkfile[0])
        if (type) {
          var params = {}
          params.directory = '/images/ribbons'
          params.name = code.title
          LogService.consoleLogMessage(params)
          await AuthorService.imageStore(params, checkfile[0])
          var filename = checkfile[0].name
          LogService.consoleLogMessage('setting this image to ' + filename)
          this.back_button = filename
          this.back_buttons = await this.getImages('site', 'images/ribbons')
        }
      } else {
        alert('Your BackButton was not uploaded')
      }
    },
  },
}
</script>
<style scoped>
img.select {
  width: 100px;
}
</style>
