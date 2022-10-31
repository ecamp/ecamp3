<template>
  <v-tooltip v-if="showIcon" v-model="showTooltip" max-width="300px" color="#333" bottom>
    <template #activator="dat">
      <v-btn
        icon
        v-bind="dat.attrs"
        class="tooltip-activator"
        @click="click"
        @mouseenter="mouseenter"
        @mouseleave="mouseleave"
      >
        <v-icon>{{ icon }}</v-icon>
      </v-btn>
    </template>
    <slot>
      {{ text }}
      <i18n v-if="tooltipI18nKey" :path="tooltipI18nKey">
        <template #br><br class="linebreak" /></template>
      </i18n>
    </slot>
  </v-tooltip>
</template>

<script>
export default {
  name: 'IconWithTooltip',
  components: {},
  inheritAttrs: false,
  props: {
    icon: { type: String, required: false, default: 'mdi-information-outline' },
    text: { type: String, required: false, default: undefined },
    tooltipI18nKey: { type: String, required: false, default: undefined },
  },
  data() {
    return {
      showTooltip: false,
    }
  },
  computed: {
    showIcon() {
      return this.text || 'default' in this.$slots || this.$tc(this.tcKey) != this.tcKey
    },
  },
  methods: {
    click() {
      if (this.$vuetify.breakpoint.xsOnly) {
        this.showTooltip = !this.showTooltip
      }
    },
    mouseenter() {
      if (!this.$vuetify.breakpoint.xsOnly) {
        this.showTooltip = true
      }
    },
    mouseleave() {
      this.showTooltip = false
    },
  },
}
</script>

<style scoped>
br.linebreak {
  display: block;
  content: '';
  margin-top: 8px;
}
</style>
