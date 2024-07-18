<template>
  <div>
    <div>
      <button class="button" @click="prototypeAll">
        Select ALL to prototype
      </button>
      <button class="button" @click="publishAll">Select ALL to publish</button>

      <button class="button" @click="prototypeNone">
        Do NOT prototype ANY
      </button>
      <button class="button" @click="publishNone">Do NOT publish ANY</button>
    </div>

    <button class="button" @click="addNewBookForm">New Book</button>

    <button class="button red" @click="saveForm">Save Changes</button>

    <button class="button grey">Disabled</button>

    <p>Please fill out the required field(s).</p>
  </div>
</template>
<script>
import { libraryUpdateMixin } from '@/mixins/library/LibraryUploadMixin.js'
export default {
  mixins: [libraryUpdateMixin],
}
</script>
