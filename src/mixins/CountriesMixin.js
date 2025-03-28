import AuthorService from '@/services/AuthorService.js'
import ContentService from '@/services/ContentService.js'
import LogService from '@/services/LogService.js'

export const countriesMixin = {
  data() {
    return {
      countries: [
        {
          name: null,
          english: null,
          code: null,
          index: null,
          image: null,
          publish: null,
        },
      ],
      loading: false,
      loaded: null,
      error: null,
      error_message: null,
      prototype: false,
      prototype_date: null,
      publish: false,
      publish_date: null,

      recnum: null,
      content: {
        recnum: '',
        version: '',
        edit_date: '',
        edit_uid: '',
        publish_uid: '',
        publish_date: '',
        language_iso: '',
        country_code: '',
        folder: '',
        filetype: '',
        title: '',
        filename: '',
        text: '',
      },
    }
  },

  methods: {
    async getCountries() {
      try {
        this.error = this.loaded = null
        this.loading = true
        this.countries = []
        AuthorService.bookmarkClear()
        var response = await ContentService.getCountries(this.$route.params)
        console.log(response)
        if (Array.isArray(response.text) && response.text.length > 0) {
          console.log('Countries found:', response.text.length)
          this.countries = response.text
        } else {
          console.log('No countries in the list.')
          this.countries = []
        }
        console.log(this.countries)
        console.log(response.recnum)
        if (response.recnum) {
          this.recnum = response.recnum
          this.publish_date = response.publish_date
          this.prototype_date = response.prototype_date
        }
        this.loaded = true
        this.loading = false
        console.log('CountryMixin Finished Get Countries')
      } catch (error) {
        LogService.consoleLogError(
          'There was an error with ContentService.getCountries of CountriesMixin:',
          error
        )
        console.log('error line 73')
        
      }
    },
    async showPage(country) {
      //console.log('show Page line 81')
      localStorage.setItem('lastPage', 'countries')
      this.$route.params.country_code = country.code
      var link = ''
      var res = await ContentService.getLanguages(this.$route.params)
      var response = res.text
      if (response.length === 1) {
        link = 'library'
        if (this.$route.params.version == 'latest') {
          link = 'previewLibrary'
        }
        var language = response[0]
        console.log('show Page line 93')
        this.$router.push({
          name: link,
          params: {
            country_code: country.code,
            language_iso: language.iso,
            library_code: 'library',
          },
        })
      } else {
        console.log('show Page line 102')
        link = 'languages'
        if (this.$route.params.version == 'latest') {
          link = 'previewLanguages'
        }

        this.$router.push({
          name: link,
          params: { country_code: country.code },
        })
      }
    },
  },
}
