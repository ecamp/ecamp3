<template>
  <ValidationObserver v-if="materialLists.length > 0"
                      ref="validation"
                      tag="tr"
                      class="newItemRow"
                      @submit.prevent="createMaterialItem">
    <td>
      <e-text-field
        ref="quantity"
        v-model="materialItem.quantity"
        dense
        :name="$tc('entity.materialItem.fields.quantity')"
        fieldname="quantity" />
    </td>
    <td>
      <e-text-field
        v-model="materialItem.unit"
        dense
        :name="$tc('entity.materialItem.fields.unit')"
        fieldname="unit" />
    </td>
    <td>
      <e-text-field
        v-model="materialItem.article"
        dense
        vee-rules="required"
        :name="$tc('entity.materialItem.fields.article')"
        fieldname="article" />
    </td>
    <td>
      <e-select
        v-model="materialItem.materialListId"
        dense
        vee-rules="required"
        :name="$tc('entity.materialList.name')"
        fieldname="materialListId"
        :items="materialLists" />
    </td>
    <td>
      <button-add hide-label @click="submitForm" />
    </td>
  </ValidationObserver>

  <tr v-else>
    <td colspan="5">
      <div>
        <p>
          {{ $tc('components.camp.materialCreateItem.noMaterialListAvailable') }}
        </p>
        <v-btn :to="campRoute(camp, 'admin')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
          {{ $tc('views.camp.navigationCamp.admin') }}
        </v-btn>
      </div>
    </td>
  </tr>
</template>

<script>
import { campRoute } from '@/router.js'
import { ValidationObserver } from 'vee-validate'
import ButtonAdd from '@/components/buttons/ButtonAdd.vue'

export default {
  name: 'MaterialCreateItem',
  components: { ValidationObserver, ButtonAdd },
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
    async submitForm () {
      const isValid = await this.$refs.validation.validate()
      if (isValid) {
        this.createMaterialItem()
      }
    },
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

        // fire event to allow for eager adding before post has finished
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

  .newItemRow {
    line-height: 80px;
  }
</style>
