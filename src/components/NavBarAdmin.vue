<template>
  <div id="nav">
    <link rel="stylesheet" href="/sites/default/styles/appGLOBAL.css" />
    <link rel="stylesheet" href="/sites/default/styles/cardGLOBAL.css" />
    <link rel="stylesheet" v-bind:href="standardCSS" />

    <div v-on:click="toggleMenu()">
      <img v-bind:src="this.headerImage" class="nav-icon" alt="Home" />
    </div>
    <div v-if="showMenu">
      <div
        v-for="menuItem in this.menu"
        :key="menuItem.link"
        :menuItem="menuItem"
      >
        <div class="menu-card -shadow" v-if="menuItem.show">
          <div
            class="float-left"
            style="cursor: pointer"
            @click="setNewSelectedOption(menuItem.link)"
          >
            {{ menuItem.value }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import LogService from '@/services/LogService.js'
export default {
  computed: mapState(['bookmark', 'user']),
  props: ['called_by'],
  mixins: [authorizeMixin],
  data() {
    return {
      headerImage: process.env.VUE_APP_SITE_MENU_DIR + 'header-admin.png',
      showMenu: false,
      authorized: false,
      administrator: false,
      standardCSS: process.env.VUE_APP_SITE_STYLE,
      menu: [
        {
          value: 'Login',
          link: 'login',
          index: 0,
          show: false,
        },
        {
          value: 'Countries',
          link: 'countries',
          index: 1,
          show: false,
        },
        {
          value: 'Languages',
          link: 'languages',
          index: 2,
          show: false,
        },
        {
          value: 'Library',
          link: 'library',
          index: 3,
          show: false,
        },
        {
          value: 'With Friends',
          link: 'library/friends',
          index: 11,
          show: false,
        },
        {
          value: 'Meet Jesus',
          link: 'library/meet',
          index: 12,
          show: false,
        },

        {
          value: 'Editors',
          link: 'register',
          index: 4,
          show: false,
        },
        {
          value: 'Tests',
          link: 'test',
          index: 5,
          show: false,
        },
        {
          value: 'Apk',
          link: 'apkMaker',
          index: 6,
          show: false,
        },
        {
          value: 'Capacitor',
          link: 'capacitorMaker',
          index: 7,
          show: false,
        },

        {
          value: 'SD Card ',
          link: 'sdcard',
          index: 8,
          show: false,
        },
        {
          value: 'Copy Series',
          link: 'seriesCopy',
          index: 10,
          show: false,
        },
        {
          value: 'Logout',
          link: 'logout',
          index: 9,
          show: true,
        },
      ],
    }
  },
  created() {
    this.myfriends = true
    this.authorized = this.authorize('read', this.$route.params)
    this.administrator = this.authorize('register', this.$route.params)
    LogService.consoleLogMessage('source','I finished authorization')
    var arrayLength = this.menu
    for (var i = 0; i < arrayLength; i++) {
      this.menu[i].show = false
    }
    if (this.authorized) {
      this.menu[1].show = true
      if (
        typeof this.$route.params.country_code != 'undefined' &&
        this.called_by !== 'language'
      ) {
        this.menu[2].show = true
      }
      // library
      if (this.$route.params.language_iso && this.called_by !== 'library') {
        this.menu[3].show = true
        if (this.myfriends){
          this.menu[11].show = true
          this.menu[12].show = true
        }
      }
      this.menu[9].show = true
    }
    if (this.administrator) {
      this.menu[4].show = true
      this.menu[5].show = true
      if (this.$route.params.folder_name) {
        this.menu[10].show = true
      }

      if (
        process.env.VUE_APP_MAKE_SDCARD == 'TRUE' &&
        this.$route.params.country_code
      ) {
        this.menu[6].show = true
        this.menu[7].show = true
        this.menu[8].show = true
      }
    }
    if (!this.authorized) {
      this.menu[0].show = true
    }
  },
  methods: {
    goBack() {
      window.history.back()
    },
    toggleMenu() {
      LogService.consoleLogMessage('source','tried to toggle')
      if (this.showMenu) {
        this.showMenu = false
      } else {
        this.showMenu = true
      }
    },
    setNewSelectedOption(selectedOption) {
      switch (selectedOption) {
        case 'login':
          this.$router.push({
            name: 'login',
          })
          break
        case 'logout':
          localStorage.clear()
          this.$router.push({
            name: 'login',
          })
          break
        case 'countries':
          this.$router.push({
            name: 'previewCountries',
          })
          break
        case 'languages':
          this.$router.push({
            name: 'previewLanguages',
            params: {
              country_code: this.bookmark.country.code,
            },
          })
          break
        case 'library':
          this.$router.push({
            name: 'previewLibrary',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: this.$route.params.library_code,
            },
          })
          break
        case 'library/friends':
          this.$router.push({
            name: 'previewLibrary',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: 'friends',
            },
          })
          break
        case 'library/meet':
          this.$router.push({
            name: 'previewLibrary',
            params: {
              country_code: this.$route.params.country_code,
              language_iso: this.$route.params.language_iso,
              library_code: 'meet',
            },
          })
          break
        case 'register':
          this.$router.push({
            name: 'farm',
          })
          break
        case 'sdcard':
          this.$router.push({
            name: 'sdCardMaker',
            params: {
              country_code: this.$route.params.country_code,
            },
          })
          break
        case 'apkMaker':
          this.$router.push({
            name: 'apkMaker',
            params: {
              country_code: this.$route.params.country_code,
            },
          })
          break
        case 'capacitorMaker':
          this.$router.push({
            name: 'capacitorMaker',
            params: {
              country_code: this.$route.params.country_code,
            },
          })
          break
        case 'test':
          var route = 'test' + process.env.VUE_APP_SITE
          this.$router.push({
            name: route,
          })
          break

        default:
          LogService.consoleLogMessage('source','Can not find route in NavBarAdmin')
        // code block
      }
    },
  },
}
</script>

<style></style>
