<template>
  <component :is="component" />
</template>

<script>
import JSLogo from '@/common/logos/JSLogo.svg'
import GSLogo from '@/common/logos/GSLogo.svg'

export default {
  name: 'YSLogo',
  components: {
    GSLogo,
    JSLogo,
  },
  computed: {
    component() {
      return this.$i18n.locale.match(/it/i) ? 'GSLogo' : 'JSLogo'
    },
  },
}
</script>

<style scoped>
path {
  fill: #e92d35;
}
</style>
