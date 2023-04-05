<template>
  <div>
    <button class="button" v-bind:class="progress" @click="verifyLibraries()">
      {{ library_text }}
    </button>
  </div>
</template>
<script>
import CapacitorService from '@/services/CapacitorService.js'

export default {
  props: {
    language: Object,
  },
  data() {
    return {
      library_text: 'Create Library Index',
      progress: 'undone',
    }
  },
  methods: {
    async verifyLibraries() {
      var params = {}
      params.language = this.language
      //console.log(params)
      this.library_text = 'Publishing Library'
      this.progress = await CapacitorService.publish('libraries', params)
      console.log (this.progress)
      this.library_text = 'Published Library'
    },
  },
}
</script>
<style scoped>
button {
  font-size: 10px;
  padding: 10px;
}

.undone {
  background-color: black;
  padding: 10px;
  color: white;
}
.error {
  background-color: red;
  padding: 10px;
  color: white;
}

.ready {
  background-color: yellow;
  padding: 10px;
  color: black;
}

.done {
  background-color: green;
  padding: 10px;
  color: white;
}
</style>
