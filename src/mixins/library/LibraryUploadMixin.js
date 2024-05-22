import Vue from 'vue'
import Vuex from 'vuex'
import { mapState } from 'vuex'
import LogService from '@/services/LogService.js'
import AuthorService from '@/services/AuthorService.js'
//import { timeout } from 'q'
Vue.use(Vuex)

export const libraryUploadMixin = {
  computed: mapState(['user']),
  methods: {
    async handleImageUpload(imageFile) {
      console.log('I am in handleImgeUpload', imageFile)
      console.log(imageFile.name)
      var type = AuthorService.typeImage(imageFile.type)
      console.log(type)
      if (type) {
        var params = {}
        params.directory = this.$store.state.bookmark.language.image_dir
        params.name = imageFile.name
        var imagePath = params.directory + '/' + params.name
        await AuthorService.imageStore(params, imageFile)
        this.$store.commit('updateBookImages', {
          image: imagePath,
          title: imageFile.name,
        })
      }
    },

    async handleStyleUpload(code) {
      LogService.consoleLogMessage('code in handle Style:' + code)
      var checkfile = ''
      var i = 0
      var arrayLength = this.$refs.style.length
      LogService.consoleLogMessage(this.$refs.style)
      for (i = 0; i < arrayLength; i++) {
        checkfile = this.$refs.style[i]['files']
        if (checkfile.length == 1) {
          LogService.consoleLogMessage(checkfile[0])
          var type = AuthorService.typeStyle(checkfile[0])
          if (type) {
            LogService.consoleLogMessage(checkfile)
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
    async handleTemplateUpload(code) {
      LogService.consoleLogMessage('code in handle Template:' + code)
      var checkfile = ''
      var i = 0
      var arrayLength = this.$refs.template.length
      LogService.consoleLogMessage(this.$refs.template)
      for (i = 0; i < arrayLength; i++) {
        checkfile = this.$refs.template[i]['files']
        if (checkfile.length == 1) {
          LogService.consoleLogMessage(' i is ' + i)
          LogService.consoleLogMessage(checkfile[0])
          var type = AuthorService.typeTemplate(checkfile[0])
          if (type) {
            LogService.consoleLogMessage('type ok')
            LogService.consoleLogMessage(checkfile)
            var params = {}
            params.file = checkfile[0]
            params.country_code = this.$route.params.country_code
            params.language_iso = this.$route.params.language_iso
            params.folder_name = code
            LogService.consoleLogMessage(params)
            type = await AuthorService.createTemplate(params)
            if (type) {
              var template = await AuthorService.getTemplates(params)
              if (template) {
                this.templates = template
                LogService.consoleLogMessage(template)
                this.template_error = false
              }
            }
          } else {
            this.template_error = true
          }
        }
      }
    },
  },
}
