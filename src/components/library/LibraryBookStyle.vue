<template>
  <div>
    div>
    <BaseSelect
      label="Book and Chapters Style Sheet:"
      :options="bookStyleOptions"
      v-model="bookStyle"
      class="field"
    />
    <label>
      Add new stylesheet&nbsp;&nbsp;&nbsp;&nbsp;
      <input
        type="file"
        v-bind:id="bookStyleFile"
        ref="style"
        v-on:change="handleStyleUpload(bookStyleFile)"
      />
    </label>
    <div>
      <BaseSelect
        label="Editor Styles Shown:"
        :options="ckEditStyleSets"
        v-model="ckEditorStyleSelected"
        class="field"
      />
    </div>
  </div>
</template>
<script>
import { libraryGetMixin } from '@/mixins/library/LibraryGetMixin.js'
import { libraryUploadMixin } from '@/mixins/library/LibraryUploadMixin.js'
export default {
  mixins: [libraryGetMixin, libraryUploadMixin],
  data() {
    return {
      styles: [],
      ckEditStyleSets: ['default'],
    }
  },
  methods: {},
}
</script>
