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
      <div class="column" v-if ="this.content_published">
        <button 
          class="button"
          v-bind:class="progress.medialist.progress"
          @click="localPublish('medialist')"
        >
          {{ medialist_text }}
        </button>
      </div>
      <div class="column" v-if ="!this.content_published"></div>
      <div class="column" v-if ="this.content_published">
        <button
          class="button"
          v-bind:class="progress.media_batfile.progress"
          @click="localPublish('media_batfile')"
        >
          {{ media_batfile_text }}
        </button>
      </div>
      <div class="column" v-if ="!this.content_published"></div>
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
  <div class="row">
    <span  v-html="timeout_message"></span>
    <span  v-html="progress.content.message"></span>
   <span  v-html="progress.medialist.message"></span>
   <span  v-html="progress.media_batfile.message"></span>
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
      media_batfile_text: 'Media BatFile',
      router_text: 'Router File',
      timeout_messsage: '',
      content_published: false,
      progress: {
        content: {
          progress: 'undone',
          message: null,
        },
        medialist: {
          progress: 'invisible',
          message: null,
        },
        media_batfile: {
          progress: 'invisible',
          message: null,
        },
        router: {
          progress: 'invisible',
          message: null,
        },
      },
    }
  },
  methods: {
    checkContentPublished() {
      if (this.progress.content.progress == 'done') {
        this.content_published = true
      }
    },
    async localPublish(location) {
      var response = null
      var params = this.book
      //console.log(params)
      if (location == 'content') {
        this.content_text = 'Publishing'
        this.progress.content.progress = 'checking'
        response = await CapacitorService.createBookContent(params)
        console.log(response)
        if (typeof response == 'string'){
          this.progress.content.progress = 'error';
        }
        else{
          this.progress.content = response
        }
        
        this.checkContentPublished()
        this.content_text = 'Content'
      }
      if (location == 'router') {
        this.router_text = 'Publishing'
        this.progress.router.progress = 'checking'
        response = await CapacitorService.createBookRouter(params)
        this.progress.router = response
        this.router_text = 'Router Published'
      }
      if (location == 'media_batfile') {
        this.media_batfile_text = 'Creating Media BatFile'
        this.progress.media_batfile.progress = 'checking'
        response = await CapacitorService.createBookMediaBatFile(params)
        console.log(response)
        this.progress.media_batfile = response
        this.media_batfile_text = 'Media BatFile Created'
      }
      if (location == 'medialist') {
        this.medialist_text = 'Publishing'
        this.progress.medialist.progress = 'checking'
        await CapacitorService.publish('videoMakeBatFileForCapacitor', params)
        response = await CapacitorService.createBookMediaList(params)
        this.progress.medialist = response
        if (this.progress.medialist.progress == 'done'){
          this.medialist_text = 'Media All Present'
        }
        if (this.progress.medialist.progress == 'error'){
          this.medialist_text = 'Missing Media'
        }
      }
      if (response == 'error') {
        this.timout_message = 'You may have timed out. Please reload this page.'
        console.log (response)
        this.error_count++
        if (this.error_count == 1) {
          this.checkStatus()
        }
      }
    },
    async checkStatus() {
      var params = {}
      params.folder_name = this.book.folder_name
      params.progress = JSON.stringify(this.progress)
      var response = await CapacitorService.checkStatusBook(params)
      this.progress = response
      this.checkContentPublished()

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
.checking {
  background-color: blue;
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
