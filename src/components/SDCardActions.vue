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
        v-bind:class="progress.nojs"
        @click="localPublish('nojs')"
      >
        {{ nojs_text }}
      </button>
    </div>
    <div class="column">
      <button
        class="button"
        v-bind:class="progress.pdf"
        @click="localPublish('pdf')"
      >
        {{ pdf_text }}
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
        v-bind:class="progress.cover"
        @click="localPublish('cover')"
      >
        {{ cover_text }}
      </button>
    </div>
  </div>
</template>

<script>
import NoJSService from '@/services/NoJSService.js'
import SDCardService from '@/services/SDCardService.js'
import PDFService from '@/services/PDFService.js'
import store from '@/store/store.js'
export default {
  props: {
    book: Object,
  },

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
      nojs_text: 'No JS',
      pdf_text: 'PDF',
      videolist_text: 'Media List',
      media_text: 'Media',
      cover_text: 'Cover',
      progress: {
        sdcard: '',
        nojs: '',
        pdf: '',
        videolist: '',
        media: '',
        cover: '',
      },
    }
  },
  methods: {
    async localPublish(location) {
      var response = null
      var params = this.book
      //console.log(params)
      if (location == 'cover') {
        this.cover_text = 'Publishing'
        await SDCardService.publish('cover', params)
        this.progress.cover = await SDCardService.verifyBookCover(params)
        this.cover_text = 'Media'
      }
      if (location == 'media') {
        this.media_text = 'Publishing'
        await SDCardService.publish('media', params)
        this.progress.media = await SDCardService.verifyBookMedia(params)
        this.media_text = 'Media'
      }
      if (location == 'nojs') {
        this.nojs_text = 'Publishing'
        await NoJSService.publish('seriesAndChapters', params)
        this.progress.nojs = await SDCardService.verifyBookNoJS(params)
        this.nojs_text = 'No JS'
      }
      if (location == 'pdf') {
        this.pdf_text = 'Publishing'
        await PDFService.publish('seriesAndChapters', params)
        this.progress.pdf = await SDCardService.verifyBookPDF(params)
        this.pdf_text = 'PDF'
      }
      if (location == 'content') {
        this.content_text = 'Publishing'
        await SDCardService.publish('seriesAndChapters', params)
        this.progress.content = await SDCardService.verifyBookSDCard(params)
        this.content_text = 'Content'
      }
      if (location == 'videolist') {
        this.videolist_text = 'Publishing'
        await SDCardService.publish('videoMakeBatFileForSDCard', params)
        this.progress.videolist = await SDCardService.verifyBookVideoList(
          params
        )
        this.videolist_text = 'Media List'
      }
      if (response == 'error') {
        alert('There was an error in SD Card Actions')
      }
    },
    async loadView() {},
  },
  async created() {
    var params = this.book
    params.sdSubDir = this.$store.state.sdSubDir
    params.progress = JSON.stringify(this.progress)
    //console.log(params)
    this.progress = await SDCardService.checkStatusBook(params)
    //console.log(this.progress)
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
