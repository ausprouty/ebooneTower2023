import Vue from 'vue'
import Vuex from 'vuex'
//import { timeout } from 'q'
Vue.use(Vuex)

export const authorizeMixin = {
  //computed: mapState(['user']),
  methods: {
    authorize(reason, route) {
      //console.log('I am in in AuthorizeMixin')
      //console.log(reason)
      //console.log(route)
      var scopeCountries = localStorage.getItem('scopeCountries')
      //console.log(scopeCountries)
      var scopeLanguages = localStorage.getItem('scopeLanguages')
      //console.log(scopeLanguages)
      if (this.$route.path == '/login') {
        return true
      }
      var user = localStorage.getItem('uid', null)
      if (!user) {
        this.$router.push({ name: 'login' })
      }
      if (typeof route == 'undefined') {
        alert('no route')
        return false
      }
      // check if expired
      var date = new Date()
      var sessionExpires = localStorage.getItem('sessionExpires', 0)
      var timestamp = date.getTime() / 1000
      if (sessionExpires < timestamp) {
        this.$router.push({ name: 'login' })
      }
      // can edit anything
      if (scopeCountries == '|*|' && scopeLanguages == '|*|') {
        //console.log('I am superuser')
        if (reason != 'readonly') {
          //console.log ('I am returning true')
          return true
        } else {
          //console.log ('I am returning false')
          return false
        }
      } else {
        //console.log('I am not superuser')
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

      // can edit this langauge in this country
      if (
        scopeCountries.includes(route.country_code) &&
        scopeLanguages.includes(route.language_iso)
      ) {
        //console.log('Can edit this language in this country')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      // can edit anything in country
      if (
        scopeCountries.includes(route.country_code) &&
        scopeLanguages == '|*|'
      ) {
        //console.log('Can edit anything in this country')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }

      // can edit anything in this language
      if (
        scopeCountries == '|*|' &&
        scopeLanguages.includes(route.language_iso)
      ) {
        //console.log('Can edit anything in this language')
        if (reason != 'readonly') {
          return true
        } else {
          return false
        }
      }
      //console.log('Fell through to bottom')
      // can only read
      if (reason == 'readonly') {
        return true
      }
      return false
    },
  },
}
