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

<style scoped>
.section-buttons {
  width: 40px;
  margin-top: 10px;
}

.row-inter {
  height: 4px;
  transition: 0s height;
  transition-duration: 0.5s;
}

.row-inter:hover {
  height: 30px;
  background-color: #EEEEEE;
  transition-delay: 0.3s;
}

.row-inter .button-add {
  opacity: 0;
  height: 0;
  transition: 0s height, opacity;
  transition-duration: 0.5s;
}

.row-inter:hover .button-add {
  opacity: 1;
  height: 30px;
  transition-delay: 0.3s;
}

</style>
