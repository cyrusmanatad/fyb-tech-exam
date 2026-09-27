<script setup lang="ts">
import { ref } from 'vue'
import { useUiStore } from '@/stores/ui'
import {
  Bars3Icon,
  PlusIcon,
  MagnifyingGlassIcon,
  ArrowLeftIcon,
  PhoneIcon,
  EllipsisVerticalIcon,
  PaperClipIcon,
  PaperAirplaneIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'
import BaseModal from '@/components/common/BaseModal.vue'

const uiStore = useUiStore()

const avatarSrc = (name: string) =>
  `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=${uiStore.avatarBackground}&color=fff`

const mobileView = ref<'list' | 'chat'>('list')
const composeModal = ref(false)

const activeChat = ref({
  name: 'Alice Johnson',
  status: 'Online',
  lastSeen: 'Active now'
})

const chats = [
  { name: 'Alice Johnson', msg: 'Hi, is the order #ORD-77221 shipped?', time: '2m', unread: true },
  { name: 'Mark Wilson', msg: 'Thank you for the quick support!', time: '1h', unread: false },
  { name: 'Sarah Lee', msg: 'I received a damaged item...', time: '3h', unread: false },
  { name: 'David Smith', msg: 'When will the Airpods be in stock?', time: 'Yesterday', unread: false }
]

const selectChat = (chat: { name: string; msg: string; time: string; unread: boolean }) => {
  activeChat.value = { ...chat, status: 'Online', lastSeen: 'Active now' }
  mobileView.value = 'chat'
}
</script>

<template>
  <div class="h-screen flex flex-col">
    <!-- HEADER -->
    <header
      class="bg-white dark:bg-dark-card border-b border-gray-200 dark:border-dark-border p-4 flex items-center justify-between"
    >
      <div class="flex items-center gap-4">
        <button
          @click="uiStore.setSidebar(true)"
          class="lg:hidden p-2 bg-gray-50 dark:bg-slate-800 border dark:border-dark-border rounded-xl"
          type="button"
        >
          <Bars3Icon class="w-6 h-6" />
        </button>
        <div class="hidden sm:block">
          <h1 class="text-xl font-bold text-gray-900 dark:text-white">Customer Inbox</h1>
          <p class="text-[11px] text-gray-500 dark:text-slate-400">Responsive real-time support</p>
        </div>
      </div>
      <button
        @click="composeModal = true"
        class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-teal-500/20"
        type="button"
      >
        <PlusIcon class="w-4 h-4" /> New Message
      </button>
    </header>

    <!-- INBOX CONTAINER -->
    <div class="flex-1 flex overflow-hidden">
      <!-- CHAT LIST PANEL -->
      <div
        :class="mobileView === 'chat' ? 'hidden md:flex' : 'flex'"
        class="w-full md:w-80 lg:w-96 border-r border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card flex-col"
      >
        <div class="p-4">
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
            <input
              type="text"
              placeholder="Search conversations..."
              class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl text-xs outline-none focus:ring-2 focus:ring-teal-500/20 transition"
            />
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
          <button
            v-for="chat in chats"
            :key="chat.name"
            @click="selectChat(chat)"
            class="w-full p-4 flex items-start gap-3 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition border-b border-gray-50 dark:border-dark-border/50 relative"
            type="button"
          >
            <div
              v-if="chat.unread"
              class="absolute left-1 top-1/2 -translate-y-1/2 w-1 h-8 bg-teal-500 rounded-r-full"
            ></div>
            <img
              :src="`https://ui-avatars.com/api/?name=${chat.name}&background=random&color=fff`"
              class="w-10 h-10 rounded-full"
              alt="Avatar"
            />
            <div class="flex-1 text-left min-w-0">
              <div class="flex justify-between items-center mb-0.5">
                <span class="text-sm font-bold text-gray-900 dark:text-white truncate">{{
                  chat.name
                }}</span>
                <span class="text-[10px] text-gray-400">{{ chat.time }}</span>
              </div>
              <p
                class="text-xs text-gray-500 dark:text-slate-400 truncate"
                :class="chat.unread && 'font-bold text-gray-900 dark:text-white'"
              >
                {{ chat.msg }}
              </p>
            </div>
          </button>
        </div>
      </div>

      <!-- CHAT CONTENT PANEL -->
      <div
        :class="mobileView === 'list' ? 'hidden md:flex' : 'flex'"
        class="flex-1 flex flex-col bg-gray-50 dark:bg-slate-950/20"
      >
        <!-- Chat Header -->
        <div
          class="bg-white dark:bg-dark-card border-b border-gray-200 dark:border-dark-border p-4 flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <button
              @click="mobileView = 'list'"
              class="md:hidden p-2 -ml-2 text-gray-400"
              type="button"
            >
              <ArrowLeftIcon class="w-5 h-5" />
            </button>
            <div class="relative">
              <img
                :src="avatarSrc(activeChat.name)"
                class="w-10 h-10 rounded-full"
                alt="Active Chat Avatar"
              />
              <div
                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-dark-card rounded-full"
              ></div>
            </div>
            <div>
              <h2 class="text-sm font-bold text-gray-900 dark:text-white">
                {{ activeChat.name }}
              </h2>
              <p class="text-[10px] text-green-500 font-bold uppercase tracking-widest">
                {{ activeChat.status }}
              </p>
            </div>
          </div>
          <div class="flex gap-1">
            <button
              class="inline-flex min-h-11 min-w-11 items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition"
              type="button"
              aria-label="Call"
            >
              <PhoneIcon class="w-4 h-4" />
            </button>
            <button
              class="inline-flex min-h-11 min-w-11 items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition"
              type="button"
              aria-label="More"
            >
              <EllipsisVerticalIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Messages Thread -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
          <!-- Received -->
          <div class="flex items-end gap-3 max-w-[80%]">
            <img
              :src="avatarSrc(activeChat.name)"
              class="w-8 h-8 rounded-full"
              alt="Message Avatar"
            />
            <div class="space-y-1">
              <div
                class="bg-white dark:bg-dark-card p-4 rounded-2xl rounded-bl-none border border-gray-100 dark:border-dark-border shadow-sm"
              >
                <p class="text-sm leading-relaxed text-gray-700 dark:text-slate-300">
                  Hi support, I was wondering if the order
                  <span class="font-bold text-teal-600">#ORD-77221</span> has been processed yet? I
                  need it for a gift this weekend.
                </p>
              </div>
              <span class="text-[10px] text-gray-400 ml-1">10:42 AM</span>
            </div>
          </div>

          <!-- Sent -->
          <div class="flex items-end gap-3 max-w-[80%] ml-auto flex-row-reverse">
            <div
              class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center text-[10px] text-white font-bold"
            >
              YOU
            </div>
            <div class="space-y-1">
              <div class="bg-teal-500 p-4 rounded-2xl rounded-br-none shadow-lg shadow-teal-500/20">
                <p class="text-sm leading-relaxed text-white">
                  Hello Alice! Let me check that for you. Yes, it was picked up by the courier this
                  morning. You should receive a tracking link in 1-2 hours.
                </p>
              </div>
              <span class="text-[10px] text-gray-400 text-right block mr-1">10:45 AM • Read</span>
            </div>
          </div>
        </div>

        <!-- Message Input -->
        <div class="p-4 bg-white dark:bg-dark-card border-t border-gray-200 dark:border-dark-border">
          <div
            class="flex items-center gap-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-2xl p-2 pr-3"
          >
            <button class="p-2 text-gray-400 hover:text-teal-500 transition" type="button">
              <PaperClipIcon class="w-5 h-5" />
            </button>
            <input
              type="text"
              placeholder="Type your reply..."
              class="min-h-11 flex-1 bg-transparent border-none text-sm dark:text-white px-2 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700"
            />
            <button
              class="inline-flex min-h-11 min-w-11 items-center justify-center bg-teal-700 hover:bg-teal-800 text-white rounded-xl transition-all shadow-md shadow-teal-500/20"
              type="button"
              aria-label="Send reply"
            >
              <PaperAirplaneIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <BaseModal :show="composeModal" @close="composeModal = false">
      <div
        class="p-6 border-b dark:border-dark-border flex justify-between items-center bg-gray-50/50 dark:bg-slate-800/20"
      >
        <div>
          <h2 class="text-xl font-black text-gray-900 dark:text-white">Compose Message</h2>
          <p class="text-xs text-gray-500 dark:text-slate-400">Start a new customer conversation</p>
        </div>
        <button
          @click="composeModal = false"
          class="p-2 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-full transition text-gray-400 dark:text-slate-500"
          type="button"
        >
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>
      <form class="p-6 space-y-5 overflow-y-auto custom-scrollbar">
        <div>
          <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5"
            >Recipient</label
          >
          <input
            type="text"
            placeholder="Search customer by name or ID..."
            class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl dark:text-white focus:ring-2 focus:ring-teal-500/20 outline-none transition"
          />
        </div>
        <div>
          <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5"
            >Message</label
          >
          <textarea
            rows="4"
            placeholder="Write your message here..."
            class="w-full px-4 py-3 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-dark-border rounded-xl dark:text-white focus:ring-2 focus:ring-teal-500/20 outline-none transition"
          ></textarea>
        </div>
      </form>
      <div class="p-6 bg-gray-50 dark:bg-slate-800/20 border-t dark:border-dark-border flex justify-end gap-3">
        <button
          @click="composeModal = false"
          class="px-5 py-2.5 text-sm font-bold text-gray-500 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-800 rounded-xl transition"
          type="button"
        >
          Cancel
        </button>
        <button
          @click="composeModal = false"
          class="px-6 py-2.5 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl shadow-lg transition active:scale-95"
          type="button"
        >
          Send Message
        </button>
      </div>
    </BaseModal>
  </div>
</template>
