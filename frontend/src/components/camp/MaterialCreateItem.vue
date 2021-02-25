<template>
  <div v-if="materialLists.length > 0">
    <ValidationObserver ref="validation" v-slot="{ handleSubmit }">
      <v-form @submit.prevent="handleSubmit(createMaterialItem)">
        <v-simple-table style="margin-top: 10px">
          <colgroup>
            <col style="width: 55px;">
            <col style="width: 15%;">
            <col>
            <col style="width: 20%;">
            <col style="width: 20%;">
          </colgroup>
          <tbody>
            <tr>
              <td style="vertical-align: top;">
                <e-text-field
                  ref="quantity"
                  v-model="materialItem.quantity"
                  dense
                  :name="$tc('entity.materialItem.fields.quantity')"
                  fieldname="quantity" />
              </td>
              <td style="vertical-align: top;">
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
              <td style="vertical-align: top;">
                <e-select
                  v-model="materialItem.materialListId"
                  dense
                  vee-rules="required"
                  :name="$tc('entity.materialList.name')"
                  fieldname="materialListId"
                  :items="materialLists" />
              </td>
              <td style="vertical-align: top; padding-top: 7px;">
                <v-btn type="submit">
                  {{ $tc('global.button.add') }}
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-form>
    </validationobserver>
  </div>
  <div v-else>
    <p>
      {{ $tc('components.camp.materialCreateItem.noMaterialListAvailable') }}
    </p>
    <v-btn :to="campRoute(camp, 'admin')">
      <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
      {{ $tc('views.camp.navigationCamp.admin') }}
    </v-btn>
  </div>
</template>

<script>
import { campRoute } from '@/router'
import { ValidationObserver } from 'vee-validate'

export default {
  name: 'MaterialCreateItem',
  components: { ValidationObserver },
  props: {
    camp: { type: Object, required: true },
    period: { type: Object, default: null },
    contentNode: { type: Object, default: null }
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
        if (this.contentNode !== null) {
          data.contentNodeId = this.contentNode.id
        }

        this.materialItem = {}
        this.$refs.quantity.focus()
        this.$refs.validation.reset()

        const res = this.api.post(uri, data)
        this.$emit('item-adding', key, data, res)
      })
    },
    campRoute
  }
}
</script>

<style scoped>
  .v-data-table >>> .v-data-table__wrapper > table > tbody > tr > td {
    padding: 0 2px;
  }
</style>
