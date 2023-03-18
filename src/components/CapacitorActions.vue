<template>
  <div>
    <div class="row">
      <div class="column">
        <button
          class="button"
          v-bind:class="progress.content.progress"
          @click="localPublish('content')"
        >
          {{ content_text }}
        </button>
      </div>
      <div class="column">
        <button
          class="button"
          v-bind:class="progress.medialist.progress"
          @click="localPublish('medialist')"
        >
          {{ medialist_text }}
        </button>
      </div>
      <div class="column">
        <button
          class="button"
          v-bind:class="progress.media.progress"
          @click="localPublish('media')"
        >
          {{ media_text }}
        </button>
      </div>
      <div class="column">
        <button
          class="button"
          v-bind:class="progress.router.progress"
          @click="localPublish('router')"
        >
          {{ router_text }}
        </button>
      </div>
  </div>
  <div class="row" v-if="progress.medialist.message">
    {this.progress.medialist.message}
    
  </div>
</div>
</template>

<script>
import CapacitorService from '@/services/CapacitorService.js'
import { mapState } from 'vuex'
export default {
  props: {
    book: Object,
  },
  computed: mapState(['capacitorSettings']),

  /*
      {
		"id": "4",
		"code": "multiply2",
		"title": "Being like Jesus",
		"progress": "/sites/mc2/progresss/mc2GLOBAL.css",
		"progresss_set": "mc2",
		"image": {
			"title": "Multiply2.png",
			"image": "/sites/mc2/content/M2/cmn/images/standard/Multiply2.png"
		},
		"format": "series",
		"pages": "one",
		"template": "multiply2/phase1.html",
		"publish": false,
		"prototype": true
    }
*/
  data() {
    return {
      error_count: 0,
      content_text: 'Content',
      medialist_text: 'Media List',
      media_text: 'Media',
      router_text: 'Router File',
      progress: {
        content: {
          progress: 'undone',
          message: null,
        },
        medialist: {
          progress: 'invisible',
          message: null,
        },
        medialist: {
          progress: 'invisible',
          message: null,
        },
        medialist: {
          progress: 'invisible',
          message: null,
        },
      },
    }
  },
  methods: {
    async localPublish(location) {
      var response = null
      var params = this.book
      //console.log(params)
      if (location == 'content') {
        this.content_text = 'Publishing'
        response = await CapacitorService.createBookContent(params)
        console.log(response)
        this.progress.content = response
        this.content_text = 'Content'
      }
      if (location == 'router') {
        this.router_text = 'Publishing'
        this.progress.router = await CapacitorService.createBookRouter(params)
        this.router_text = 'Router Published'
      }
      if (location == 'media') {
        this.media_text = 'Checking'
        await CapacitorService.publish('media', params)
        this.progress.media = await CapacitorService.verifyBookMedia(params)
        this.media_text = 'Media Checked'
      }
      if (location == 'medialist') {
        this.medialist_text = 'Publishing'
        await CapacitorService.publish('videoMakeBatFileForCapacitor', params)
        this.progress.medialist = await CapacitorService.createBookMediaList(
          params
        )
        if (this.progress.medialist == 'done'){
          this.medialist_text = 'Media All Present'
        }
        if (this.progress.medialist == 'error'){
          this.medialist_text = 'Missing Media'
        }
      }
      if (response == 'error') {
        console.log('You may have timed out')
        this.error_count++
        if (this.error_count == 1){
          this.checkStatus()
        }
        
      }
    },
    async checkStatus() {
      var params = this.book
      params.capacitor_settings = JSON.stringify(this.capacitorSettings)
      params.progress = JSON.stringify(this.progress)
      this.progress = await CapacitorService.checkStatusBook(params)
    },
  },
  async created() {
    await this.checkStatus()
  },
}
</script>
<style scoped>
button {
  font-size: 10px;
  padding: 10px;
}
div.actions {
  flex: 1 1 0px;
}
div.parent {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
}
.row {
  display: table;
  width: 100%; /*Optional*/
  table-layout: fixed; /*Optional*/
  border-spacing: 10px; /*Optional*/
}
.column {
  display: table-cell;
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
.invisible {
  background-color: white;
  padding: 10px;
  color: white;
}

.done {
  background-color: green;
  padding: 10px;
  color: white;
}
</style>
