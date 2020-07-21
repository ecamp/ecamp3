<template>
  <div v-if="activityContents.length > 0">
    <v-card outlined class="mt-3">
      <v-expansion-panels v-model="openPanels" multiple flat>
        <draggable
          v-model="sortedActivityContentHrefs"
          :component-data="{ attrs: { class: 'drag-container' } }"
          handle=".drag-handle">
          <activity-content v-for="activityContent in sortedActivityContents"
                            :key="activityContent._meta.self"
                            :activity-content="activityContent"
                            @move-up="() => moveUp(activityContent)"
                            @move-down="() => moveDown(activityContent)" />
        </draggable>
      </v-expansion-panels>
    </v-card>
  </div>
</template>

<script>
import ActivityContent from './ActivityContent'
import Draggable from 'vuedraggable'

export default {
  name: 'ContentType',
  components: {
    ActivityContent,
    Draggable
  },
  props: {
    contentTypeName: { type: String, required: true },
    activity: { type: Object, required: true }
  },
  data () {
    return {
      openPanels: [0],
      sortedActivityContentHrefs: []
    }
  },
  computed: {
    activityContents () {
      return this.activity.activityContents().items.filter(ep => ep.contentTypeName === this.contentTypeName)
    },
    sortedActivityContents () {
      return this.sortedActivityContentHrefs.map(href => this.api.get(href))
    }
  },
  watch: {
    activityContents () {
      this.refreshSortedActivityContentHrefs()
    }
  },
  mounted () {
    this.refreshSortedActivityContentHrefs()
  },
  methods: {
    moveUp (ac) {
      const href = ac._meta.self
      const idx = this.sortedActivityContentHrefs.indexOf(href)
      if (idx > 0) {
        this.sortedActivityContentHrefs.splice(idx, 1)
        this.sortedActivityContentHrefs.splice(idx - 1, 0, href)
      }
    },
    moveDown (ac) {
      const href = ac._meta.self
      const idx = this.sortedActivityContentHrefs.indexOf(href)
      if (idx < this.sortedActivityContentHrefs.length - 1) {
        this.sortedActivityContentHrefs.splice(idx, 1)
        this.sortedActivityContentHrefs.splice(idx + 1, 0, href)
      }
    },
    refreshSortedActivityContentHrefs () {
      const activityContentHrefs = this.activityContents.map(ac => ac._meta.self)

      // remove unknown Ids:
      for (let i = this.sortedActivityContentHrefs.length - 1; i >= 0; i--) {
        const href = this.sortedActivityContentHrefs[i]
        if (!activityContentHrefs.includes(href)) {
          this.sortedActivityContentHrefs.splice(i, 1)
        }
      }
      // append new Ids:
      for (let i = activityContentHrefs.length - 1; i >= 0; i--) {
        const href = activityContentHrefs[i]
        if (!this.sortedActivityContentHrefs.includes(href)) {
          this.sortedActivityContentHrefs.push(href)
        }
      }
    }
  }
}
</script>

<style scoped>

div.drag-container {
  flex: 1;
}

div.v-expansion-panel {
  margin-top: 0;
  padding-top: 0;
  border-radius: 0;
}

div.v-expansion-panel:first-child {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
}

div.v-expansion-panel:last-child {
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
}

div.v-expansion-panel:not(:first-child) {
  border-top: solid 1px lightgray;
}

div.v-expansion-panel:not(:first-child)::after {
  border: none;
}

div.v-expansion-panel >>> .v-expansion-panel-header {
  min-height: 48px;
}

div.v-expansion-panel >>> .v-expansion-panel-header > header {
  border-radius: 6px;
}

div.v-expansion-panel >>> .v-expansion-panel-content .v-expansion-panel-content__wrap {
  padding: 0 16px;
}

</style>
