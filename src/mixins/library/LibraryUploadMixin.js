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
    async handleStyleUpload(cssFile) {
      console.log('I am in handleStyleUpload', cssFile)
      console.log(cssFile.name)
      var params = {}
      params.file = cssFile
      params.country_code = this.$route.params.country_code
      params.language_iso = this.$route.params.language_iso
      var res = await AuthorService.createStyle(params)
      console.log('res', res)
    },
    async handleTemplateUpload(code) {
      LogService.consoleLogMessage('source','code in handle Template:' + code)
      var checkfile = ''
      var i = 0
      var arrayLength = this.$refs.template.length
      LogService.consoleLogMessage('source',this.$refs.template)
      for (i = 0; i < arrayLength; i++) {
        checkfile = this.$refs.template[i]['files']
        if (checkfile.length == 1) {
          LogService.consoleLogMessage('source',' i is ' + i)
          LogService.consoleLogMessage('source',checkfile[0])
          var type = AuthorService.typeTemplate(checkfile[0])
          if (type) {
            LogService.consoleLogMessage('source','type ok')
            LogService.consoleLogMessage('source',checkfile)
            var params = {}
            params.file = checkfile[0]
            params.country_code = this.$route.params.country_code
            params.language_iso = this.$route.params.language_iso
            params.folder_name = code
            LogService.consoleLogMessage('source',params)
            type = await AuthorService.createTemplate(params)
            if (type) {
              var template = await AuthorService.getTemplates(params)
              if (template) {
                this.templates = template
                LogService.consoleLogMessage('source',template)
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
