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
      //console.log('this is the authorizeMixin speaking')
      //console.log(reason)
      //console.log(this.$store.state.user)
      //console.log(store.state.user)
      if (this.$route.path == '/login') {
        return true
      }
      // if you did not follow the router you can restore by using localStorage
      if (typeof store.state.user.scope_countries == 'undefined') {
        alert('I am restoring user state from localstorage')
        if (localStorage.getItem('user')) {
          var user = JSON.parse(localStorage.getItem('user'))
          if (user != 'error') {
            this.$store.dispatch('loginUser', [user])
          } else {
            this.$router.push({ name: 'login' })
          }
        } else {
          this.$router.push({ name: 'login' })
        }
      }
      if (typeof route == 'undefined') {
        alert('no route')
        return false
      }
      // check if expired
      var date = new Date()
      var timestamp = date.getTime() / 1000
      if (store.state.user.expires < timestamp) {
        //console.log('You have been timed out. Please log in again')
        this.$router.push({ name: 'login' })
      }
      // can edit anything
      if (
        store.state.user.scope_countries == '|*|' &&
        store.state.user.scope_languages == '|*|'
      ) {
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
      // check for legacy errors
      if (typeof store.state.user.scope_countries === 'undefined') {
        alert('I am reassigning scope countries')
        store.state.user.scope_countries = 'undefined'
      }
      if (typeof store.state.user.scope_languages === 'undefined') {
        store.state.user.scope_languages = 'undefined'
        alert('I am reassigning scope languages')
      }
      // check authority
      if (reason == 'read') {
        return true
      }
      console.log(store.state.user)
      console.log(route)
      // can edit this langauge in this country
      if (
        store.state.user.scope_countries.includes(route.country_code) &&
        store.state.user.scope_languages.includes(route.language_iso)
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
        store.state.user.scope_countries.includes(route.country_code) &&
        store.state.user.scope_languages == '*'
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
        (store.state.user.scope_countries =
          '*' && store.state.user.scope_languages.includes(route.language_iso))
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
