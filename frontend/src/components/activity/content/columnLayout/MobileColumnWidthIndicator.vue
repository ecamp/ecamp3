<template>
  <v-subheader>
    {{ $tc('contentNode.columnLayout.entity.column.name') }}
    <v-progress-linear
      class="ml-3"
      :color="color"
      buffer-value="100"
      :style="'flex-basis: ' + widthLeft + '0%'"
    />
    <v-progress-linear
      value="100"
      :color="color"
      :style="'flex-basis: ' + width + '0%'"
    />
    <v-progress-linear
      :color="color"
      buffer-value="100"
      :style="'flex-basis: ' + widthRight + '0%'"
    />
  </v-subheader>
</template>
<script>
export default {
  name: 'MobileColumnWidthIndicator',
  props: {
    width: { type: Number, required: true },
    widthLeft: { type: Number, required: true },
    widthRight: { type: Number, required: true },
    color: { type: String, required: true },
  },
}
</script>
<style scoped>
.v-subheader {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12) !important;
}
</style>
