<template>
  <e-form name="campCollaboration">
    <e-text-field
      v-if="status"
      class="ec-status-field"
      :value="translatedStatus"
      readonly
      path="status"
    >
      <template #append>
        <slot name="statusChange" />
      </template>
    </e-text-field>
    <e-select
      v-if="readonlyRole"
      v-model="localCollaboration.role"
      path="role"
      readonly
      aria-readonly="true"
      aria-describedby="readonly"
      :items="items"
      :hint="$tc('components.collaborator.collaboratorForm.roleHint')"
      persistent-hint
      item-value="key"
      item-text="role"
      vee-rules="required"
    >
      <template #selection="{ item }">
        <span
          >{{ item.role }} &middot;
          <span class="grey--text"
            ><template v-for="icon in item.icons"
              ><v-icon :key="icon" x-small>{{ icon }}</v-icon
              >&thinsp;</template
            ></span
          >
        </span>
      </template>
    </e-select>
    <e-select
      v-else
      v-model="localCollaboration.role"
      path="role"
      :items="items"
      persistent-hint
      item-value="key"
      item-text="role"
      vee-rules="required"
    >
      <template #item="{ item, on, attrs }">
        <v-list-item v-bind="attrs" two-line v-on="on">
          <v-list-item-content>
            <v-list-item-title>{{ item.role }}</v-list-item-title>
            <span class="caption">{{ item.abilities }}</span>
          </v-list-item-content>
          <v-list-item-action-text class="text-right">
            <span
              ><template v-for="icon in item.icons"
                ><v-icon :key="icon" small>{{ icon }}</v-icon
                >&thinsp;</template
              ></span
            >
          </v-list-item-action-text>
        </v-list-item>
      </template>
      <template #selection="{ item }">
        <span
          >{{ item.role }} &middot;
          <span class="grey--text"
            ><template v-for="icon in item.icons"
              ><v-icon :key="icon" x-small>{{ icon }}</v-icon
              >&thinsp;</template
            ></span
          >
        </span>
      </template>
    </e-select>
  </e-form>
</template>

<script>
export default {
  name: 'SettingsCollaboratorForm',
  props: {
    collaboration: { type: Object, required: true },
    status: { type: [String, Boolean], required: false, default: false },
    readonlyRole: { type: [String, Boolean], required: false, default: false },
  },
  computed: {
    items() {
      return [
        {
          key: 'manager',
          role: this.$tc('entity.camp.collaborators.manager'),
          abilities: this.$tc('global.collaborationAbilities.manager'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
        },
        {
          key: 'member',
          role: this.$tc('entity.camp.collaborators.member'),
          abilities: this.$tc('global.collaborationAbilities.member'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
        },
        {
          key: 'guest',
          role: this.$tc('entity.camp.collaborators.guest'),
          abilities: this.$tc('global.collaborationAbilities.guest'),
          icons: ['mdi-eye-outline'],
        },
      ]
    },
    localCollaboration() {
      return this.collaboration
    },
    translatedStatus() {
      return this.$tc(`entity.campCollaboration.status.${this.status}`)
    },
  },
}
</script>

<style scoped>
.ec-status-field::v-deep(.v-input__append-inner) {
  margin-top: 0;
  align-self: center;
  margin-right: -4px;
}
</style>
