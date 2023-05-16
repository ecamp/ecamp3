<template>
  <div>
    <ul>
      <li v-for="(error, index) in errorList" :key="index">
        {{ error }}
      </li>
    </ul>
  </div>
</template>

<script>
import { violationsToFlatArray } from '@/helpers/serverError'

export default {
  name: 'ServerErrorContent',
  props: {
    serverError: {
      type: [Object, String, Error],
      default: null,
    },
  },
  computed: {
    errorList() {
      return violationsToFlatArray(this.serverError, this.$i18n)
    },
  },
}
</script>
