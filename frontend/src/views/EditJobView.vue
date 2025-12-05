<script setup>
import { onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";

const state = reactive({
  data : {
    type: "Full-Time",
    title: "",
    description: "",
    salary: "",
    location: "",
    company: {
      name: "",
      description: "",
      contactEmail: "",
      contactPhone: "",
    },
  }
});

const route = useRoute();
const router = useRouter();

const jobId = route.params.id;

const toast = useToast();

const handleUpdate = async () => {
  const updatedJob = {
    type: state.data.type,
    title: state.data.title,
    description: state.data.description,
    salary: state.data.salary,
    location: state.data.location,
    company: {
      name: state.data.company.name,
      description: state.data.company.description,
      contactEmail: state.data.company.contactEmail,
      contactPhone: state.data.company.contactPhone,
    },
  };

  try {
    const response = await axios.put(`/api/job-listings/${jobId}`, updatedJob);
    toast.success("Job Updated Successfully");
    router.push(`/jobs/${response.data.id}`);
  } catch (error) {
    console.log(error);
    toast.error("Job Was Not Updated");
  }
};

onMounted(async () => {
  try {
    const response = await axios.get(`/api/job-listings/${jobId}`);
    state.data = response.data;
  } catch (error) {
  }
});
</script>

<template>
  <section class="bg-green-50">
    <div class="container m-auto max-w-2xl py-24">
      <div
        class="m-4 mb-4 rounded-md border bg-white px-6 py-8 shadow-md md:m-0"
      >
        <form @submit.prevent="handleUpdate">
          <h2 class="mb-6 text-center text-3xl font-semibold">Edit Job</h2>

          <div class="mb-4">
            <label for="type" class="mb-2 block font-bold text-gray-700"
              >Job Type</label
            >
            <select
              v-model="state.data.type"
              id="type"
              name="type"
              class="w-full rounded border px-3 py-2"
              required
            >
              <option value="Full-Time">Full-Time</option>
              <option value="Part-Time">Part-Time</option>
              <option value="Remote">Remote</option>
              <option value="Internship">Internship</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="mb-2 block font-bold text-gray-700"
              >Job Listing Name</label
            >
            <input
              v-model="state.data.title"
              type="text"
              id="name"
              name="name"
              class="mb-2 w-full rounded border px-3 py-2"
              placeholder="eg. Beautiful Apartment In Miami"
              required
            />
          </div>
          <div class="mb-4">
            <label for="description" class="mb-2 block font-bold text-gray-700"
              >Description</label
            >
            <textarea
              v-model="state.data.description"
              id="description"
              name="description"
              class="w-full rounded border px-3 py-2"
              rows="4"
              placeholder="Add any job duties, expectations, requirements, etc"
            ></textarea>
          </div>

          <div class="mb-4">
            <label for="type" class="mb-2 block font-bold text-gray-700"
              >Salary</label
            >
            <select
              v-model="state.data.salary"
              id="salary"
              name="salary"
              class="w-full rounded border px-3 py-2"
              required
            >
              <option value="Under PHP 50K">under PHP 50K</option>
              <option value="PHP 50K - 60K">PHP 50K - 60K</option>
              <option value="PHP 60K - 70K">PHP 60K - 70K</option>
              <option value="PHP 70K - 80K">PHP 70K - 80K</option>
              <option value="PHP 80K - 90K">PHP 80K - 90K</option>
              <option value="PHP 90K - 100K">PHP 90K - 100K</option>
              <option value="PHP 100K - 125K">PHP 100K - 125K</option>
              <option value="PHP 125K - 150K">PHP 125K - 150K</option>
              <option value="PHP 150K - 175K">PHP 150K - 175K</option>
              <option value="PHP 175K - 200K">PHP 175K - 200K</option>
              <option value="Over PHP 200K">Over PHP 200K</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="mb-2 block font-bold text-gray-700"> Location </label>
            <input
              v-model="state.data.location"
              type="text"
              id="location"
              name="location"
              class="mb-2 w-full rounded border px-3 py-2"
              placeholder="Company Location"
              required
            />
          </div>

          <h3 class="mb-5 text-2xl">Company Info</h3>

          <div class="mb-4">
            <label for="company" class="mb-2 block font-bold text-gray-700"
              >Company Name</label
            >
            <input
              v-model="state.data.company.name"
              type="text"
              id="company"
              name="company"
              class="w-full rounded border px-3 py-2"
              placeholder="Company Name"
            />
          </div>

          <div class="mb-4">
            <label
              for="company_description"
              class="mb-2 block font-bold text-gray-700"
              >Company Description</label
            >
            <textarea
              v-model="state.data.company.description"
              id="company_description"
              name="company_description"
              class="w-full rounded border px-3 py-2"
              rows="4"
              placeholder="What does your company do?"
            ></textarea>
          </div>

          <div class="mb-4">
            <label
              for="contact_email"
              class="mb-2 block font-bold text-gray-700"
              >Contact Email</label
            >
            <input
              v-model="state.data.company.contactEmail"
              type="email"
              id="contact_email"
              name="contact_email"
              class="w-full rounded border px-3 py-2"
              placeholder="Email address for applicants"
              required
            />
          </div>
          <div class="mb-4">
            <label
              for="contact_phone"
              class="mb-2 block font-bold text-gray-700"
              >Contact Phone</label
            >
            <input
              v-model="state.data.company.contactPhone"
              type="tel"
              id="contact_phone"
              name="contact_phone"
              class="w-full rounded border px-3 py-2"
              placeholder="Optional phone for applicants"
            />
          </div>

          <div>
            <button
              class="focus:shadow-outline w-full rounded-full bg-green-500 px-4 py-2 font-bold text-white hover:bg-green-600 focus:outline-none"
              type="submit"
            >
              Update Job
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>
