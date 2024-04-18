<template>
  <div>
    <h3>Format and Styling:</h3>
    <BaseSelect
      label="Format:"
      :options="formats"
      v-model="book.format.$model"
      class="field"
      :class="{ error: book.format.$error }"
      @mousedown="book.format.$touch()"
    />
    <template v-if="book.format.$error">
      <p v-if="!book.format.required" class="errorMessage">
        Format is required
      </p>
    </template>
  </div>
</template>
<script>
export default {
  data() {
    return {
      formats: ['page', 'series', 'library'],
    }
  },
}
</script>
