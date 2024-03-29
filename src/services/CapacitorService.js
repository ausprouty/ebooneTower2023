const apiURL = process.env.VUE_APP_DEFAULT_SITES_URL
const apiSite = process.env.VUE_APP_SITE
const apiLocation = process.env.VUE_APP_SITE_LOCATION

const apiSECURE = axios.create({
  baseURL: apiURL,
  withCredentials: false, // This is the default
  crossDomain: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})
import axios from 'axios'
import store from '@/store/store.js'
import AuthorService from '@/services/AuthorService.js'
// I want to export a JSON.stringified of response.data.text
export default {
  initialize(params) {
    if (typeof store.state.capacitorSettings !== 'undefined') {
      var temp1 = JSON.stringify(store.state.capacitorSettings)
      params.capacitor_settings = temp1
      if (typeof store.state.capacitorSettings.language != 'undefined') {
        params.country_code =
          store.state.capacitorSettings.language.country_code
        params.language_iso =
          store.state.capacitorSettings.language.language_iso
      }
    }
    params.site = process.env.VUE_APP_SITE
    params.my_uid = localStorage.getItem('uid')
    params.subdirectory = 'capacitor'
    params.token = localStorage.getItem('token')
    if (typeof params.destination == 'undefined') {
      params.destination = 'capacitor'
    }
    //console.log(params)
    return params
  },
  async checkCommonFiles(params) {
    params = this.initialize(params)
    params.page = 'checkCommonFiles'
    params.action = 'checkCommonFiles'
    var res = await AuthorService.aReturnContent(params)
    return res
  },
  async checkContentIndex(params) {
    params = this.initialize(params)
    params.page = 'checkContentIndex'
    params.action = 'checkContentIndex'
    var res = await AuthorService.aReturnContent(params)
    return res
  },
  async checkStatusBook(params) {
    params = this.initialize(params)
    params.page = 'checkStatusBook'
    params.action = 'checkStatusBook'
    return await AuthorService.aReturnContent(params)
  },
  async createBookContent(params) {
    params = this.initialize(params)
    params.page = 'publishSeriesAndChapters'
    params.action = 'publishSeriesAndChapters'
    return await AuthorService.aReturnContent(params)
  },
  async createBookRouter(params) {
    params = this.initialize(params)
    params.page = 'createBookRouter'
    params.action = 'createBookRouter'
    return await AuthorService.aReturnContentParsed(params)
  },
  async createBookMediaList(params) {
    params = this.initialize(params)
    params.page = 'createBookMediaList'
    params.action = 'createBookMediaList'
    return await AuthorService.aReturnContentParsed(params)
  },
  async createBookMediaBatFile(params) {
    params = this.initialize(params)
    params.page = 'createBookMediaBatFile'
    params.action = 'createBookMediaBatFile'
    var response = await AuthorService.aReturnContent(params)
    console.log(response)
    return response
  },
  async createCommonFiles(params) {
    params = this.initialize(params)
    params.page = 'createCommonFiles'
    params.action = 'createCommonFiles'
    var res = await AuthorService.aReturnContent(params)
    return res
  },

  async getBooks(params) {
    params = this.initialize(params)
    params.page = 'getBooksForLanguage'
    params.action = 'getBooksForLanguage'
    return await AuthorService.aReturnContent(params)
  },
  async getFooters(params) {
    params = this.initialize(params)
    params.page = 'getFooters'
    params.action = 'getFooters'
    return await AuthorService.aReturnContent(params)
  },
  async getLanguages(params) {
    params = this.initialize(params)
    params.page = 'getLanguagesAvailable'
    params.action = 'getLanguagesAvailable'
    console.log(params)
    return await AuthorService.aReturnContent(params)
  },
  async publish(scope, params) {
    var action = null
    params = this.initialize(params)
    switch (scope) {
      case 'country':
        action = 'AuthorApi.php?page=publishCountry&action=publishCountry'
        break
      case 'language':
        action = 'AuthorApi.php?page=publish&action=publishLanguage'
        break
      case 'libraries':
        action = 'AuthorApi.php?page=publishLibraries&action=publishLibraries'
        break
      case 'library':
        action = 'AuthorApi.php?page=publishLibrary&action=publishLibrary'
        break
      case 'libraryAndBooks':
        action =
          'AuthorApi.php?page=publishLibraryAndBooks&action=publishLibraryAndBooks'
        break
      case 'libraryIndex':
        action =
          'AuthorApi.php?page=publishLibraryIndex&action=publishLibraryIndex'
        break
      case 'series':
        action = 'AuthorApi.php?page=publishSeries&action=publishSeries'
        break
      case 'seriesAndChapters':
        action =
          'AuthorApi.php?page=publishSeriesAndChapters&action=publishSeriesAndChapters'
        break
      case 'page':
        action = 'AuthorApi.php?page=publishPage&action=publishPage'
        break
      case 'videoMakeBatFile':
        action = 'AuthorApi.php?page=videoMakeBatFile&action=videoMakeBatFile'
        break
      case 'videoConcatBat':
        action = 'AuthorApi.php?page=videoConcatBat&action=videoConcatBat'
        break
      case 'media':
        action = 'AuthorApi.php?page=copyBookMedia&action=copyBookMedia'
        break

      case 'default':
        action = null
    }
    var complete_action =
      action + '&site=' + apiSite + '&location=' + apiLocation
    var contentForm = this.toFormData(params)
    var response = await apiSECURE.post(complete_action, contentForm)
    return response
  },
  async verifyBookContent(params) {
    params = this.initialize(params)
    params.page = 'verifyBookContent'
    params.action = 'verifyBookContent'
    return await AuthorService.aReturnContentParsed(params)
  },
  async verifyBookRouter(params) {
    params = this.initialize(params)
    params.page = 'verifyBookRouter'
    params.action = 'verifyBookRouter'
    return await AuthorService.aReturnContentParsed(params)
  },
  async verifyBookMedia(params) {
    params = this.initialize(params)
    params.page = 'verifyBook'
    params.action = 'verifyBookMedia'
    return await AuthorService.aReturnContentParsed(params)
  },
  async verifyBookMediaList(params) {
    params = this.initialize(params)
    params.page = 'verifyBookMediaList'
    params.action = 'verifyBookMediaList'
    return await AuthorService.aReturnContentParsed(params)
  },
  async verifyCommonFiles(params) {
    params = this.initialize(params)
    params.page = 'verifyCommonFiles'
    params.action = 'verifyCommonFiles'
    var res = await AuthorService.aReturnContent(params)
    return res
  },
  async verifyLanguageIndex(params) {
    params = this.initialize(params)
    params.page = 'verifyLanguageIndex'
    params.action = 'verifyLanguageIndex'
    return await AuthorService.aReturnContent(params)
  },
  async verifyContentIndex(params) {
    params = this.initialize(params)
    params.page = 'verifyContentIndex'
    params.action = 'verifyContentIndex'
    var res = await AuthorService.aReturnContent(params)
    //console.log(res)
    return res
  },

  toFormData(obj) {
    var form_data = new FormData()
    for (var key in obj) {
      form_data.append(key, obj[key])
    }
    return form_data
  },
}
