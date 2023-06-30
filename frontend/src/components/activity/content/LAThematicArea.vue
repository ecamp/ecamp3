<template>
  <card-content-node v-bind="$props">
    <v-list three-line class="mx-n4 ec-lathematicarea">
      <v-list-item-group multiple :value="model">
        <v-list-item
          v-for="(option, key) in contentNode.data.options"
          :key="key"
          :value="key"
          :disabled="layoutMode || disabled"
        >
          <v-list-item-action class="ml-0">
            <api-checkbox
              :id="contentNode.id + key"
              :fieldname="`data.options.${key}.checked`"
              :uri="contentNode._meta.self"
            />
          </v-list-item-action>
          <v-list-item-content tag="label" :for="contentNode.id + key">
            <v-list-item-title
              >{{ $tc(`contentNode.laThematicArea.entity.option.${key}.name`) }}
            </v-list-item-title>
            <v-list-item-subtitle
              >{{ $tc(`contentNode.laThematicArea.entity.option.${key}.description`) }}
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </card-content-node>
</template>

<script>
import ApiCheckbox from '@/components/form/api/ApiCheckbox.vue'
import CardContentNode from '@/components/activity/CardContentNode.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'LAThematicArea',
  components: { CardContentNode, ApiCheckbox },
  mixins: [contentNodeMixin],
  computed: {
    model() {
      return Object.entries(this.contentNode.data.options)
        .filter(([, option]) => option.checked)
        .map(([key]) => key)
    },
  },
  methods: {
    async refreshContent() {
      await this.api.reload(this.contentNode)
    },
  },
}
</script>

<style scoped>
.ec-lathematicarea .theme--light.v-list-item--active:hover::before,
.ec-lathematicarea .theme--light.v-list-item--active::before {
  opacity: 0;
}
</style>
