<template>
  <ContentNodeCard v-bind="$props">
    <v-list three-line class="mx-n4" color="transparent">
      <v-list-item-group>
        <v-list-item
          v-for="(option, key) in contentNode.data.options"
          :key="key"
          tag="label"
          :disabled="layoutMode || disabled"
        >
          <v-list-item-action>
            <api-checkbox
              :fieldname="`data.options.${key}.checked`"
              :uri="contentNode._meta.self"
            />
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{
              $tc(`contentNode.laThematicArea.entity.option.${key}.name`)
            }}</v-list-item-title>
            <v-list-item-subtitle>{{
              $tc(`contentNode.laThematicArea.entity.option.${key}.description`)
            }}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
  </ContentNodeCard>
</template>

<script>
import ApiCheckbox from '@/components/form/api/ApiCheckbox.vue'
import ContentNodeCard from '@/components/activity/content/layout/ContentNodeCard.vue'
import { contentNodeMixin } from '@/mixins/contentNodeMixin.js'

export default {
  name: 'LAThematicArea',
  components: { ContentNodeCard, ApiCheckbox },
  mixins: [contentNodeMixin],
  methods: {
    async refreshContent() {
      await this.api.reload(this.contentNode)
    },
  },
}
</script>
