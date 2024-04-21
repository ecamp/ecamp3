<template>
  <div class="bg">
    <div class="hill">
      <v-icon :size="$vuetify.breakpoint.smAndUp ? 300 : 200" class="tent"
        >$vuetify.icons.tentDay
      </v-icon>
      <div class="trees"></div>
      <div class="localnav justify-space-between d-flex w-100 py-2">
        <ButtonBack v-if="!$vuetify.breakpoint.mdAndUp" text dark visible-label />
        <UserMeta v-if="!$vuetify.breakpoint.mdAndUp" avatar-only />
      </div>
      <h1 class="wood text-center align-self-end">
        <span class="rope"></span>
        <span class="rope l"></span>
        <v-skeleton-loader
          v-if="variant === 'default' && invitationFound === undefined"
          type="image"
          width="200"
          height="48"
        />
        <template v-else-if="variant === 'default' && invitationFound !== false">
          <span class="subtitle-2 font-weight-bold">{{
            $tc('views.camp.invitation.title')
          }}</span>
          <span v-if="!invite?._meta.loading">{{ invite?.campTitle }}</span>
        </template>
        <span v-else-if="variant === 'rejected'">{{
          $tc('views.camp.invitation.successfullyRejected')
        }}</span>
        <span v-else-if="variant === 'error'">{{
          $tc('views.camp.invitation.error')
        }}</span>
        <span v-else>{{ $tc('views.camp.invitation.notFound') }}</span>
      </h1>
      <div
        v-if="variant === 'default' && invitationFound === true"
        class="invitation-actions flex-column d-grid gap-2 align-self-start justify-items-center mx-2"
      >
        <div v-if="authUser">
          <v-alert
            v-if="invite?.userAlreadyInCamp"
            border="left"
            colored-border
            color="primary"
            class="mb-1"
            :icon="$vuetify.breakpoint.smAndUp ? 'mdi-information-outline' : false"
            type="info"
          >
            {{ $tc('views.camp.invitation.userAlreadyInCamp') }}
            <div class="mt-2 d-flex flex-wrap gap-2">
              <v-btn small color="primary" :to="campLink" elevation="0">
                {{ $tc('views.camp.invitation.openCamp') }}
              </v-btn>
              <v-btn
                color="primary"
                text
                small
                class="v-btn--has-bg"
                @click="useAnotherAccount"
              >
                {{ $tc('views.camp.invitation.useOtherAuth') }}
              </v-btn>
            </div>
          </v-alert>
          <div v-else class="d-grid gap-2 grid-cols-fill">
            <v-btn color="primary" x-large @click="acceptInvitation">
              {{ $tc('views.camp.invitation.acceptCurrentAuth') }}<br />
            </v-btn>
            <v-btn dark text class="mt-2" @click="useAnotherAccount">
              {{ $tc('views.camp.invitation.useOtherAuth') }}
            </v-btn>
          </div>
        </div>
        <div v-else class="d-grid gap-2 grid-cols-2">
          <v-btn color="primary" x-large :to="loginLink">
            {{ $tc('global.button.login') }}
          </v-btn>
          <v-btn color="secondary" x-large :to="{ name: 'register' }">
            {{ $tc('views.camp.invitation.register') }}
          </v-btn>
        </div>
        <v-btn dark text :small="invite?.userAlreadyInCamp" @click="rejectInvitation">
          {{ $tc('views.camp.invitation.reject') }}
        </v-btn>
      </div>
      <div class="justify-center d-flex col gap-2">
        <v-btn v-if="authUser" text dark :to="{ name: 'home' }">
          <v-icon left>mdi-tent</v-icon>
          {{ $tc('views.camp.invitation.backToHome') }}
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script>
import { loginRoute } from '@/router'
import VueRouter from 'vue-router'
import { errorToMultiLineToast } from '@/components/toast/toasts'
import ButtonBack from '@/components/buttons/ButtonBack.vue'
import UserMeta from '@/components/navigation/UserMeta.vue'

const { isNavigationFailure, NavigationFailureType } = VueRouter
const ignoreNavigationFailure = (e) => {
  if (!isNavigationFailure(e, NavigationFailureType.redirected)) {
    return Promise.reject(e)
  }
}

export default {
  name: 'Invitation',
  components: { ButtonBack, UserMeta },
  props: {
    invitation: { type: Object, required: false, default: null },
    variant: { type: String, default: 'default' },
  },
  data: () => ({
    invitationFound: undefined,
  }),
  computed: {
    invite() {
      return this.invitationFound === true ? this.invitation : null
    },
    campLink() {
      return {
        name: 'camp/program',
        params: { campId: this.invite?.campId },
      }
    },
    loginLink() {
      return loginRoute(this.$route.fullPath)
    },
    ready() {
      return this.invitationFound !== undefined
    },
    authUser() {
      return this.$store.state.auth.user
    },
  },
  mounted() {
    this.invitationFound = undefined

    if (this.variant === 'default') {
      // Content of api response depends on authenticated user --> reload every time this component is mounted
      this.invitation?.$reload().then(
        () => {
          this.invitationFound = true
        },
        () => {
          this.invitationFound = false
        }
      )
    }
  },
  methods: {
    useAnotherAccount() {
      // Remember the login link for after we are logged out
      const loginLink = this.loginLink
      this.$auth.logout().then((_) => this.$router.push(loginLink))
    },
    acceptInvitation() {
      this.api
        .href(this.api.get(), 'invitations', {
          action: 'accept',
          id: this.$route.params.inviteKey,
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .then(
          (_) => {
            this.$router.push(this.campLink).catch(ignoreNavigationFailure)
          },
          () => {
            this.$router
              .push({ name: 'invitationUpdateError' })
              .catch(ignoreNavigationFailure)
          }
        )
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
    },
    rejectInvitation() {
      this.api
        .href(this.api.get(), 'invitations', {
          action: 'reject',
          id: this.$route.params.inviteKey,
        })
        .then((postUrl) => this.api.patch(postUrl, {}))
        .then(
          (_) => {
            this.$router
              .push({ name: 'invitationRejected' })
              .catch(ignoreNavigationFailure)
          },
          () => {
            this.$router
              .push({ name: 'invitationUpdateError' })
              .catch(ignoreNavigationFailure)
          }
        )
        .catch((e) => this.$toast.error(errorToMultiLineToast(e)))
    },
  },
}
</script>

<style scoped lang="scss">
.bg {
  height: 100%;
  background-image: radial-gradient(
    circle at bottom,
    #edf6fb,
    hsl(195deg 100% 90.32%),
    #3298e0
  );
  background-blend-mode: screen;
  background-size: contain, 1470px;
  background-repeat: no-repeat, repeat-x;
  background-position:
    bottom,
    center top;
}

.hill {
  height: 100%;
  background-image: radial-gradient(
      150vmax 80% at bottom,
      #2c5b23,
      #84b444 70.1%,
      transparent 70.2%
    ),
    url(../../assets/invitation/edge-left.svg),
    url(../../assets/invitation/edge-right.svg);
  background-size: auto, 40%, 40%;
  grid-template-rows: auto 2fr auto 3fr auto;
  display: grid;
  justify-items: center;
  align-content: center;
  background-position:
    bottom center,
    bottom 58% left -5%,
    bottom 58% right -5%;
}

@media #{map-get($display-breakpoints, 'sm-and-up')} {
  .hill {
    background-image: radial-gradient(
        150vmax 75% at bottom,
        #2c5b23,
        #84b444 70.1%,
        transparent 70.2%
      ),
      url(../../assets/invitation/edge-left.svg),
      url(../../assets/invitation/edge-right.svg);
    grid-template-rows: auto 1fr auto 1fr auto;
    background-size: auto, min(40%, 800px), min(40%, 800px);
    background-position:
      bottom center,
      bottom 54% left,
      bottom 54% right;
  }
}

@media #{map-get($display-breakpoints, 'md-and-up')} {
  .hill {
    transition: background-position 0.5s ease-in-out;
    background-position:
      bottom center,
      bottom 55% left,
      bottom 55% right;
  }
}

@media #{map-get($display-breakpoints, 'lg-and-up')} {
  .hill {
    background-position:
      bottom center,
      bottom 56% left,
      bottom 56% right;
  }
}

.tent {
  margin: auto;
  max-width: 100%;
  position: relative;
}

.tent::before {
  content: '';
  display: block;
  position: absolute;
  background: red;
}

.wood {
  background: linear-gradient(to left, #6236002b, #462c061f, #542e002b),
    linear-gradient(to right top, #4d31191f, #502f122b, #cab09b26, #d9b18a4f),
    linear-gradient(
      to bottom,
      #b67543 1%,
      #a4632724,
      #b67543 3%,
      #a4632724,
      #b67543 15%,
      #a4632724,
      #b67543 40%,
      #a4632724,
      #b67543,
      #a4632724 55%,
      #b67543,
      #a4632724,
      #b67543 68%,
      #a4632724,
      #b67543 70%,
      #a4632724,
      #b67543 80%,
      #a4632724
    ),
    linear-gradient(191deg, #d49c67, #ac6127);
  background-blend-mode: darken, screen, normal, normal;
  padding: 20px;
  color: white;
  display: grid;
  border-radius: 4px;
  box-shadow:
    inset -1px -2px 2px #6d340a,
    inset 0px 3px 1px #e7c29f;
  margin: 0 16px;
  position: relative;
  text-shadow: -1px -1px 1px #5d2f0c;
  order: -1;
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  .wood {
    font-size: 22px;
  }
}

.localnav {
  order: -2;
  gap: 8px;
}

.invitation-actions {
  display: grid;
  gap: 8px;
  justify-items: center;
}

.rope,
.rope::before,
.rope::after {
  position: absolute;
  left: min(5%, 42px);
  bottom: calc(100% - 10px);
}

.rope::after {
  content: '';
  z-index: 0;
  height: 40vh;
  position: absolute;
  width: 10px;
  z-index: 1;
  border-radius: 50px;
  background-image: linear-gradient(0deg, #2d0b0396, #ffffff 8%, #ffffff 48%, #000000cc),
    linear-gradient(90deg, #583c175c, #c1916054, #5f401712),
    linear-gradient(309deg, #552302, transparent, #552302, transparent, #552302),
    linear-gradient(319deg, #efd0b3, #c19160);
  background-size:
    cover,
    cover,
    10px 12px,
    cover;
  background-repeat: repeat;
  background-blend-mode: multiply, normal, normal, normal;
}

.rope::before {
  content: '';
  position: absolute;
  width: 10px;
  height: 16px;
  border-radius: 50%;
  background: #70370c;
  margin: -1px 0;
  opacity: 0.8;
  z-index: 0;
  box-shadow: 0 3px 4px 1px #70370c;
}

.rope.l,
.rope.l::before,
.rope.l::after {
  position: absolute;
  left: auto;
  right: min(5%, 42px);
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  .rope,
  .rope::before,
  .rope::after {
    left: 25%;
  }

  .rope.l,
  .rope.l::before,
  .rope.l::after {
    left: auto;
    right: 25%;
  }
}

.rope.l::after {
  background-position-x: -2px;
}

.trees {
  position: absolute;
  pointer-events: none;
  inset: 0;
  background-image: url('../../assets/invitation/tree-left.svg'),
    url('../../assets/invitation/tree-right.svg');
  background-position:
    left bottom,
    right bottom;
  transition: background-size 0.5s ease;
  background-size:
    auto 40%,
    auto 65%;
  @media #{map-get($display-breakpoints, 'sm-and-up')} {
    background-size:
      auto 44%,
      auto 65%;
  }
  @media #{map-get($display-breakpoints, 'md-and-up')} {
    background-size:
      auto 49%,
      auto 75%;
  }
  @media #{map-get($display-breakpoints, 'lg-and-up')} {
    background-size:
      auto 100%,
      auto 90%;
  }
}
</style>
