<template>
  <ValidationObserver ref="validation" v-slot="{ handleSubmit }">
    <v-form @submit.prevent="handleSubmit(createMaterialItem)">
      <v-simple-table style="margin-top: 10px">
        <tbody>
          <tr>
            <td style="vertical-align: top; width: 10%;">
              <e-text-field
                ref="quantity"
                v-model="materialItem.quantity"
                dense
                :name="$tc('entity.materialItem.fields.quantity')"
                fieldname="quantity" />
            </td>
            <td style="vertical-align: top; width: 15%">
              <e-text-field
                v-model="materialItem.unit"
                dense
                :name="$tc('entity.materialItem.fields.unit')"
                fieldname="unit" />
            </td>
            <td style="vertical-align: top;">
              <e-text-field
                v-model="materialItem.article"
                dense
                vee-rules="required"
                :name="$tc('entity.materialItem.fields.article')"
                fieldname="article" />
            </td>
            <td style="vertical-align: top; width: 20%;">
              <e-select
                v-model="materialItem.materialListId"
                dense
                vee-rules="required"
                :name="$tc('entity.materialList.name')"
                fieldname="materialListId"
                :items="materialLists" />
            </td>
            <td style="vertical-align: middle; width: 15%;">
              <v-btn type="submit">
                {{ $tc('global.button.add') }}
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-simple-table>
    </v-form>
  </validationobserver>
</template>

<script>
import { ValidationObserver } from 'vee-validate'

export default {
  name: 'MaterialCreateItem',
  components: { ValidationObserver },
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
        const key = Date.now()
        const data = this.materialItem

        if (this.period !== null) {
          data.periodId = this.period.id
        }
        if (this.activityContent !== null) {
          data.activityContentId = this.activityContent.id
        }

        this.materialItem = {}
        this.$refs.quantity.focus()
        this.$refs.validation.reset()

        const res = this.api.post(uri, data)
        this.$emit('item-adding', key, data, res)
      })
    }
  }
}
</script>

<style scoped>
</style>
