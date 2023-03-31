import VueImageZoomer from '@/components/vueImageZoomer.vue'
export default {
	install: (app, options) => {
		app.component("VueImageZoomer", VueImageZoomer);
	},
}
export { default as VueImageZoomer } from "@/components/vueImageZoomer.vue";
console.log ('in index.js')
