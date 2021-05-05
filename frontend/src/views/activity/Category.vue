<template>
  <v-container fluid>
    <content-card toolbar>
      <template #title>
        <v-toolbar-title class="font-weight-bold">
          <template v-if="category()">
            <v-chip
              :color="category().color"
              dark>
              {{ category().short }}
            </v-chip>
            {{ category().name }}
          </template>
          <template v-else>
            loading...
          </template>
        </v-toolbar-title>
      </template>
      <template #title-actions>
        <v-btn v-if="!layoutMode"
               color="primary"
               outlined
               @click="layoutMode = true">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-puzzle-edit-outline</v-icon>
            {{ $tc('views.activity.activity.changeLayout') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.layout') }}</template>
        </v-btn>
        <v-btn v-else
               color="success"
               outlined
               @click="layoutMode = false">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-icon left>mdi-check</v-icon>
            {{ $tc('views.activity.activity.backToContents') }}
          </template>
          <template v-else>{{ $tc('views.activity.activity.back') }}</template>
        </v-btn>
      </template>
      <v-card-text class="px-0 py-0">
        <content-node
          v-if="rootContentNode"
          :content-node="rootContentNode"
          :layout-mode="layoutMode" />
      </v-card-text>
    </content-card>
  </v-container>
</template>

<script>
import ContentCard from '@/components/layout/ContentCard.vue'
import ContentNode from '@/components/activity/ContentNode.vue'

export default {
  name: 'Category',
  components: {
    ContentCard,
    ContentNode
  },
  props: {
    category: {
      type: Function,
      required: true
    }
  },
  data () {
    return {
      layoutMode: true
    }
  },
  computed: {
    rootContentNode () {
      if (!this.category) return null
      if (!this.category()) return null
      if (!this.category().rootContentNode) return null
      return this.category().rootContentNode()
    }
  }
}
</script>

<style scoped>
</style>
