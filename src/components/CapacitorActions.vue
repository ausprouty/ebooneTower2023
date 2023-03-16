<template>
  <div class="row">
    <div class="column">
      <button
        class="button"
        v-bind:class="progress.content"
        @click="localPublish('content')"
      >
        {{ content_text }}
      </button>
    </div>
    <div class="column">
      <button
        class="button"
        v-bind:class="progress.videolist"
        @click="localPublish('videolist')"
      >
        {{ videolist_text }}
      </button>
    </div>
    <div class="column">
      <button
        class="button"
        v-bind:class="progress.media"
        @click="localPublish('media')"
      >
        {{ media_text }}
      </button>
    </div>
    <div class="column">
      <button
        class="button"
        v-bind:class="progress.router"
        @click="localPublish('router')"
      >
        {{ router_text }}
      </button>
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
      content_text: 'Content',
      videolist_text: 'Media List',
      media_text: 'Media',
      router_text: 'Router File',
      progress: {
        content: 'undone',
        videolist: 'undone',
        media: 'undone',
        router: 'undone',
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
        await CapacitorService.publish('router', params)
        this.progress.router = await CapacitorService.createBookContent(params)
        this.router_text = 'Router Published'
      }
      if (location == 'media') {
        this.media_text = 'Checking'
        await CapacitorService.publish('media', params)
        this.progress.media = await CapacitorService.verifyBookMedia(params)
        this.media_text = 'Media Checked'
      }
      if (location == 'videolist') {
        this.videolist_text = 'Publishing'
        await CapacitorService.publish('videoMakeBatFileForCapacitor', params)
        this.progress.videolist = await CapacitorService.createBookVideoList(
          params
        )
        this.videolist_text = 'Media List Published'
      }
      if (response == 'error') {
        alert('There was an error')
      }
    },
    async loadView() {},
  },
  async created() {
    var params = this.book
    params.capacitor_settings = JSON.stringify(this.capacitorSettings)
    params.progress = JSON.stringify(this.progress)
    this.progress = await CapacitorService.checkStatusBook(params)
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

.done {
  background-color: green;
  padding: 10px;
   color: white;
}
</style>
