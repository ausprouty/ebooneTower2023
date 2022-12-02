import Vue from 'vue'
import Router from 'vue-router'
import CountriesPreview from './views/CountriesPreview.vue'
import Login from './views/Login.vue'
import NotFoundComponent from './views/NotFound.vue'

Vue.use(Router)

function guardMyroute(to, from, next) {
  var isAuthenticated = false
  if (localStorage.getItem('user')) isAuthenticated = true
  else isAuthenticated = false
  if (isAuthenticated) {
    next() // allow to enter route
  } else {
    next('/login') // go to '/login';
  }
}

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/prototype',
      name: 'prototype',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "prototype" */ './views/Prototype.vue'
        )
      },
    },
    {
      path: '/apk/:country_code',
      name: 'apkMaker',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortCountries" */ './views/ApkMaker.vue'
        )
      },
    },
    {
      path: '/capacitor/:country_code',
      name: 'capacitorMaker',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortCountries" */ './views/CapacitorMaker.vue'
        )
      },
    },
    {
      path: '/',
      name: 'home',
      component: CountriesPreview,
    },
    {
      path: '/languages',
      name: 'languages',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "languages" */ './views/Languages.vue'
        )
      },
    },
    {
      path: '/compare/page/:country_code/:language_iso/:library_code/:folder_name/:filename/:cssFORMATTED',
      name: 'comparePage',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "comparePage" */ './views/PageCompare.vue'
        )
      },
      props: true,
    },

    {
      path: '/farm',
      name: 'farm',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "farm" */ './views/Register.vue')
      },
      props: true,
    },
    {
      path: '/edit/countries',
      name: 'editCountries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "editCountries" */ './views/CountriesEdit.vue'
        )
      },
    },
    {
      path: '/edit/languages/:country_code',
      name: 'editLanguages',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "editLanguages" */ './views/LanguagesEdit.vue'
        )
      },
      props: true,
    },
    {
      path: '/edit/libraryIndex/:country_code/:language_iso',
      name: 'editLibraryIndex',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "editLibraryIndex" */ './views/LibraryIndexEdit.vue'
        )
      },
    },
    {
      path: '/edit/library/:country_code/:language_iso/:library_code?',
      name: 'editLibrary',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "editLibrary" */ './views/LibraryEdit.vue'
        )
      },
      props: true,
    },
    {
      path: '/edit/series/:country_code/:language_iso/:library_code/:folder_name',
      name: 'editSeries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "editSeries" */ './views/SeriesEdit.vue'
        )
      },
      props: true,
    },
    {
      path: '/edit/page/:country_code/:language_iso/:library_code/:folder_name/:filename/:cssFORMATTED/:styles_set?',
      name: 'editPage',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "editPage" */ './views/PageEdit.vue')
      },
      props: true,
    },
    {
      path: '/preview',
      name: 'previewCountries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "previewCountries" */ './views/CountriesPreview.vue'
        )
      },
    },
    {
      path: '/preview/languages/:country_code',
      name: 'previewLanguages',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "previewLanguages" */ './views/LanguagesPreview.vue'
        )
      },
      props: true,
    },
    {
      path: '/preview/libraryIndex/:country_code/:language_iso',
      name: 'previewLibraryIndex',
      beforeEnter: guardMyroute,

      component: function () {
        return import(
          /* webpackChunkName: "previewLibraryIndex" */ './views/LibraryIndexPreview.vue'
        )
      },
    },
    {
      path: '/preview/library/:country_code/:language_iso/:library_code',
      name: 'previewLibrary',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "previewLibrary" */ './views/LibraryPreview.vue'
        )
      },
      props: true,
    },
    {
      path: '/preview/library2/:country_code/:language_iso/:library_code',
      name: 'previewLibrary2',
      beforeEnter: guardMyroute,

      component: function () {
        return import(
          /* webpackChunkName: "previewLibrary2" */ './views/LibraryPreview2.vue'
        )
      },
      props: true,
    },
    {
      path: '/preview/series/:country_code/:language_iso/:library_code/:folder_name',
      name: 'previewSeries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "previewSeries" */ './views/SeriesPreview.vue'
        )
      },
      props: true,
    },
    {
      path: '/preview/page/:country_code/:language_iso/:library_code/:folder_name/:filename',
      name: 'previewPage',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "previewPage" */ './views/PagePreview.vue'
        )
      },
      props: true,
    },
    {
      path: '/sdcard/:country_code',
      name: 'sdCardMaker',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortCountries" */ './views/SDCardMaker.vue'
        )
      },
    },
    {
      path: '/sort/countries',
      name: 'sortCountries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortCountries" */ './views/CountriesSort.vue'
        )
      },
    },
    {
      path: '/sort/languages/:country_code',
      name: 'sortLanguages',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortLanguages" */ './views/LanguagesSort.vue'
        )
      },
      props: true,
    },
    {
      path: '/sort/library/:country_code/:language_iso/:library_code/',
      name: 'sortLibrary',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortLibrary" */ './views/LibrarySort.vue'
        )
      },
      props: true,
    },
    {
      path: '/sort/series/:country_code/:language_iso/:library_code/:folder_name',
      name: 'sortSeries',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "sortSeries" */ './views/SeriesSort.vue'
        )
      },
      props: true,
    },
    {
      path: '/template/:country_code/:language_iso/:library_code/:title/:template/:cssFORMATTED/:styles_set/:book_code/:book_format',
      name: 'createTemplate',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "createTemplate" */ './views/Template.vue'
        )
      },
      props: true,
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      props: false,
    },
    {
      path: '/users',
      name: 'users',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "users" */ './views/Users.vue')
      },
      props: false,
    },
    {
      path: '/user/:uid',
      name: 'user',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "user" */ './views/User.vue')
      },
      props: true,
    },
    {
      path: '/test/generations',
      name: 'testgenerations',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "testGenerations" */ './tests/generations.vue'
        )
      },
      props: false,
    },
    {
      path: '/test/myfriends',
      name: 'testmyfriends',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "testMyfriends" */ './tests/myfriends.vue'
        )
      },
      props: false,
    },
    {
      path: '/test/mc2',
      name: 'testmc2',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "testmc2" */ './tests/mc2.vue')
      },
      props: false,
    },
    {
      path: '/test/sent67',
      name: 'testsent67',
      beforeEnter: guardMyroute,
      component: function () {
        return import(
          /* webpackChunkName: "testMyfriends" */ './tests/sent67.vue'
        )
      },
      props: false,
    },

    {
      path: '/validate',
      name: 'validate',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "validate" */ './views/Validate.vue')
      },
      props: false,
    },
    {
      path: '/admin',
      name: 'admin',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "admin" */ './views/Admin.vue')
      },
      props: true,
    },
    {
      path: '/upload',
      name: 'upload',
      beforeEnter: guardMyroute,
      component: function () {
        return import(/* webpackChunkName: "upload" */ './views/Upload.vue')
      },
      props: true,
    },
    {
      path: '*',
      component: NotFoundComponent,
    },
  ],
})
