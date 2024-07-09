<template>
  <component :is="component" style="color: #e92d35" />
</template>

<script>
import JSLogo from '@/common/assets/logos/JSLogo.svg'
import GSLogo from '@/common/assets/logos/GSLogo.svg'
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
