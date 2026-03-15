<script setup>
import { onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useForumStore } from "@/stores/forums";
import PulseLoader from "vue-spinner/src/PulseLoader.vue";

const auth = useAuthStore();
const forumStore = useForumStore();

const newTopic = ref({
  title: "",
  content: "",
});

const newComments = ref({}); // Store new comment text by topicId
const creatingTopic = ref(false);
const addingComment = ref({});

onMounted(async () => {
  await forumStore.fetchTopics();
});

const handleCreateTopic = async () => {
  if (!newTopic.value.title || !newTopic.value.content) return;

  creatingTopic.value = true;
  const success = await forumStore.createTopic({
    user_id: auth.user.id,
    title: newTopic.value.title,
    content: newTopic.value.content,
  });
  creatingTopic.value = false;

  if (success) {
    newTopic.value.title = "";
    newTopic.value.content = "";
  }
};

const handleAddComment = async (topicId) => {
  const content = {
    comment: newComments.value[topicId],
    forum_id: topicId,
    user_id: auth.user.id,
  };

  console.log(auth.user);
  

  if (!content) return;

  addingComment.value[topicId] = true;
  const success = await forumStore.addComment(topicId, { ...content });
  addingComment.value[topicId] = false;

  if (success) {
    newComments.value[topicId] = "";
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl">
      <h1 class="mb-8 text-3xl font-extrabold text-gray-900">
        Community Forum
      </h1>

      <!-- Create Topic Form -->
      <div
        v-if="auth.user"
        class="mb-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
      >
        <h2 class="mb-4 text-xl font-semibold text-gray-800">
          Start a New Topic
        </h2>
        <form @submit.prevent="handleCreateTopic">
          <div class="mb-4">
            <label
              for="title"
              class="mb-1 block text-sm font-medium text-gray-700"
              >Title</label
            >
            <input
              id="title"
              v-model="newTopic.title"
              type="text"
              placeholder="What's on your mind?"
              class="w-full rounded-md border border-gray-300 px-4 py-2 transition outline-none focus:border-blue-500 focus:ring-blue-500"
              required
            />
          </div>
          <div class="mb-4">
            <label
              for="content"
              class="mb-1 block text-sm font-medium text-gray-700"
              >Content</label
            >
            <textarea
              id="content"
              v-model="newTopic.content"
              rows="3"
              placeholder="Tell us more..."
              class="w-full rounded-md border border-gray-300 px-4 py-2 transition outline-none focus:border-blue-500 focus:ring-blue-500"
              required
            ></textarea>
          </div>
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="creatingTopic"
              class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
            >
              Post Topic
              <PulseLoader
                v-show="creatingTopic"
                class="ml-2"
                size="8px"
                color="white"
              />
            </button>
          </div>
        </form>
      </div>
      <div v-else class="mb-8 border-l-4 border-blue-400 bg-blue-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              class="h-5 w-5 text-blue-400"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-blue-700">
              Please
              <router-link
                to="/login"
                class="font-medium underline hover:text-blue-600"
                >log in</router-link
              >
              to start a new topic or join the conversation.
            </p>
          </div>
        </div>
      </div>

      <!-- Topics List -->
      <div v-if="forumStore.loading" class="space-y-4">
        <div
          v-for="i in 3"
          :key="i"
          class="animate-pulse rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
        >
          <div class="mb-4 h-4 w-3/4 rounded bg-gray-200"></div>
          <div class="mb-2 h-3 w-full rounded bg-gray-200"></div>
          <div class="h-3 w-5/6 rounded bg-gray-200"></div>
        </div>
      </div>

      <div
        v-else-if="forumStore.topics?.length === 0"
        class="rounded-lg border-2 border-dashed border-gray-300 bg-white py-12 text-center"
      >
        <p class="text-gray-500">
          No topics yet. Be the first to start a conversation!
        </p>
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="topic in forumStore.topics"
          :key="topic.id"
          class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
        >
          <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-xl font-bold text-gray-900">{{ topic.title }}</h3>
              <span class="text-xs text-gray-500">{{
                new Date(topic.created_at).toLocaleDateString()
              }}</span>
            </div>
            <p class="mb-6 whitespace-pre-wrap text-gray-700">
              {{ topic.content }}
            </p>

            <div
              class="mb-4 flex items-center border-t pt-4 text-sm text-gray-500"
            >
              <span class="mr-2 font-medium"
                >Posted by {{ topic.user?.name || "Anonymous" }}</span
              >
              <span>&bull; {{ topic.comments?.length || 0 }} comments</span>
            </div>

            <!-- Comments Section -->
            <div class="mt-4 space-y-4">
              <div
                v-for="comment in topic.comments"
                :key="comment.id"
                class="rounded-md bg-gray-50 p-4"
              >
                <div class="mb-1 flex items-center justify-between">
                  <span class="text-sm font-semibold text-gray-800">{{
                    comment.user?.name || "Anonymous"
                  }}</span>
                  <span class="text-xs text-gray-400">{{ comment.humanize_datetime }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ comment.comment }}</p>
              </div>

              <!-- Add Comment Form -->
              <div v-if="auth.user" class="mt-4">
                <div class="flex space-x-2">
                  <input
                    v-model="newComments[topic.id]"
                    type="text"
                    placeholder="Write a comment..."
                    class="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm transition outline-none focus:border-blue-500 focus:ring-blue-500"
                    @keyup.enter="handleAddComment(topic.id)"
                  />
                  <button
                    @click="handleAddComment(topic.id)"
                    :disabled="
                      addingComment[topic.id] || !newComments[topic.id]
                    "
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-medium text-white shadow-sm transition hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                  >
                    Reply
                    <PulseLoader
                      v-show="addingComment[topic.id]"
                      class="ml-2"
                      size="4px"
                      color="white"
                    />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
