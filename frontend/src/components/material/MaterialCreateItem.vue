<template>
  <ValidationObserver
    v-if="materialLists.length > 0"
    ref="validation"
    tag="tr"
    class="newItemRow align-top"
    @keyup.enter="submitForm"
  >
    <td style="padding-top: 4px; padding-bottom: 1px">
      <e-text-field
        ref="quantity"
        v-model="materialItem.quantity"
        dense
        vee-rules="numeric"
        inputmode="numeric"
        :name="$tc('entity.materialItem.fields.quantity')"
        fieldname="quantity"
        persistent-placeholder
      />
    </td>
    <td style="padding-top: 4px">
      <e-text-field
        v-model="materialItem.unit"
        dense
        :name="$tc('entity.materialItem.fields.unit')"
        fieldname="unit"
        maxlength="32"
        persistent-placeholder
      />
    </td>
    <td style="padding-top: 4px">
      <e-text-field
        v-model="materialItem.article"
        dense
        vee-rules="required"
        :name="$tc('entity.materialItem.fields.article')"
        fieldname="article"
        maxlength="64"
        persistent-placeholder
      />
    </td>
    <td style="vertical-align: top; padding-top: 4px">
      <e-select
        v-model="materialItem.materialList"
        dense
        vee-rules="required"
        :name="$tc('entity.materialList.name')"
        fieldname="materialList"
        :items="materialLists"
      />
    </td>
    <td v-if="!materialNode && !period" style="vertical-align: top; padding-top: 4px">
      <e-select
        v-model="materialItem.period"
        dense
        vee-rules="required"
        :name="$tc('entity.period.name')"
        fieldname="period"
        :items="periods"
      />
    </td>
    <td :colspan="!materialNode && !period ? 1 : 2">
      <button-add
        v-if="$vuetify.breakpoint.xsOnly"
        fab
        small
        hide-label
        @click="submitForm"
      />
      <ButtonAdd
        v-else
        height="52"
        class="v-btn--has-bg mt-1 mb-4"
        color="primary"
        :hide-label="!$vuetify.breakpoint.mdAndUp"
        :width="$vuetify.breakpoint.lg ? '100%' : null"
        @click="submitForm"
      >
        <i18n :path="`components.material.materialCreateItem.add.${target}`">
          <template #br><br /></template>
        </i18n>
      </ButtonAdd>
    </td>
  </ValidationObserver>

  <tr v-else>
    <td :colspan="columns">
      <div>
        <p>
          {{ $tc('components.material.materialCreateItem.noMaterialListAvailable') }}
        </p>
        <v-btn :to="campRoute(camp, 'admin')">
          <v-icon :left="$vuetify.breakpoint.mdAndUp">mdi-cogs</v-icon>
          {{ $tc('components.material.materialCreateItem.campSettingsButton') }}
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
    period: { type: Object, required: false, default: null },
    materialList: { type: String, required: false, default: null },
    materialNode: { type: Object, required: false, default: null },

    /* number of colums currently visible in table */
    columns: { type: Number, required: true },
  },
  data() {
    return {
      materialItem: {},
    }
  },
  computed: {
    materialLists() {
      return this.camp.materialLists().items.map((l) => ({
        value: l._meta.self,
        text: l.name,
      }))
    },
    periods() {
      return this.camp.periods().items.map((p) => ({
        value: p._meta.self,
        text: p.description,
      }))
    },
    target() {
      return this.materialNode ? 'activity' : 'period'
    },
  },
  mounted() {
    this.materialItem = {
      materialList: this.materialList,
    }
  },
  methods: {
    async submitForm() {
      const isValid = await this.$refs.validation.validate()
      if (isValid) {
        this.createMaterialItem()
      }
    },
    createMaterialItem() {
      const key = Date.now()
      const data = this.materialItem

      this.materialItem = {}
      this.$refs.quantity.focus()
      this.$refs.validation.reset()

      // fire event to allow for eager adding before post has finished
      this.$emit('item-adding', key, data)
    },
    campRoute,
  },
}
</script>

<style scoped>
tr td,
tr th {
  vertical-align: top;
}
</style>
