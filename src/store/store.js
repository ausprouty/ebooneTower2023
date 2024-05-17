import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import { saveStatePlugin } from '@/utils.js' // <-- Import saveStatePlugin
Vue.config.devtools = true
Vue.use(Vuex)
export default new Vuex.Store({
  plugins: [saveStatePlugin], // <-- Use
  state: {
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
    imagesForBooks: [],
    revision: '2.0',
    baseURL: './',
    cssURL: './content/',
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
    languages: [],
    sdCardSettings: {
      languages: [],
      footer: null,
      external_links: false,
      action: 'sdcard',
      series: null,
      subDirectory: null,
    },
  },

  getters: {
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
    getLibraryBooks: (state) => {
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
  },
  mutations: {
    addNewLibraryBookCode(state, code) {
      state.bookmark.library.books.push({ code })
    },
    setBookCode: (state, payload) => {
      const { index, value } = payload
      state.bookmark.library.books[index].code = value
    },
    setBookTitle: (state, payload) => {
      const { index, value } = payload
      state.bookmark.library.books[index].title = value
    },
    setImagesForBooks(state, images) {
      state.imagesForBooks = images
    },
    setLanguageImageDirectory: (state, selectedDirectory) => {
      state.bookmark.language.image_dir = selectedDirectory
    },
    setLibraryBookCode(state, { index, code }) {
      if (state.bookmark.library.books[index]) {
        Vue.set(state.bookmark.library.books[index], 'code', code)
      }
    },
    setLibraryBooks: (state, selectedBooks) => {
      state.bookmark.library.books = selectedBooks
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
