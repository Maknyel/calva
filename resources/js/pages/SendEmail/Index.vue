<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Send Email</h1>
      <p class="text-gray-600 mt-1">Send custom emails to distributors and suppliers</p>
    </div>

    <!-- Email Form -->
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl">
      <form @submit.prevent="sendEmail">
        <!-- Email Dropdown -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Email</label>
          <select
            v-model="form.email"
            class="w-full border rounded px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
            <option value="" disabled>Select an email</option>
            <option
              v-for="email in emails"
              :key="email"
              :value="email"
            >
              {{ email }}
            </option>
          </select>
        </div>

        <!-- Subject -->
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Subject</label>
          <input
            v-model="form.subject"
            type="text"
            class="w-full border rounded px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            placeholder="Enter email subject"
          />
        </div>

        <!-- Message -->
        <div class="mb-6">
          <label class="block text-gray-700 mb-2 font-medium">Message</label>
          <textarea
            v-model="form.message"
            class="w-full border rounded px-3 py-2 text-black focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="8"
            required
            placeholder="Enter your message here..."
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="resetForm"
            class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100 text-gray-700"
          >
            Clear
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Sending...' : 'Send Email' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Success/Error Message -->
    <div v-if="message" :class="messageClass" class="mt-4 p-4 rounded-lg max-w-2xl">
      {{ message }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'SendEmail',
  data() {
    return {
      emails: [],
      form: {
        email: '',
        subject: '',
        message: ''
      },
      loading: false,
      message: '',
      messageType: ''
    };
  },
  computed: {
    messageClass() {
      if (this.messageType === 'success') {
        return 'bg-green-100 border border-green-400 text-green-700';
      } else if (this.messageType === 'error') {
        return 'bg-red-100 border border-red-400 text-red-700';
      }
      return '';
    }
  },
  mounted() {
    this.fetchEmails();
  },
  methods: {
    async fetchEmails() {
      try {
        const response = await axios.get('/api/emails/list');
        if (response.data.success) {
          this.emails = response.data.emails;
        }
      } catch (error) {
        console.error('Error fetching emails:', error);
        this.showMessage('Failed to load email list', 'error');
      }
    },
    async sendEmail() {
      this.loading = true;
      this.message = '';

      try {
        const response = await axios.post('/api/emails/send', this.form);

        if (response.data.success) {
          this.showMessage('Email sent successfully!', 'success');
          console.log('Email sent:', response.data);
          this.resetForm();
        } else {
          this.showMessage(response.data.message || 'Failed to send email', 'error');
        }
      } catch (error) {
        console.error('Error sending email:', error);
        const errorMessage = error.response?.data?.message || 'An error occurred while sending the email';
        this.showMessage(errorMessage, 'error');
      } finally {
        this.loading = false;
      }
    },
    resetForm() {
      this.form = {
        email: '',
        subject: '',
        message: ''
      };
      this.message = '';
    },
    showMessage(msg, type) {
      this.message = msg;
      this.messageType = type;

      setTimeout(() => {
        this.message = '';
        this.messageType = '';
      }, 5000);
    }
  }
};
</script>
