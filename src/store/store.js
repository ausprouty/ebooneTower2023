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
    sdCardSettings: {
      languages: [],
      footer: null,
      external_links: false,
      action: 'sdcard',
      series: null,
      subDirectory: null,
    },

    revision: '2.0',
    baseURL: './',
    cssURL: './content/',
    standard: {
      items: ['about', 'basics', 'community', 'compass', 'life', 'steps'],
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
      library: [
        {
          id: '',
          book: '',
          title: '',
          folder: '',
          index: '',
          style: process.env.VUE_APP_SITE_STYLE,
          image: process.env.VUE_APP_SITE_IMAGE,
          format: 'series',
          pages: 1,
          instructions: '',
        },
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
  },
  getters: {
    getLanguageImageDirectory: (state) => {
      return state.bookmark.language.image_dir
    },
    getLibraryBook: (state) => {
      return state.bookmark.library.books
    },
    getLibraryFormatBackButton: (state) => {
      return state.bookmark.library.format.back_button
    },
    getLibraryFormatCustom: (state) => {
      return state.bookmark.library.format.custom
    },
    getLibraryFormatImage: (state) => {
      return state.bookmark.library.format.VUE_APP_SITE_IMAGE
    },
    getLibraryFormatNoRibbon: (state) => {
      return state.bookmark.library.format.no_ribbon
    },
    getLibraryFormatReplaceHeader: (state) => {
      return state.bookmark.library.format.replace_header
    },
    getLibraryFormatStyle: (state) => {
      return state.bookmark.library.format.getLibraryFormatStyle
    },
    getLibraryText: (state) => {
      return state.bookmark.library.text
    },
  },
  mutations: {
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
