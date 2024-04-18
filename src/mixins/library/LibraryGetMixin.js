import Vue from 'vue'
import Vuex from 'vuex'
import { mapState } from 'vuex'
import LogService from '@/services/LogService.js'
import AuthorService from '@/services/AuthorService.js'
//import { timeout } from 'q'
Vue.use(Vuex)

export const libraryGetMixin = {
  computed: mapState(['user']),
  methods: {
    async getCkEditStyleSets(param) {
      var style_sets = await AuthorService.getCkEditStyleSets(param)
      if (typeof style_sets !== 'undefined') {
        this.ckEditStyleSets = style_sets
      }
    },
    async getStyles(param) {
      // get styles
      var style = await AuthorService.getStyles(param)
      if (typeof style !== 'undefined') {
        this.styles = style
      }
    },

    async getTemplates() {
      //get templates
      var template = await AuthorService.getTemplates(param)
      if (typeof template !== 'undefined') {
        this.templates = template
        LogService.consoleLogMessage('this.templates')
        LogService.consoleLogMessage(this.templates)
      }
    },
  },
}
