<template>
  <div v-if="contentNodes.length > 0">
    <v-card outlined class="mt-3">
      <v-expansion-panels v-model="openPanels" multiple flat>
        <draggable
          v-model="sortedContentNodeHrefs"
          :component-data="{ attrs: { class: 'drag-container' } }"
          :disabled="!(sortedContentNodes.length > 1)"
          handle=".drag-handle">
          <content-node v-for="contentNode in sortedContentNodes"
                        :key="contentNode._meta.self"
                        :content-node="contentNode"
                        :drag-drop-enabled="sortedContentNodes.length > 1"
                        @move-up="() => moveUp(contentNode)"
                        @move-down="() => moveDown(contentNode)" />
        </draggable>
      </v-expansion-panels>
    </v-card>
  </div>
</template>

<script>
import ContentNode from './ContentNode'
import Draggable from 'vuedraggable'

export default {
  name: 'ContentType',
  components: {
    ContentNode,
    Draggable
  },
  props: {
    contentTypeName: { type: String, required: true },
    activity: { type: Object, required: true }
  },
  data () {
    return {
      openPanels: [0],
      sortedContentNodeHrefs: []
    }
  },
  computed: {
    contentNodes () {
      return this.activity.contentNodes().items.filter(ep => ep.contentTypeName === this.contentTypeName)
    },
    sortedContentNodes () {
      return this.sortedContentNodeHrefs.map(href => this.api.get(href))
    }
  },
  watch: {
    activity () {
      if (this.activity !== null) {
        this.activity._meta.load.then(() => {
          this.refreshSortedContentNodeHrefs()
          this.openPanels = [0]
        })
      }
    },
    contentNodes () {
      this.refreshSortedContentNodeHrefs()
    }
  },
  mounted () {
    this.refreshSortedContentNodeHrefs()
  },
  methods: {
    moveUp (cn) {
      const href = cn._meta.self
      const idx = this.sortedContentNodeHrefs.indexOf(href)
      if (idx > 0) {
        this.sortedContentNodeHrefs.splice(idx, 1)
        this.sortedContentNodeHrefs.splice(idx - 1, 0, href)
      }
    },
    moveDown (cn) {
      const href = cn._meta.self
      const idx = this.sortedContentNodeHrefs.indexOf(href)
      if (idx < this.sortedContentNodeHrefs.length - 1) {
        this.sortedContentNodeHrefs.splice(idx, 1)
        this.sortedContentNodeHrefs.splice(idx + 1, 0, href)
      }
    },
    refreshSortedContentNodeHrefs () {
      const contentNodeHrefs = this.contentNodes.map(cn => cn._meta.self)

      // append new Ids:
      for (let i = contentNodeHrefs.length - 1; i >= 0; i--) {
        const href = contentNodeHrefs[i]
        if (!this.sortedContentNodeHrefs.includes(href)) {
          this.sortedContentNodeHrefs.push(href)
        }
      }

      // remove unknown Ids:
      for (let i = this.sortedContentNodeHrefs.length - 1; i >= 0; i--) {
        const href = this.sortedContentNodeHrefs[i]
        if (!contentNodeHrefs.includes(href)) {
          this.sortedContentNodeHrefs.splice(i, 1)
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
