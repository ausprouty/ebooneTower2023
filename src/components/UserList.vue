<template>
  <div>
    <div class="user">
      <router-link :to="'/user/' + user.uid">
        {{ user.firstname }} {{ user.lastname }}
      </router-link>
      ---- {{ this.scope_countries }} ({{ localStorage.scope_languages }})
    </div>
  </div>
</template>

<script>
import LogService from '@/services/LogService.js'
import store from '@/store/store.js'
import { countriesMixin } from '@/mixins/CountriesMixin.js'

export default {
  props: {
    user: Object,
  },
  mixins: [countriesMixin],
  data: function () {
    return {
      scope: null,
    }
  },
  methods: {
    findUser(uid) {
      this.$router.push({
        name: 'user',
        params: {
          uid: uid,
        },
      })
    },
  },
  async created() {
    if (localStorage.scope_countries == '|*|') {
      this.scope_countries = 'Global'
    } else {
      await this.getCountries()
      LogService.consoleLogMessage('source','scope_countries')
      LogService.consoleLogMessage('source',localStorage.scope_countries)
      var country_count = localStorage.scope_countries.length
      var c = 0
      this.scope = ''
      var user_scope = localStorage.scope_countries.split('|')
      var length = user_scope.length
      LogService.consoleLogMessage('source',length)
      for (var i = 0; i < length; i++) {
        for (c = 0; c < country_count; c++) {
          if (this.countries[c].code == user_scope[i]) {
            this.scope = this.scope + this.countries[c].english + '  '
          }
        }
      }
    }
  },
}
</script>
