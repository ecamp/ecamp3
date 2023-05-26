<template>
  <div>
    <e-text-field
      v-if="status"
      class="ec-status-field"
      :value="translatedStatus"
      readonly
      :name="$tc('entity.campCollaboration.fields.status')"
    >
      <template #append>
        <slot name="statusChange" />
      </template>
    </e-text-field>
    <e-select
      v-model="localCollaboration.role"
      :name="$tc('entity.campCollaboration.fields.role')"
      fieldname="role"
      :items="[
        {
          key: 'manager',
          role: $tc('entity.camp.collaborators.manager'),
          abilities: $tc('entity.camp.collaborators.managerAbilities'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline', 'mdi-cog-outline'],
        },
        {
          key: 'member',
          role: $tc('entity.camp.collaborators.member'),
          abilities: $tc('entity.camp.collaborators.memberAbilities'),
          icons: ['mdi-eye-outline', 'mdi-pencil-outline'],
        },
        {
          key: 'guest',
          role: $tc('entity.camp.collaborators.guest'),
          abilities: $tc('entity.camp.collaborators.guestAbilities'),
          icons: ['mdi-eye-outline'],
        },
      ]"
      :hint="$tc('components.collaborator.settingsCollaboratorForm.roleHint')"
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
  </div>
</template>

<script>
export default {
  name: 'SettingsCollaboratorForm',
  props: {
    collaboration: { type: Object, required: true },
    status: { type: [String, Boolean], required: false, default: false },
  },
  computed: {
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
.ec-status-field::v-deep .v-input__append-inner {
  margin-top: 0;
  align-self: center;
  margin-right: -4px;
}
</style>
