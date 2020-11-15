<template>
  <v-row dense no-glutters justify="space-around">
    <v-col>
      <e-text-field
        v-model="materialItem.article"
        name="Artikel"
        fieldname="article" />
    </v-col>
    <v-col>
      <e-text-field
        v-model="materialItem.quantity"
        name="Quantity"
        fieldname="quantity" />
    </v-col>
    <v-col>
      <e-text-field
        v-model="materialItem.unit"
        name="Unit"
        fieldname="unit" />
    </v-col>
    <v-col>
      <e-select
        v-model="materialItem.materialListId"
        name="List"
        fieldname="materialListId"
        :items="materialLists" />
    </v-col>
    <v-col>
      <a href="#" @click="createMaterialItem">
        SAVE
      </a>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'MaterialCreateItem',
  props: {
    camp: { type: Object, required: true },
    period: { type: Object, default: null },
    activityContent: { type: Object, default: null }
  },
  data () {
    return {
      materialItem: {}
    }
  },
  computed: {
    materialLists () {
      return this.camp.materialLists().items.map(l => ({
        value: l.id,
        text: l.name
      }))
    }
  },
  methods: {
    createMaterialItem () {
      this.api.href(this.api.get(), 'materialItems').then(uri => {
        const data = this.materialItem

        if (this.period !== null) {
          data.periodId = this.period.id
        }
        if (this.activityContent !== null) {
          data.activityContentId = this.activityContent.id
        }

        this.api.post(uri, data)
      })
    }
  }
}
</script>

<style scoped>
</style>
