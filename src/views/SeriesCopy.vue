<template>
  <div>
    <NavBar called_by="seriesCopy" />
    <p>This will copy to another country</p>
    {{ this.publish_text }}
    country_code: {{ this.$route.params.country_code }}, language_iso:
    {{ this.$route.params.language_iso }}, library_code:
    {{ this.$route.params.library_code }}, folder_name:
    {{ this.$route.params.folder_name }}
  </div>
</template>
<script>
import NavBar from '@/components/NavBarAdmin.vue'
import { seriesMixin } from '@/mixins/SeriesMixin.js'
import { authorizeMixin } from '@/mixins/AuthorizeMixin.js'
import { publishMixin } from '@/mixins/PublishMixin.js'

export default {
  mixins: [seriesMixin, authorizeMixin, publishMixin],
  props: ['country_code', 'language_iso', 'library_code', 'folder_name'],

  components: {
    NavBar,
  },
}
</script>
