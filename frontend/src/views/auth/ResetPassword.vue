<template>
    <auth-container>
        <h1 class="display-1 text-center mb-4">{{ $tc('views.auth.resetPassword.title') }}</h1>

        <v-alert v-if="passwordReseted" type="success">
            {{ $tc('views.auth.resetPassword.successMessage') }}
        </v-alert>

        <v-form v-else @submit.prevent="resetPassword">
            <e-text-field
                :value="email"
                :label="$tc('entity.user.fields.email')"
                name="email"
                append-icon="mdi-at"
                :dense="$vuetify.breakpoint.xsOnly"
                type="text"
                readonly />

            <e-text-field
                v-model="password1"
                :label="$tc('views.auth.resetPassword.password')"
                name="password1"
                :rules="pw1Rules"
                validate-on-blur
                :dense="$vuetify.breakpoint.xsOnly"
                type="password"
                autofocus
            />
                
            <e-text-field
                v-model="password2"
                :label="$tc('views.auth.resetPassword.passwordConfirmation')"
                name="password2"
                :rules="pw2Rules"
                validate-on-blur
                :dense="$vuetify.breakpoint.xsOnly"
                type="password"
            />

            <v-btn type="submit"
                    block
                    :color="email ? 'blue darken-2' : 'blue lightne-4'"
                    :disabled="!email"
                    outlined
                    :x-large="$vuetify.breakpoint.smAndUp"
                    class="my-4">
                <v-progress-circular v-if="passwordResetSending" indeterminate size="24" />
                <v-icon v-else>$vuetify.icons.ecamp</v-icon>
                <v-spacer />
                <span>{{ $tc('views.auth.resetPassword.send') }}</span>
                <v-spacer />
                <icon-spacer />
            </v-btn>
        </v-form>
        <p class="mt-8 mb0 text--secondary text-center">
            <router-link :to="{ name: 'login' }">
                {{ $tc('views.auth.resetPassword.back') }}
            </router-link>
        </p>
    </auth-container>
</template>

<script>
export default {
    name: 'ResetPassword',
    props: {
        emailBase64: { type: String, required: true },
        resetKey: { type: String, required: true }
    },
    
    data () {
        return {
            password1: '',
            password2: '',
            passwordResetSending: false,
            passwordReseted: false
        }
    }, 

    computed: {
        email () {
            return atob(this.emailBase64)
        },
        pw1Rules () {
            return [ v => v.length >= 8 || 'Mindestens 8 Zeichen lang sein' ]
        },
        pw2Rules () {
            return [ v => (!!v && v) === this.password1 || 'Nicht übereinstimmend' ]
        }
    },

    methods: {
        async resetPassword () {
            this.passwordResetSending = true
            this.$auth.resetPassword(this.emailBase64, this.resetKey, this.password1).then(() => {
                this.passwordReseted = true
                this.passwordResetSending = false
            }).catch((e) => {
                console.log(e)
                console.log(arguments)
                this.passwordResetSending = false
            })
        }
    }
}
</script>
