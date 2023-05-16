<template>
  <div>
    <span v-if="errorList.length === 1">{{ errorList[0] }}</span>
    <ul v-else>
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
