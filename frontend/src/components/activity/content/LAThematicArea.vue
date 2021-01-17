<template>
  <v-list three-line>
    <v-list-item v-for="option in activityContent.options().items" :key="option.id" class="px-0">
      <v-list-item-action>
        <api-checkbox :id="option.id" fieldname="checked" :uri="option._meta.self" />
      </v-list-item-action>
      <label class="v-list-item__content align-baseline" :for="option.id">
        <v-list-item-title>{{ $tc(`activityContent.laThematicArea.entity.option.${option.translateKey}.name`) }}</v-list-item-title>
        <v-list-item-subtitle>{{ $tc(`activityContent.laThematicArea.entity.option.${option.translateKey}.description`) }}</v-list-item-subtitle>
      </label>
    </v-list-item>
  </v-list>
</template>

<script>

import ApiCheckbox from '@/components/form/api/ApiCheckbox'

export default {
  name: 'LAThematicArea',
  components: { ApiCheckbox },
  props: {
    activityContent: { type: Object, required: true }
  },
  methods: {
    async refreshContent () {
      await this.api.reload(this.activityContent)
    }
  }
}
</script>
