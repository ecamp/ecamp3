<template>
    <auth-container>
        <h1 class="display-1 text-center mb-4">{{ $tc('views.auth.resetPasswordRequest.title') }}</h1>

        <v-alert v-if="requestSent" type="success">
            {{ $tc('views.auth.resetPasswordRequest.successMessage') }}
        </v-alert>

        <v-form v-else @submit.prevent="resetPassword">
            <e-text-field
                v-model="email"
                :label="$tc('entity.user.fields.email')"
                name="email"
                append-icon="mdi-at"
                :dense="$vuetify.breakpoint.xsOnly"
                type="text"
                autofocus />

            <v-btn type="submit"
                    block
                    :color="email ? 'blue darken-2' : 'blue lightne-4'"
                    :disabled="!email"
                    outlined
                    :x-large="$vuetify.breakpoint.smAndUp"
                    class="my-4">
                <v-progress-circular v-if="requestSending" indeterminate size="24" />
                <v-icon v-else>$vuetify.icons.ecamp</v-icon>
                <v-spacer />
                <span>{{ $tc('views.auth.resetPasswordRequest.send') }}</span>
                <v-spacer />
                <icon-spacer />
            </v-btn>
        </v-form>
        <p class="mt-8 mb0 text--secondary text-center">
            <router-link :to="{ name: 'login' }">
                {{ $tc('views.auth.resetPasswordRequest.back') }}
            </router-link>
        </p>
    </auth-container>
</template>

<script>
export default {
    name: 'ResetPasswordRequest',

    data () {
        return {
            email: '',
            requestSending: false,
            requestSent: false
        }
    }, 

    methods: {
        async resetPassword () {
            this.requestSending = true
            this.$auth.resetPasswordRequest(this.email).then(() => {
                this.requestSent = true
                this.requestSending = false
            }).catch(() => {
                this.requestSending = false
            })
        }
    }
}
</script>
