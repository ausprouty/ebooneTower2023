import BibleService from '@/services/BibleService.js'
import LogService from '@/services/LogService.js'
export const bibleMixin = {
  methods: {
    /* expects  var params = {
          language_iso: language_iso,
          testament: "OT" or "NT" or "Full"
        }
    */
    async getBibleVersions(params) {
      try {
        const versions = []

        const iso = params.language_iso
        console.log('Get Bible Versions For:', iso)

        if (iso.length < 2) {
          return versions
        }

        const response = await BibleService.getBibleVersions(params)
        console.log('Bible Versions Response:', response)

        if (response !== false) {
          return response
        }

        return versions
      } catch (error) {
        LogService.consoleLogError(
          'BIBLE MIXIN -- There was an error finding Bible Versions:',
          error
        )
        this.error = error.toString() + ' BIBLE MIXIN -- getBibleVersions'
        return null
      }
    },
  },
}
