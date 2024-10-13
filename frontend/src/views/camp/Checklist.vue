<template>
  <content-card
    :title="$tc('views.camp.checklist.title')"
    toolbar
    :no-border="$vuetify.breakpoint.mdAndUp"
  >
    <v-expansion-panels v-model="expandedChecklists" accordion flat multiple>
      <v-expansion-panel v-for="checklist in checklists" :key="checklist._meta.self">
        <v-expansion-panel-header>
          <h3>{{ checklist.name }}</h3>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <div class="mx-n4">
            <table class="w-100 px-n4" style="border-collapse: collapse">
              <thead>
                <tr>
                  <td></td>
                  <td></td>
                  <td v-if="$vuetify.breakpoint.mdAndUp" style="width: 300px"></td>
                </tr>
              </thead>
              <tbody>
                <ChecklistItemParent
                  v-for="{ value, depth } in flat"
                  :key="value?._meta.self"
                  :checklist-item="value"
                  :depth="depth"
                  :all-checklist-nodes="allChecklistNodes"
                />
              </tbody>
            </table>
          </div>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </content-card>
</template>

<script>
import { flattenDeep, groupBy } from 'lodash'
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemParent from '@/components/checklist/ChecklistItemParent.vue'

export default {
  name: 'Checklist',
  components: {
    ContentCard,
    ChecklistItemParent,
  },
  props: {
    camp: { type: Object, required: true },
  },
  data() {
    return {
      checklistContentType: null,
      expandedChecklists: [0],
      allChecklistNodes: [],
      allChecklistItems: [],
    }
  },
  computed: {
    checklists() {
      return this.camp.checklists().items
    },
    indexedItems() {
      return groupBy(this.allChecklistItems, (item) => item.parent?.()._meta.self ?? 0)
    },
    flat() {
      return flattenDeep(this.deepChildren(null))
    },
  },
  async mounted() {
    await Promise.all([
      this.camp.categories()._meta.load,
      this.camp.activities().$reload(),
      this.camp.checklists().$reload(),
      this.api
        .get()
        .checklistNodes({ camp: this.camp._meta.self })
        .$reload()
        .then((cns) => {
          this.allChecklistNodes = cns.items
        }),
      this.api
        .get()
        .checklistItems({ 'checklist.camp': this.camp._meta.self })
        .$reload()
        .then(({ items }) => {
          this.allChecklistItems = items
        }),
    ])
  },
  methods: {
    deepChildren(item, depth = 0) {
      return this.indexedItems[item?._meta.self ?? 0]
        ?.sort((a, b) => a.position - b.position)
        .map((child) => {
          if (child._meta.self in this.indexedItems) {
            return [{ value: child, depth }, this.deepChildren(child, depth + 1)]
          }
          return [{ value: child, depth }]
        })
    },
  },
}
</script>
