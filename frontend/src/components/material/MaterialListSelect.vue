<template>
  <EAutocomplete
    v-model:search="search"
    v-model:menu="menu"
    :items="materialLists"
    :hide-no-data="false"
    :label="$t('entity.materialList.name')"
    :readonly="creating || readonly"
    :error-messages="errorMessages.concat(creationErrors)"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <template #no-data>
      <v-list-item
        :title="$t('components.material.materialListSelect.noListsFound')"
        :link="false"
      />
    </template>
    <template #append-inner>
      <v-progress-circular
        v-if="creating"
        indeterminate
        color="primary"
        size="16"
        width="2"
      />
    </template>
    <template #append-item>
      <v-list-item
        v-if="search?.trim()"
        append-icon="mdi-plus"
        :title="search.trim()"
        :subtitle="$t('components.material.materialListSelect.createNew')"
        @mousedown.prevent
        @click="createMaterialList"
      />
    </template>
  </EAutocomplete>
</template>

<script>
import EAutocomplete from '@/components/form/base/EAutocomplete.vue'
import materialListsSorted from '@/common/helpers/materialListsSorted.js'
import { serverErrorToString, violationsToFlatArray } from '@/helpers/serverError.js'
import { componentI18n } from '@/plugins/i18n/index.js'

export default {
  name: 'MaterialListSelect',
  components: { EAutocomplete },
  props: {
    camp: { type: Object, required: true },
    readonly: { type: Boolean, default: false },
    errorMessages: { type: Array, default: () => [] },
  },
  emits: ['update:modelValue'],
  data() {
    return {
      search: '',
      menu: false,
      creating: false,
      creationErrors: [],
      pendingCreation: null,
    }
  },
  computed: {
    materialLists() {
      return materialListsSorted(this.camp.materialLists().items).map((list) => ({
        value: list._meta.self,
        text: list.name,
      }))
    },
  },
  methods: {
    async createMaterialList() {
      const name = this.search?.trim()
      if (!name || this.creating) return

      this.menu = false
      this.creating = true
      this.creationErrors = []
      await this.$nextTick()

      try {
        if (this.pendingCreation?.name !== name) {
          const materialList = await this.api.post(
            await this.api.href(this.api.get(), 'materialLists'),
            { camp: this.camp._meta.self, name }
          )
          this.pendingCreation = { name, materialList }
        }
        await this.api.reload(this.camp.materialLists())
        const materialList = this.pendingCreation.materialList
        this.$emit('update:modelValue', materialList._meta.self)
        this.search = materialList.name
        this.pendingCreation = null
        await this.$nextTick()
        this.menu = false
      } catch (error) {
        const violations = violationsToFlatArray(error, componentI18n)
        this.creationErrors = violations.length
          ? violations
          : [serverErrorToString(error)]
        this.search = name
      } finally {
        this.creating = false
      }
    },
  },
}
</script>
