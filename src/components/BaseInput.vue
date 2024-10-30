<template>
  <div>
    <label v-if="label">{{ label }}</label>
    <input
      ref="input"
      :value="value"
      @input="updateValue"
      v-bind="$attrs"
      v-on="listeners"
    />
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    label: {
      type: String,
      default: '',
    },
    value: [String, Number],
  },
  computed: {
    listeners() {
      return {
        ...this.$listeners,
        input: this.updateValue,
      }
    },
  },
  methods: {
    updateValue(event) {
      this.$emit('input', event.target.value)
    },
    focus() {
      // Expose a focus method to focus the native input element
      this.$refs.input.focus()
    },
  },
}
</script>
