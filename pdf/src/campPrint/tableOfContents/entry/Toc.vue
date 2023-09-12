<template>
  <Link class="toc-entry" :href="`#${id}`">
    <Text>{{ $tc('print.toc.title') }}</Text>
  </Link>
</template>
<script>
import PdfComponent from '@/PdfComponent.js'

export default {
  name: 'Toc',
  extends: PdfComponent,
}
</script>
<pdf-style>
</pdf-style>
