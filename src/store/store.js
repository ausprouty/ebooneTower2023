import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import { saveStatePlugin } from '@/utils.js' // <-- Import saveStatePlugin
import { add } from 'lodash'
Vue.config.devtools = true
Vue.use(Vuex)
export default new Vuex.Store({
  plugins: [saveStatePlugin], // <-- Use
  state: {
    baseURL: './',
    bookImages: [],
    bookStyleSheets: [],
    bookTemplates: [],
    bookmark: {
      country: {},
      language: {},
      library: {
        books: [],
        format: {
          back_button: {
            image: null,
            title: null,
          },
          custom: null,
          image: {
            image: null,
            title: null,
          },
          no_ribbon: true,
          replace_header: true,
          style: null,
        },
        text: null,
      },
    },
    ckEditorStyleSets: [],
    content: {
      recnum: null,
      version: '1.1',
      edit_date: null,
      edit_uid: null,
      publish_uid: null,
      publish_date: null,
      language_iso: null,
      country_code: null,
      folder: null,
      filetype: null,
      title: null,
      filename: null,
      text: null,
    },
    cssURL: './content/',
    languages: [],
    revision: '2.2',

    standard: {
      ckEditStyleSets: [
        'compass',
        'firststeps',
        'generations',
        'mc2',
        'multiply',
        'myfriends',
        'sent67',
        'train',
      ],
    },
    sdCardSettings: {
      languages: [],
      footer: null,
      external_links: false,
      action: 'sdcard',
      series: null,
      subDirectory: null,
    },
    user: {
      authorized: false,
      expires: 0,
      firstname: '',
      lastname: '',
      scope_countries: '',
      scope_languages: '',
      start_page: '',
      token: '',
      uid: '',
    },
  },

  getters: {
    getBookProperty: (state) => (index, property) => {
      if (state.bookmark.library.books[index]) {
        return state.bookmark.library.books[index][property]
      }
      return null // or undefined, or any default value you'd like
    },
    getCkEditStyleSets: (state) => {
      return state.standard.ckEditStyleSets
    },
    getLanguageImageDirectory: (state) => {
      return state.bookmark.language.image_dir
    },
    getLibraryBook: (state, id) => {
      return state.bookmark.library.books[id]
    },
    getLibraryBookTitle: (state, id) => {
      var book = state.bookmark.library.books[id]
      if (typeof book != 'undefined') {
        return book.title
      } else {
        return null
      }
    },
    // Vuex getter (no complex logic, just return the books array)
    getLibraryBooks: (state) => {
      console.log('getLibraryBooks recalculating')
      return state.bookmark.library.books
    },

    getLibraryBookCodes: (state) => {
      return state.bookmark.library.books.map((book) => book.code).sort()
    },
    
    getLibraryFormatBackButton: (state) => {
      return state.bookmark.library.format.back_button
    },
    getLibraryFormatCustom: (state) => {
      if (typeof state.bookmark.library.format.custom == 'undefined') {
        return null
      } else {
        return state.bookmark.library.format.custom
      }
    },
    getLibraryFormatImage: (state) => {
      return state.bookmark.library.format.image
    },
    getLibraryFormatNoRibbon: (state) => {
      return state.bookmark.library.format.no_ribbon
    },
    getLibraryFormatReplaceHeader: (state) => {
      if (
        typeof state.bookmark.library.format.getLibraryFormatReplaceHeader ==
        'undefined'
      ) {
        return null
      } else {
        return state.bookmark.library.format.replace_header
      }
    },
    getLibraryFormatStyle: (state) => {
      if (typeof state.bookmark.library.format.style == 'undefined') {
        return null
      } else {
        return state.bookmark.library.format.style
      }
    },
    getLibraryText: (state) => {
      return state.bookmark.library.text
    },
    getLibraryProperty: (state) => (path) => {
      const properties = path.split('.')
      let result = state.bookmark.library

      for (const prop of properties) {
        if (result && typeof result[prop] !== 'undefined') {
          result = result[prop]
        } else {
          return null // Return null if any part of the path is undefined
        }
      }

      return result
    },
  },
  mutations: {
    addBook(state, book) {
      const updatedBooks = [...state.bookmark.library.books, book]
      // Chain setLibraryBooks to replace the entire array
      this.commit('setLibraryBooks', updatedBooks)
    },
    addNewLibraryBookCode(state, newCode) {
      state.bookmark.library.books.push({ code: newCode })
      console.log(`New book code added: ${newCode}`)
    },
    removeBook(state, index) {
      state.bookmark.library.books.splice(index, 1)
    },
    setAllBooksPrototypeToFalse(state) {
      state.bookmark.library.books.forEach((book) => {
        book.prototype = false
      })
    },
    setAllBooksPrototypeToTrue(state) {
      state.bookmark.library.books.forEach((book) => {
        book.prototype = true
      })
    },
    setAllBooksPublishToFalse(state) {
      state.bookmark.library.books.forEach((book) => {
        book.publish = false
      })
    },
    setAllBooksPublishToTrue(state) {
      state.bookmark.library.books.forEach((book) => {
        book.publish = true
      })
    },
    setBookCkEditorStyleSelected: (state, { index, value }) => {
      state.bookmark.library.books[index].ckEditorStyleSelected = value
    },
    setBookProperty: (state, { index, property, value }) => {
      if (state.bookmark.library.books[index]) {
        Vue.set(state.bookmark.library.books[index], property, value)
      }
    },
    setBookCode: (state, payload) => {
      const { index, value } = payload
      state.bookmark.library.books[index].code = value
    },
    setBookFormat: (state, { index, formatType }) => {
      state.bookmark.library.books[index].format = formatType
    },
    setBookImage: (state, { index, image }) => {
      state.bookmark.library.books[index].image = image
    },
    setBookImages(state, images) {
      state.bookImages = images
    },
    updateBookImages(state, image) {
      state.bookImages.push(image)
    },
    setBookPermissionHide(state, { index, value }) {
      state.bookmark.library.books[index].hide = value
    },
    setBookPermissionPassword(state, { index, value }) {
      state.bookmark.library.books[index].password = value
    },
    setBookPermissionPrototype(state, { index, value }) {
      state.bookmark.library.books[index].prototype = value
    },
    setBookPermissionPublish(state, { index, value }) {
      state.bookmark.library.books[index].publish = value
    },
    setBookStyleSheets(state, styleSheets) {
      state.bookStyleSheets = styleSheets
    },
    updateBookStyleSheets(state, styleSheet) {
      state.bookStyleSheets.push(styleSheet)
    },
    setBookTemplate: (state, { index, value }) => {
      state.bookmark.library.books[index].template = value
    },
    setBookTemplates(state, templates) {
      state.bookTemplates = templates
    },
    setBookTitle: (state, { index, value }) => {
      state.bookmark.library.books[index].title = value
    },
    setCkEditorStyleSets(state, styles) {
      state.ckEditorStyleSets = styles
    },
    setLanguageImageDirectory: (state, selectedDirectory) => {
      state.bookmark.language.image_dir = selectedDirectory
    },
    setLibraryBookCode(state, { index, code }) {
      if (!state.bookmark.library.books[index]) {
        console.warn(`Book at index ${index} not found.`)
        return
      }
      // Use Vue.set to ensure reactivity
      Vue.set(state.bookmark.library.books[index], 'code', code)
      //console.log(`Book code set to ${code} at index ${index}.`);
    },
    setLibraryBooks(state, books) {
      console.log('setLibraryBooks called:', books)
      state.bookmark.library.books = books
    },
    setLibraryBookCodes: (state, value) => {
      state.libraryBookCodes = value
    },
    setLibraryFormatBackButton: (state, value) => {
      state.bookmark.library.back_button = value
    },
    setLibraryFormatCustom: (state, value) => {
      state.bookmark.library.format.custom = value
    },
    setLibraryFormatImage: (state, selectedImage) => {
      state.bookmark.library.format.image = selectedImage
    },
    setLibraryFormatNoRibbon: (state, value) => {
      state.bookmark.library.format.no_ribbon = value
    },
    setLibraryFormatStyle: (state, value) => {
      state.bookmark.library.format.style = value
    },
    setLibraryFormatReplaceHeader: (state, value) => {
      state.bookmark.library.format.getLibraryFormatReplaceHeader = value
    },
    setLibraryText: (state, value) => {
      state.bookmark.library.text = value
    },
    SET_LANGUAGES(state, value) {
      state.languages = value[0]
    },
    SET_APK(state, value) {
      state.apk = value
    },
    SET_SDCARD_SETTINGS(state, value) {
      state.sdCardSettings = value
    },
    SET_CAPACITOR_SETTINGS(state, value) {
      state.capacitorSettings = value
    },
    SET_USER_DATA(state, userData) {
      state.user = userData
      axios.defaults.headers.common[
        'Authorization'
      ] = `Bearer ${userData.token}`
    },
    NEW_BOOKMARK(state) {
      //console.log('STORE - NEW BOOKMARK    ')
      state.bookmark = {}
    },
    UPDATE_ALL_BOOKMARKS(state, value) {
      //console.log('STORE - UPDATE ALL BOOKMARKS    ')
      state.bookmark = {}
      if (typeof value.country != 'undefined') {
        state.bookmark.country = value.country
      }
      if (typeof value.language != 'undefined') {
        state.bookmark.language = value.language
      }
      if (typeof value.library != 'undefined') {
        state.bookmark.library = value.library
      }
      if (typeof value.series != 'undefined') {
        state.bookmark.series = value.series
      }
      if (typeof value.book != 'undefined') {
        state.bookmark.book = value.book
      }
      if (typeof value.page != 'undefined') {
        state.bookmark.page = value.page
      }
      //console.log('state.bookmark')
      //console.log(state.bookmark)
    },
    SET_BOOKMARK(state, [mark, value]) {
      switch (mark) {
        case 'country':
          state.bookmark.country = value
          break
        case 'language':
          state.bookmark.language = value
          break
        case 'library':
          state.bookmark.library = value
          break
        case 'book':
          state.bookmark.book = value
          break
        case 'series':
          state.bookmark.series = value
          break
        case 'page':
          state.bookmark.page = value
          break
      }
    },
    UNSET_BOOKMARK(state, [mark]) {
      switch (mark) {
        case 'country':
          state.bookmark.country = {
            code: 'au',
            english: '',
            name: '',
            index: '',
            image: '',
          }
          break
        case 'language':
          state.bookmark.language = {
            id: '',
            folder: '',
            iso: '',
            name: '',
            bible: '',
            life_issues: '',
            image_dir: '',
            nt: '',
            ot: '',
            rldir: '',
          }
          break
        case 'library':
          state.bookmark.library = []
          break
        case 'book':
          state.bookmark.book = {
            book: '',
            folder: '',
            format: '',
            id: '',
            image: '',
            index: '',
            instructions: '',
            title: '',
          }
          break
        case 'series':
          state.bookmark.series = {
            series: '',
            language: '',
            description: 'This means not set',
            chapters: [],
          }
          break
        case 'page':
          state.bookmark.page = ''
          break
      }
    },
  },
  actions: {
    updateAllBookmarks({ commit }, value) {
      commit('UPDATE_ALL_BOOKMARKS', value)
    },
    newBookmark({ commit }, value) {
      commit('UNSET_BOOKMARK', [value])
    },
    updateBookmark({ commit }, [mark, value]) {
      commit('SET_BOOKMARK', [mark, value])
    },
    unsetBookmark({ commit }, [mark]) {
      commit('UNSET_BOOKMARK', [mark])
    },
    setLanguages({ commit }, [value]) {
      commit('SET_LANGUAGES', [value])
    },
    setSDCardSettings({ commit }, value) {
      commit('SET_SDCARD_SETTINGS', value)
    },
    setCapacitorSettings({ commit }, value) {
      commit('SET_CAPACITOR_SETTINGS', value)
    },
    setApk({ commit }, value) {
      commit('SET_APK', value)
    },
  },
})
