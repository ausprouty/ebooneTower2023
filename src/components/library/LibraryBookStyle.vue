<template>
  <div>
    div>
    <BaseSelect
      label="Book and Chapters Style Sheet:"
      :options="styles"
      v-model="book.style.$model"
      class="field"
    />
    <template v-if="style_error">
      <p class="errorMessage">Only .css files may be uploaded</p>
    </template>

    <label>
      Add new stylesheet&nbsp;&nbsp;&nbsp;&nbsp;
      <input
        type="file"
        v-bind:id="book.title.$model"
        ref="style"
        v-on:change="handleStyleUpload(book.code.$model)"
      />
    </label>
    <div>
      <BaseSelect
        label="Editor Styles Shown:"
        :options="ckEditStyleSets"
        v-model="book.styles_set.$model"
        class="field"
        :class="{ error: book.styles_set.$error }"
        @mousedown="book.styles_set.$touch()"
      />
      <template v-if="book.styles_set.$error">
        <p v-if="!book.styles_set.required" class="errorMessage">
          Editor Styles is required
        </p>
      </template>
    </div>
  </div>
</template>
<script>
</script>
