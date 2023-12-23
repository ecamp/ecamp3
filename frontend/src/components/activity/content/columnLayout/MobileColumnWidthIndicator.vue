<template>
  <div class="mb-1">
    <p class="mb-0 blue--text text--darken-3 font-weight-medium flex-shrink-0">
      {{ $tc('contentNode.columnLayout.entity.column.name', 2, { name: slotName }) }}
      <span class="d-sr-only">{{ width + '0%' }}</span>
    </p>
    <div v-if="showProgress" class="d-flex mb-1" aria-hidden="true">
      <v-progress-linear
        color="blue darken-1"
        buffer-value="100"
        :style="'flex-basis: ' + widthLeft + '0%'"
      />
      <v-progress-linear
        value="100"
        color="blue darken-1"
        :style="'flex-basis: ' + width + '0%'"
      />
      <v-progress-linear
        color="blue darken-1"
        buffer-value="100"
        :style="'flex-basis: ' + widthRight + '0%'"
      />
    </div>
  </div>
</template>
<script>
export default {
  name: 'MobileColumnWidthIndicator',
  props: {
    width: { type: Number, required: true },
    widthLeft: { type: Number, required: true },
    widthRight: { type: Number, required: true },
    slotName: { type: [String, Number], required: true },
    showProgress: { type: Boolean, default: true },
  },
}
</script>
