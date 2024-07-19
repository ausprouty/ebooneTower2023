import AuthorService from '@/services/AuthorService.js'

export const libraryUpdateMixin = {
  async revert() {
    var params = {}
    params.recnum = this.recnum
    params.route = JSON.stringify(this.$route.params)
    params.scope = 'library'
    var res = await AuthorService.revert(params)
    //console.log(res.content)
    this.seriesDetails = res.content.text
    this.recnum = res.content.recnum
  },
}
