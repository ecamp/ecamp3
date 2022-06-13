<template>
  <card-content-node v-bind="$props">
    <v-list three-line class="mx-n4">
      <v-list-item-group>
        <v-list-item
          v-for="option in contentNode.options().items"
          :key="option._meta.self"
          tag="label"
          :disabled="layoutMode || disabled"
        >
          <v-list-item-action>
            <api-checkbox fieldname="checked" :uri="option._meta.self" />
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>
              {{
                $tc(
                  `contentNode.laThematicArea.entity.option.${option.translateKey}.name`
                )
              }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{
                $tc(
                  `contentNode.laThematicArea.entity.option.${option.translateKey}.description`
                )
              }}
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
  methods: {
    async refreshContent() {
      await this.api.reload(this.contentNode)
    },
  },
}
</script>
