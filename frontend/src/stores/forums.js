import { defineStore } from "pinia";
import axios from "@/axios";

export const useForumStore = defineStore("forums", {
  state: () => ({
    topics: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchTopics() {
      this.loading = true;
      try {
        const { data } = await axios.get("/api/forums");
        this.topics = data.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    async createTopic(topicData) {
      try {
        const { data } = await axios.post("/api/forums", topicData);
        this.topics.unshift(data.data);
        return true;
      } catch (error) {
        this.error = error.message;
        return false;
      }
    },
    async addComment(topicId, commentData) {
      try {
        const { data } = await axios.post(`/api/forums/${topicId}/comments`, commentData);
        const topic = this.topics.find((t) => t.id === topicId);
        if (topic) {
          if (!topic.comments) topic.comments = [];
          topic.comments.push(data.data);
        }
        return true;
      } catch (error) {
        this.error = error.message;
        return false;
      }
    },
  },
});
