import axios from 'axios'
import store from '@/store/store.js'
import LogService from '@/services/LogService.js'

const log = process.env.VUE_APP_SITE_SHOW_CONSOLE_LOG
const apiURL = process.env.VUE_APP_DEFAULT_SITES_URL
const apiSite = process.env.VUE_APP_SITE
const apiLocation = process.env.VUE_APP_SITE_LOCATION
//const apiLocation = 'author'
const postDestination =
  'AuthorApi.php?site=' + apiSite + '&location=' + apiLocation
const apiSELECT = axios.create({
  baseURL: apiURL,
  withCredentials: false, // This is the default
  crossDomain: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})
const apiIMAGE = axios.create({
  baseURL: apiURL,
  withCredentials: false, // This is the default
  crossDomain: true,
  headers: {
    'Content-Type': 'multipart/form-data',
  },
})
// I want to export a JSON.stringified of response.data.text
export default {
  consoleLog(params, response) {
    if (log == true) {
      if (params.action !== 'XloginX') {
        console.log(params)
        console.log(response)
      }
    }
  },
  async aReturnResponse(params) {
    try {
      var contentForm = this.toAuthorizedFormData(params)
      var response = await apiSELECT.post(postDestination, contentForm)
      params.function = 'aReturnContentParsed'
      LogService.consoleLog('aReturnResponse', params, response)
      if (response.data.login) {
        this.$router.push({
          name: 'login',
        })
      } else {
        if (response.data.status == 'error') {
          const thisError = params.action + ':' + response.data.error
          console.log(thisError)
          return 'error'
        }
        return response.data.result
      }
    } catch (error) {
      const thisError = error.toString() + ' ' + params.action
      LogService.consoleLogError(
        'something went wrong when AuthorService was called',
        thisError,
        'aReturnResponse'
      )
      return 'error'
    }
  },
  async aReturnContent(params) {
    try {
      var content = {}
      var contentForm = this.toAuthorizedFormData(params)
      var response = await apiSELECT.post(postDestination, contentForm)
      params.function = 'aReturnContent'
      console.log(params)
      console.log(response)
      if (response.data.login) {
        this.$router.push({
          name: 'login',
        })
      }
      if (typeof response.data !== 'undefined') {
        if (response.data.status == 'error') {
          const thisError = params.action + ':' + response.data.error
          console.log(thisError)
          return 'error'
        }
        //console.log(' have data content')
        content = response.data.result
      } else if (typeof response !== 'undefined') {
        //console.log(' have  content')
        content = response
      }
      //this.clearActionAndPage()
      return content
    } catch (error) {
      const thisError = error.toString() + ' ' + params.action
      LogService.consoleLogError(
        'something went wrong',
        thisError,
        'aReturnContent'
      )
      return 'error'
    }
  },
  async aReturnContentParsed(params) {
    try {
      var parse = {}
      var contentForm = this.toAuthorizedFormData(params)
      var response = await apiSELECT.post(postDestination, contentForm)
      console.log(params.action)
      console.log(response)
      if (response.data.login) {
        this.$router.push({
          name: 'login',
        })
      }
      console.log(response.data)
      console.log(response.data.result)
      if (response.data.status == 'error') {
        const thisError = params.action + ':' + response.data.error
        console.log(thisError)
        return 'error'
      }
      if (response.data.result) {
        if (typeof response.data.result === 'string') {
          parse = JSON.parse(response.data.result)
        } else if (Array.isArray(response.data.result)) {
          parse = response.data.result
        } else {
          console.warn(
            'Unexpected type for result:',
            typeof response.data.result
          )
        }
      }
      return parse
    } catch (error) {
      const thisError = error.toString() + ' ' + params.action
      console.log(thisError)
      LogService.consoleLogError(
        'something went wrong',
        thisError,
        'aReturnContentParsed'
      )
      return 'error in aReturnContentParsed'
    }
  },
  async aReturnData(params) {
    try {
      var data = {}
      var contentForm = this.toAuthorizedFormData(params)
      var response = await apiSELECT.post(postDestination, contentForm)
      console.log('aReturnData')
      console.log(params.action)
      console.log(response)
      params.function = 'aReturn'
      this.consoleLog(params, response)
      if (response.data.login) {
        this.$router.push({
          name: 'login',
        })
      }
      if (response.data.status == 'error') {
        const thisError = params.action + ':' + response.data.error
        console.log(thisError)
        return 'error'
      }
      if (response.data) {
        data = response.data
      }

      console.log(data)
      return data
    } catch (error) {
      const thisError = error.toString() + ' ' + params.action
      LogService.consoleLogError(
        'something went Wrong',
        thisError,
        'aReturnData'
      )
      return 'error'
    }
  },
  async bibleUpdateABS(params) {
    params.page = 'bibleUpdateABS'
    params.action = 'bibleUpdateABSJson'
    return await this.aReturnResponse(params)
  },
  async bibleABSnew(params) {
    params.page = 'bibleUpdateABS'
    params.action = 'bibleABSnew'
    return await this.aReturnResponse(params)
  },
  async bibleUpdateDBT(params) {
    params.page = 'bibleUpdateDBT'
    params.action = 'bibleUpdateDBT'
    return await this.aReturnResponse(params)
  },

  async bibleCheckDBTIndex(params) {
    params.page = 'bibleUpdateDBT'
    params.action = 'bibleCheckDBTIndex'
    return await this.aReturnResponse(params)
  },
  async bibleCheckDBTDetail(params) {
    params.page = 'bibleUpdateDBT'
    params.action = 'bibleCheckDBTDetail'
    return await this.aReturnResponse(params)
  },
  async bookmark(params) {
    if (params.scope == 'countries') {
      store.dispatch('newBookmark', null)
      return
    }
    params.page = 'bookmark'
    params.action = 'bookmark'
    var content = await this.aReturnContent(params)
    if (content) {
      store.dispatch('updateAllBookmarks', content)
    } else {
      store.dispatch('newBookmark', null)
    }
    return content
  },
  bookmarkClear() {
    store.dispatch('newBookmark', null)
  },
  async checkCache(params) {
    params.page = 'publicationCache'
    params.action = 'checkCache'
    return await this.aReturnData(params)
  },

  clearActionAndPage() {
    this.$route.params.page = null
    this.$route.params.action = null
  },
  async copyBook(params) {
    params.page = 'sql'
    params.action = 'copyBook'
    return await this.aReturnResponse(params)
  },
  async createContentData(params) {
    var d = new Date()
    params.edit_date = d.getTime()
    params.page = 'create'
    params.action = 'createContent'
    var res = await this.aReturnData(params)
    console.log(res)
    return res
  },
  async createContentFolder(params) {
    params.page = 'create'
    params.action = 'createContentFolder'
    return await this.aReturnResponse(params)
  },
  // languages is an array of language objects
  async createDirectoryLanguages(content) {
    var code = ''
    var route = JSON.parse(content.route)
    var arrayLength = content.length
    for (var i = 0; i < arrayLength; i++) {
      code = content[i].iso
      if (this.isFilename(code)) {
        var params = {}
        params.page = 'create'
        params.action = 'createDirectoryLanguages'
        params.scope = 'language'
        params.country = route.country_code
        //this.consoleLog(params, 'Creating language directory for ' + code)
        params.code = code
        await this.aReturnResponse(params)
      }
    }
  },
  async createDirectoryMenu(country, language) {
    if (this.isFilename(language)) {
      var params = {}
      params.language_iso = language
      params.country_code = country
      params.token = localStorage.getItem('token')
      params.page = 'create'
      params.action = 'createDirectoryMenu'
      return await this.aReturnResponse(params)
    } else {
      return 'Language not acceptable: ' + language
    }
  },

  async createSeriesIndex(params) {
    params.page = 'create'
    params.action = 'createSeriesIndex'
    return await this.aReturnResponse(params)
  },

  async createStyle(params) {
    if (this.isFilename(params.file.name)) {
      params.page = 'create'
      params.action = 'createStyle'
      return await this.aReturnResponse(params)
    } else {
      return 'Filename not acceptable: ' + params.file.name
    }
  },
  async createTemplate(params) {
    if (this.isFilename(params.file.name)) {
      params.page = 'create'
      params.action = 'createTemplate'
      return await this.aReturnResponse(params)
    } else {
      return 'Filename not acceptable: ' + params.file.name
    }
  },
  /////////////////////////////////////////////////
  async debug(params) {
    params.page = 'debug'
    return await this.aReturnResponse(params)
  },
  ////////////////////////////////////////////////
  async deleteUser(params) {
    params.page = 'deleteUser'
    params.action = 'deleteUser'
    return await this.aReturnResponse(params)
  },
  async editTemplate(params) {
    params.page = 'editTemplate'
    params.action = 'editTemplate'
    return await this.aReturnResponse(params)
  },
  async getComparisons(params) {
    params.page = 'getComparisons'
    params.action = 'getComparisons'
    return await this.aReturnContent(params)
  },
  async getFoldersContent(params) {
    params.page = 'get'
    params.action = 'getFoldersContent'
    var folders = await this.aReturnContentParsed(params)
    return folders
  },
  async getContentFoldersForLanguage(params) {
    params.page = 'getContentFoldersForLanguage'
    params.action = 'getContentFoldersForLanguage'
    var folders = await this.aReturnContent(params)
    return folders
  },
  async getCurrentBooks() {
    var params = {}
    params.page = 'getCurrentBooks'
    params.action = 'getCurrentBooks'
    return await this.aReturnResponse(params)
  },
  async getCurrentLanguages() {
    var params = {}
    params.page = 'getCurrentLanguages'
    params.action = 'getCurrentLanguages'
    return await this.aReturnResponse(params)
  },
  async getFoldersImages() {
    var params = {}
    params.page = 'getFoldersImages'
    params.action = 'getFoldersImages'
    let folders = []
    folders = await this.aReturnContentParsed(params)
    if (folders.length > 0) {
      folders.sort()
    }

    return folders
  },
  async getImagesForSite(directory) {
    var params = {}
    params.page = 'getImagesForSite'
    params.action = 'getImagesForSite'
    params.image_dir = directory
    var content = await this.aReturnContent(params)
    //if (content) {
    //  var images = JSON.parse(content)
    //}
    return content
  },
  async getImagesInContentDirectories(directories) {
    // var images = {}
    var params = {}
    params.image_dirs = directories
    params.page = 'getImagesInContentDirectories'
    params.action = 'getImagesInContentDirectories'
    var content = await this.aReturnContent(params)
    //console.log('content from getImagesInContentDirectories')
    //console.log(content)
    //if (content) {
    //  images = JSON.parse(content)
    //}
    return content
  },
  async getImagesInContentDirectory(directory) {
    var params = {}
    params.image_dir = directory
    params.page = 'getImagesInContentDirectory'
    params.action = 'getImagesInContentDirectory'
    var content = await this.aReturnContent(params)
    //if (content) {
    // images = JSON.parse(content)
    //}
    return content
  },
  async getLanguageOptionsForEditors(params) {
    params.page = 'getLanguageOptionsForEditors'
    params.action = 'getLanguageOptionsForEditors'
    return this.aReturnContent(params)
  },
  async getLanguagesAvailable(params) {
    params.page = 'getLanguagesAvailable'
    params.action = 'getLanguagesAvailable'
    return this.aReturnContent(params)
  },
  async getLanguagesForAuthorization(params) {
    params.page = 'getLanguagesForAuthorization'
    params.action = 'getLanguagesForAuthorization'
    return this.aReturnContent(params)
  },
  async getLatestContent(params) {
    params.page = 'getLatestContent'
    params.action = 'getLatestContent'
    return this.aReturnContentParsed(params)
  },
  async getPage(params) {
    if (typeof params.template != 'undefined') {
      delete params.template
    }
    params.page = 'getPageOrTemplate'
    params.action = 'getPageOrTemplate'
    return this.aReturnData(params)
  },
  async getPageOrTemplate(params) {
    params.page = 'getPageOrTemplate'
    params.action = 'getPageOrTemplate'
    return this.aReturnData(params)
  },

  async getStyles(params) {
    params.page = 'getStyles'
    params.action = 'getStyles'
    return this.aReturnContentParsed(params)
  },
  async getCkEditStyleSets(params) {
    params.page = 'getCkEditStyleSets'
    params.action = 'getCkEditStyleSets'
    return await this.aReturnContent(params)
  },
  async getTemplate(params) {
    params.page = 'get'
    params.action = 'getTemplate'
    return this.aReturnContent(params)
  },
  async getTemplates(params) {
    params.page = 'get'
    params.action = 'getTemplates'
    console.log('AuthorService.getTemplates')
    console.log(params)
    var templates = this.aReturnContentParsed(params)
    console.log(templates)
    return templates
  },
  async getUser(params) {
    params.page = 'getUser'
    params.action = 'getUser'
    return this.aReturnContentParsed(params)
  },
  async getUsers(params) {
    params.page = 'getUsers'
    params.action = 'getUsers'
    return this.aReturnContentParsed(params)
  },

  isFilename(s) {
    return s.match('^[a-zA-Z0-9-_.]+$')
  },
  isFoldername(s) {
    return s.match('^[a-zA-Z0-9-_/]+$')
  },

  async login(params) {
    params.action = 'login'
    var check = await this.aReturnData(params)
    console.log(check)
    return check
  },
  async prototype(params) {
    params.page = 'prototype'
    params.action = 'prototype' + params.actionCODE
    return this.aReturnResponse(params)
  },
  async publish(params) {
    params.page = 'publish'
    params.action = 'publish' + params.actionCODE
    return this.aReturnResponse(params)
  },
  async revert(params) {
    params.page = 'revert'
    params.action = 'revert'
    return this.aReturnData(params)
  },
  async registerUser(params) {
    params.page = 'register'
    params.action = 'registerUser'
    return this.aReturnResponse(params)
  },
  async setupCountries(countries) {
    var code = ''
    var res = {}
    var arrayLength = countries.length
    //console.log(countries)
    for (var i = 0; i < arrayLength; i++) {
      code = countries[i].code
      if (code.length == 2) {
        if (this.isFilename(code)) {
          var params = {}
          params.country_code = code
          params.page = 'setupCountry'
          params.action = 'setupCountry'
          res = await this.aReturnResponse(params)
        }
      }
    }
    return res // returns data ='success'
  },
  async setupLanguageFolder(params) {
    params.page = 'setupLanguageFolder'
    params.action = 'setupLanguageFolder'
    return this.aReturnResponse(params)
  },
  async setupImageFolder(params) {
    params.page = 'setup'
    params.action = 'setupImageFolder'
    return this.aReturnResponse(params)
  },
  async setupSeries(params, file) {
    params.page = 'setupSeries'
    params.action = 'setupSeries'
    var contentForm = this.toAuthorizedFormData(params)
    contentForm.append('file', file)
    return apiIMAGE.post(postDestination, contentForm)
  },
  async updateVideoLinks(params) {
    params.page = 'updateVideoLinks'
    params.action = 'updateVideoLinks'
    return this.aReturnContent(params)
  },

  async imageStore(params, image) {
    params.page = 'image'
    params.action = 'imageStore'
    console.log('I am want to store images')
    var contentForm = this.toAuthorizedFormData(params)
    contentForm.append('file', image)
    console.log('I am storing images')
    console.log(params)
    var response = await apiIMAGE.post(postDestination, contentForm)
    console.log(response)
    return response
  },
  typeImage(filetype) {
    const types = {
      'image/jpeg': '.jpg',
      'image/png': '.png',
      'image/gif': '.gif',
    }
    return types[filetype] || null
  },
  typeStyle(file) {
    var type = null
    var filetype = file['type']
    switch (filetype) {
      case 'text/css':
        type = '.css'
        break
      default:
    }
    return type
  },
  typeTemplate(file) {
    var type = null
    var filetype = file['type']
    switch (filetype) {
      case 'text/html':
        type = '.html'
        break
      default:
    }
    return type
  },

  toAuthorizedFormData(params) {
    params.my_uid = localStorage.getItem('uid')
    params.token = localStorage.getItem('token')
    params.expires = localStorage.getItem('expires')
    params.site = apiSite
    var form_data = new FormData()
    for (var key in params) {
      form_data.append(key, params[key])
    }
    // Display the key/value pairs
    // for (var pair of form_data.entries()) {
    //  this.consoleLog(params, pair[0] + ', ' + pair[1])
    //}
    //this.consoleLog(params, form_data)
    return form_data
  },
  async updateUser(params) {
    params.page = 'updateUser'
    params.action = 'updateUser'
    return this.aReturnResponse(params)
  },
}
