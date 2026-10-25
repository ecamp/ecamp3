<template>
  <div class="flex-grow-1 overflow-y-auto d-flex flex-column ga-4 px-3 ec-comments-list">
    <template v-if="loading">
      <v-skeleton-loader type="list-item-two-line" />
      <v-skeleton-loader type="list-item-two-line" />
    </template>
    <Transition
      v-else
      :name="
        filterTransition &&
        (filterTransition === 'left' ? 'ec-filter-left' : 'ec-filter-right')
      "
      mode="out-in"
      @before-leave="restoreLeavingContent"
    >
      <div
        :key="filterPage"
        class="ec-comments-list__page d-flex flex-column ga-4"
        @vue:before-unmount="captureLeavingContent"
      >
        <p
          v-if="!visibleComments.length && emptyText"
          class="text-body-2 text-medium-emphasis mb-0"
        >
          {{ emptyText }}
        </p>
        <TransitionGroup name="ec-thread" @before-leave="restoreLeavingContent">
          <div
            v-for="root in roots"
            :key="root._meta.self"
            class="ec-comments-list__item"
            :class="{
              'ec-comments-list__item--muted':
                statusFilter === 'all' && !!root.resolvedAt,
              'ec-comments-list__item--resolved':
                statusFilter === 'open' && !!root.resolvedAt,
            }"
            :data-status-filter="statusFilter"
            @vue:before-unmount="captureLeavingContent($event, root)"
          >
            <CommentThread
              :root="root"
              :camp="camp"
              :replies="repliesTo(root)"
              :show-context="showContext"
              :status-filter="statusFilter"
              @replied="$emit('replied')"
              @retain="retainThread(root._meta.self, $event)"
            />
          </div>
        </TransitionGroup>
      </div>
    </Transition>
    <slot name="after" />
  </div>
</template>

<script>
import CommentThread from '@/components/comments/CommentThread.vue'
import { filterComments } from '@/components/comments/commentHelpers.js'

const leavingContent = new WeakMap()

export default {
  name: 'CommentsList',
  components: { CommentThread },
  props: {
    comments: { type: Array, required: true },
    camp: { type: Object, default: null },
    loading: { type: Boolean, default: false },
    showContext: { type: Boolean, default: false },
    emptyText: { type: String, default: null },
    statusFilter: { type: String, default: 'open' },
  },
  emits: ['replied'],
  data() {
    return { filterPage: 0, filterTransition: '', retainedThreads: new Set() }
  },
  computed: {
    visibleComments() {
      return filterComments(this.comments, this.statusFilter, this.retainedThreads)
    },
    roots() {
      return this.visibleComments.filter(
        (comment) => typeof comment.parent !== 'function'
      )
    },
  },
  watch: {
    statusFilter(next, previous) {
      this.retainedThreads.clear()
      if (
        (previous === 'open' && next === 'resolved') ||
        (previous === 'resolved' && next === 'open')
      ) {
        this.filterTransition = next === 'resolved' ? 'left' : 'right'
        this.filterPage += 1
      } else {
        this.filterTransition = ''
      }
    },
  },
  methods: {
    retainThread(uri, retained) {
      if (retained) this.retainedThreads.add(uri)
      else this.retainedThreads.delete(uri)
    },
    captureLeavingContent(vnode, root) {
      if (root && !root.resolvedAt && vnode.el.dataset.statusFilter === 'resolved') {
        vnode.el.classList.add('ec-comments-list__item--reopened')
      }
      // Tiptap destroys its DOM during unmount, before the leave animation starts.
      leavingContent.set(vnode.el, vnode.el.cloneNode(true))
    },
    restoreLeavingContent(element) {
      const snapshot = leavingContent.get(element)
      if (!snapshot) return
      element.replaceChildren(...snapshot.childNodes)
      element.inert = true
      leavingContent.delete(element)
    },
    repliesTo(root) {
      return this.visibleComments.filter(
        (comment) =>
          typeof comment.parent === 'function' &&
          comment.parent()._meta.self === root._meta.self
      )
    },
    scrollToBottom() {
      const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches
      this.$el.scrollTo({
        top: this.$el.scrollHeight,
        behavior: reduceMotion ? 'auto' : 'smooth',
      })
    },
  },
}
</script>

<style scoped>
.ec-comments-list {
  --fade: 24px;
  --fade-padding: 17px;

  overflow-x: hidden;
  padding-top: var(--fade-padding);
  padding-bottom: var(--fade-padding);
  mask-image: linear-gradient(
    to bottom,
    transparent 0,
    #000 var(--fade),
    #000 calc(100% - var(--fade)),
    transparent 100%
  );
}

.ec-comments-list__page {
  min-width: 0;
}

.ec-filter-left-enter-active,
.ec-filter-left-leave-active,
.ec-filter-right-enter-active,
.ec-filter-right-leave-active {
  transition:
    transform 0.2s ease,
    opacity 0.2s ease;
}

.ec-filter-left-enter-from,
.ec-filter-right-leave-to {
  transform: translateX(32px);
  opacity: 0;
}

.ec-filter-left-leave-to,
.ec-filter-right-enter-from {
  transform: translateX(-32px);
  opacity: 0;
}

.ec-comments-list__item {
  display: grid;
  grid-template-rows: 1fr;
}

.ec-comments-list__item > * {
  min-height: 0;
  transition: opacity 0.7s ease;
}

.ec-comments-list__item--muted > * {
  opacity: 0.5;
}

.ec-thread-enter-active,
.ec-thread-leave-active {
  transition:
    grid-template-rows 0.6s cubic-bezier(0.4, 0, 0.2, 1),
    margin-bottom 0.6s cubic-bezier(0.4, 0, 0.2, 1),
    opacity 0.6s,
    transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.ec-thread-enter-active > *,
.ec-thread-leave-active > * {
  overflow: hidden;
}

.ec-thread-enter-from,
.ec-thread-leave-to {
  grid-template-rows: 0fr;
  margin-bottom: -16px;
  opacity: 0;
}

.ec-thread-leave-to.ec-comments-list__item--resolved {
  transform: translateX(100%);
}

.ec-thread-leave-active.ec-comments-list__item--resolved,
.ec-thread-leave-active.ec-comments-list__item--reopened {
  transition:
    transform 0.4s ease-in,
    opacity 0.4s 0.15s,
    grid-template-rows 0.4s 0.15s,
    margin-bottom 0.4s 0.15s;
}

.ec-thread-leave-to.ec-comments-list__item--reopened {
  transform: translateX(-100%);
}

@media (prefers-reduced-motion: reduce) {
  .ec-comments-list__item > *,
  .ec-filter-left-enter-active,
  .ec-filter-left-leave-active,
  .ec-filter-right-enter-active,
  .ec-filter-right-leave-active,
  .ec-thread-enter-active,
  .ec-thread-leave-active,
  .ec-thread-leave-active.ec-comments-list__item--resolved,
  .ec-thread-leave-active.ec-comments-list__item--reopened {
    transition: none;
  }
}
</style>
