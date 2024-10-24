<template>
  <v-container :key="checklist._meta.self" fluid>
    <content-card v-if="!checklist._meta.loading" :title="checklist?.name" toolbar>
      <v-skeleton-loader v-if="loading" type="table" />
      <table v-else class="w-100 px-n4" style="border-collapse: collapse">
        <thead>
          <tr>
            <td></td>
            <td></td>
            <td v-if="$vuetify.breakpoint.mdAndUp" style="width: 300px"></td>
          </tr>
        </thead>
        <tbody>
          <ChecklistItemParent
            v-for="{ value, depth } in indexedChecklistItems"
            :key="value?._meta.self"
            :checklist-item="value"
            :depth="depth"
            :all-checklist-nodes="allChecklistNodes"
          />
        </tbody>
      </table>
    </content-card>
  </v-container>
</template>

<script>
import { flattenDeep, groupBy } from 'lodash'
import ContentCard from '@/components/layout/ContentCard.vue'
import ChecklistItemParent from '@/components/checklist/ChecklistItemParent.vue'

export default {
  name: 'ChecklistOverview',
  components: {
    ContentCard,
    ChecklistItemParent,
  },
  props: {
    camp: { type: Object, required: true },
    checklist: { type: Object, required: true },
  },
  data() {
    return {
      loading: true,
      allChecklistNodes: [],
      indexedChecklistItems: {},
    }
  },
  watch: {
    checklist: {
      handler(newVal, oldVal) {
        if (newVal._meta.self !== oldVal._meta.self) {
          this.loading = true
          this.api
            .get()
            .checklistItems({ 'checklist.camp': this.camp._meta.self })
            .$reload()
            .then(({ items }) => {
              this.processChecklistItems(items)
              this.loading = false
            })
        }
      },
    },
  },
  async mounted() {
    await Promise.all([
      this.camp.categories()._meta.load,
      this.camp.activities().$reload(),
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
          this.processChecklistItems(items)
          this.loading = false
        }),
    ])
  },
  methods: {
    processChecklistItems(items) {
      this.indexedChecklistItems = flattenDeep(
        this.deepChildren(
          groupBy(
            items.filter((item) => item.checklist().id === this.checklist.id),
            (item) => item.parent?.()._meta.self ?? 0
          )
        )
      )
    },
    deepChildren(items, item = null, depth = 0) {
      return items[item?._meta.self ?? 0]
        ?.sort((a, b) => a.position - b.position)
        .map((child) => {
          if (child._meta.self in items) {
            return [{ value: child, depth }, this.deepChildren(items, child, depth + 1)]
          }
          return [{ value: child, depth }]
        })
    },
  },
}
</script>
