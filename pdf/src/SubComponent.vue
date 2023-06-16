<template>
  <Text :class="{ blue: true, red: '123' }">I'm blue</Text>
</template>
<script>
import PdfComponent from './PdfComponent.js'

export default {
  name: 'SubComponent',
  extends: PdfComponent,
}
</script>

<pdf-style>
.blue {
  color: blue;
}
</pdf-style>
