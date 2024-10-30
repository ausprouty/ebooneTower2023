<template>
  <div>
    <h2>Preliminary Text</h2>

    <p>
      <vue-ckeditor v-model="localText" :config="config" />
    </p>
  </div>
</template>

<script>
import '@/views/ckeditor/index.js'
import VueCkeditor from 'vue-ckeditor2'

export default {
  components: { VueCkeditor },
  props: {
    value: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      localText: this.value, // Initialize local data with the prop value
      config: {
        extraPlugins: [
          'bidi',
          'uploadimage',
          'iframe',
          'uploadwidget',
          'clipboard',
          'videoembed',
          'templates',
          'panelbutton',
          'floatpanel',
          'colorbutton',
          'justify',
        ],
        extraAllowedContent: [
          '*(*)[id]',
          'ol[*]',
          'span[*]',
          'align[*]',
          'webkitallowfullscreen',
          'mozallowfullscreen',
          'allowfullscreen',
        ],
        contentsCss: this.$route.params.css,
        stylesSet: this.$route.params.styles_set,
        templates_replaceContent: false,
        templates_files: [
          '/sites/' +
            process.env.VUE_APP_SITE +
            '/ckeditor/templates/' +
            this.$route.params.styles_set +
            '.js',
        ],
        filebrowserBrowseUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL + 'ckfinder.html',
        filebrowserUploadUrl:
          process.env.VUE_APP_SITE_CKFINDER_URL +
          'core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=' +
          this.languageDirectory,
        toolbarGroups: [
          { name: 'styles', groups: ['styles'] },
          { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
          { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
          { name: 'links', groups: ['links'] },
          { name: 'insert', groups: ['insert'] },
          { name: 'forms', groups: ['forms'] },
          { name: 'tools', groups: ['tools'] },
          { name: 'document', groups: ['mode', 'document', 'doctools'] },
          { name: 'clipboard', groups: ['clipboard', 'undo'] },
          { name: 'others', groups: ['others'] },
          '/',
          {
            name: 'paragraph',
            groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'],
          },
          { name: 'colors', groups: ['colors'] },
          { name: 'about', groups: ['about'] },
        ],
        height: 600,
        removeButtons:
          'About,Button,Checkbox,CreatePlaceholder,DocProps,Flash,Form,HiddenField,Iframe,NewPage,PageBreak,Preview,Print,Radio,Save,Scayt,Select,Smiley,SpecialChar,TextField,Textarea',
      }, // config
    }
  },
  watch: {
    // Sync local text with prop value if prop changes
    value(newVal) {
      this.localText = newVal;
    },
  },
  methods: {
    // Emit 'input' event whenever localText changes to update parent
    updateText(newText) {
      this.$emit('input', newText);
    },
  },
  mounted() {
    // Watch for changes in localText and emit updates
    this.$watch('localText', (newValue) => {
      this.updateText(newValue);
    });
  },
}
</script>
