<template>
  <e-select
    v-model="localRole"
    path="role"
    :items="items"
    persistent-hint
    vee-rules="required"
    v-bind="$attrs"
  >
    <template #item="{ item, props }">
      <v-list-item lines="two" v-bind="props">
        <v-list-item-subtitle>{{ item.raw.abilities }}</v-list-item-subtitle>
        <template #append>
          <span>
            <template v-for="icon in item.raw.icons" :key="icon">
              <v-icon size="small">{{ icon }}</v-icon>
              &thinsp;
            </template>
          </span>
        </template>
      </v-list-item>
    </template>
    <template #selection="{ item }">
      <span>
        {{ item.title }} &middot;
        <span class="text-grey">
          <template v-for="icon in item.raw.icons" :key="icon">
            <v-icon size="x-small">{{ icon }}</v-icon>
            &thinsp;
          </template>
        </span>
      </span>
    </template>
  </e-select>
</template>

<script>
export default {
  name: 'CollaboratorRoleSelect',
  props: {
    modelValue: { type: String, required: false, default: 'member' },
  },
  emits: ['update:modelValue'],
  computed: {
    localRole: {
      get() {
        return this.modelValue
      },
      set(value) {
        this.$emit('update:modelValue', value)
      },
    },
    items() {
      return [
        {
          value: 'manager',
          text: this.$t('entity.camp.collaborators.manager'),
          abilities: this.$t('global.collaborationAbilities.manager'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
        },
        {
          value: 'member',
          text: this.$t('entity.camp.collaborators.member'),
          abilities: this.$t('global.collaborationAbilities.member'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
        },
        {
          value: 'guest',
          text: this.$t('entity.camp.collaborators.guest'),
          abilities: this.$t('global.collaborationAbilities.guest'),
          icons: ['mdi-eye-outline'],
        },
      ]
    },
  },
}
</script>
