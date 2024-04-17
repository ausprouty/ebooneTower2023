<template>
  <div>
    <h2>Name and Code</h2>

    <BaseInput
      v-model="book.id.$model"
      label="Book Number:"
      type="text"
      placeholder="#"
      class="field"
      :class="{ error: book.id.$error }"
      @blur="book.id.$touch()"
    />
    <BaseInput
      v-model="book.title.$model"
      label="Title:"
      type="text"
      placeholder="Title"
      class="field"
      :class="{ error: book.title.$error }"
      @blur="book.title.$touch()"
    />
    <template v-if="book.title.$error">
      <p v-if="!book.title.required" class="errorMessage">
        Book Title is required
      </p>
    </template>
  </div>
</template>
<script>
</script>
