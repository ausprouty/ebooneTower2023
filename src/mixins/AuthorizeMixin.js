import Vue from 'vue'
import Vuex from 'vuex'
import store from '@/store/store.js'
//import { mapState } from 'vuex'
//import { timeout } from 'q'
Vue.use(Vuex)

export const authorizeMixin = {
  //computed: mapState(['user']),
  methods: {
    authorize(reason, route) {
      if (this.$route.path == '/login') {
        return true
      }
      if (!localStorage.getItem('user')) {
        this.$router.push({ name: 'login' })
      }
      var user = JSON.parse(localStorage.getItem('user'))
      if (typeof route == 'undefined') {
        alert('no route')
        return false
      }
      // check if expired
      var date = new Date()
      var timestamp = date.getTime() / 1000
      if (user.expires < timestamp) {
        this.$router.push({ name: 'login' })
      }
      // can edit anything
      if (user.scope_countries == '|*|' && user.scope_languages == '|*|') {
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      // check route
      if (typeof route.country_code === 'undefined') {
        route.country_code = 'undefined'
      }
      if (typeof route.language_iso === 'undefined') {
        route.language_iso = 'undefined'
      }
      // check authority
      if (reason == 'read') {
        return true
      }
      console.log(user)
      console.log(route)
      // can edit this langauge in this country
      if (
        user.scope_countries.includes(route.country_code) &&
        user.scope_languages.includes(route.language_iso)
      ) {
        console.log('Can edit this language in this country')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      // can edit anything in country
      if (
        user.scope_countries.includes(route.country_code) &&
        user.scope_languages == '|*|'
      ) {
        console.log('Can edit anything in this country')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      // can edit anything in this language
      if (
        (user.scope_countries =
          '|*|' && user.scope_languages.includes(route.language_iso))
      ) {
        console.log('Can edit anything in this language')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      console.log('Fell through to bottom')
      // can only read
      if (reason == 'readonly') {
        return true
      }
      return false
    },
  },
}
