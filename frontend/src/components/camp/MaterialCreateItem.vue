<template>
  <v-row dense no-glutters justify="space-around">
    <v-col>
      <e-text-field
        v-model="materialItem.quantity"
        dense
        :name="$tc('entity.materialItem.fields.quantity')"
        fieldname="quantity" />
    </v-col>
    <v-col>
      <e-text-field
        v-model="materialItem.unit"
        dense
        :name="$tc('entity.materialItem.fields.unit')"
        fieldname="unit" />
    </v-col>
    <v-col>
      <e-text-field
        v-model="materialItem.article"
        dense
        :name="$tc('entity.materialItem.fields.article')"
        fieldname="article" />
    </v-col>
    <v-col>
      <e-select
        v-model="materialItem.materialListId"
        dense
        :name="$tc('entity.materialList.name')"
        fieldname="materialListId"
        :items="materialLists" />
    </v-col>
    <v-col>
      <v-btn @click="createMaterialItem">
        {{ $tc('global.button.add') }}
      </v-btn>
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

        this.api.post(uri, data).then(mi => {
          this.$emit('item-add', mi)
          this.materialItem = {}
        })
      })
    }
  }
}
</script>

<style scoped>
</style>
