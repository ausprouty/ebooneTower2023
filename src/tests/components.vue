<template>
  <div>
   
 <LibraryBookTitle :index="1" />
   <!--    <LibraryBookCode :index="1" />
    <LibraryBookImage :index="1" />
    <LibraryBookFormat :index="1" />
    <LibraryBookStyle :index="1" />
    <LibraryBookTemplate :index="1" />-->
    <LibraryBookPermission :index="1" />
  </div>
</template>
<script>
import LibraryBookCode from '@/components/library/LibraryBookCode'
import LibraryBookFormat from '@/components/library/LibraryBookFormat'
import LibraryBookImage from '@/components/library/LibraryBookImage'
import LibraryBookPermission from '@/components/library/LibraryBookPermission'
import LibraryBookStyle from '@/components/library/LibraryBookStyle'
import LibraryBookTemplate from '@/components/library/LibraryBookTemplate'
import LibraryBookTitle from '@/components/library/LibraryBookTitle'
export default {
  components: {
    LibraryBookCode,
    LibraryBookFormat,
    LibraryBookImage,
    LibraryBookPermission,
    LibraryBookStyle,
    LibraryBookTemplate,
    LibraryBookTitle,
  },
}
</script>
