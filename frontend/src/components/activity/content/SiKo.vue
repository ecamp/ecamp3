<template>
  <div>
    <api-form :entity="activityContent.richtext()">
      <api-richtext
        fieldname="text"
        :label="$t('activityContent.siKo.name')"
        rows="2" />
    </api-form>
    <!--
    <api-form :entity="activityContent.richtext()">
      <api-textarea
        fieldname="text"
        :label="$t('activityContent.siKo.name')"
        rows="2"
        auto-grow />
    -->
  </div>
</template>

<script>

import ApiRichtext from '../../form/api/ApiRichtext'
import ApiForm from '@/components/form/api/ApiForm'

export default {
  name: 'SiKo',
  components: {
    ApiForm,
    ApiRichtext
  },
  props: {
    activityContent: { type: Object, required: true }
  },
  data: () => ({
    text: 'asdf'
  })
}
</script>

<style scoped>
</style>
