<template>
  <e-select
    :v-model="localCollaboration.role"
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
    :hint="$tc('components.collaborator.dialogCollaboratorForm.roleHint')"
    persistent-hint
    item-value="key"
    item-text="role"
    :my="0"
    vee-rules="required"
  >
    <template #item="{ item, on, attrs }">
      <v-list-item v-bind="attrs" v-on="on">
        <v-list-item-content>
          <v-list-item-title>{{ item.role }}</v-list-item-title>
          <v-list-item-subtitle
            >{{ item.abilities }} &middot;
            <span
              ><template v-for="icon in item.icons"
                ><v-icon :key="icon" x-small>{{ icon }}</v-icon
                >&thinsp;</template
              ></span
            ></v-list-item-subtitle
          >
        </v-list-item-content>
      </v-list-item>
    </template>
  </e-select>
</template>

<script>
export default {
  name: 'DialogCollaboratorForm',
  props: {
    collaboration: { type: Object, required: true },
  },
  computed: {
    localCollaboration() {
      return this.collaboration
    },
  },
}
</script>
