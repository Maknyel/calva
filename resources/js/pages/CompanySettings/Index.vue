<template>
  <div class="container mx-auto p-6">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Company Settings</h1>

        <form @submit.prevent="saveSettings">
          <!-- Company Name -->
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
              Company Name <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="form.company_name"
              required
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="Enter company name"
            />
          </div>

          <!-- Company Logo Path
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
              Company Logo Path
            </label>
            <input
              type="text"
              v-model="form.company_logo"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., assets/images/logo.jpg"
            />
            <p class="text-sm text-gray-500 mt-1">Relative path to your logo image</p>
          </div> -->

          <!-- Company Address -->
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
              Company Address <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.company_address"
              required
              rows="3"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="Enter company address"
            ></textarea>
          </div>

          <!-- Company Phone -->
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
              Company Phone
            </label>
            <input
              type="text"
              v-model="form.company_phone"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., (123) 456-7890"
            />
          </div>

          <!-- Company Email -->
          <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">
              Company Email
            </label>
            <input
              type="email"
              v-model="form.company_email"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., info@company.com"
            />
          </div>

          <!-- Company Tax ID -->
          <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
              Tax ID / TIN
            </label>
            <input
              type="text"
              v-model="form.company_tax_id"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="e.g., 123-456-789-000"
            />
          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-between">
            <button
              type="submit"
              :disabled="loading"
              class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50"
            >
              <span v-if="loading">Saving...</span>
              <span v-else>Save Settings</span>
            </button>

            <p v-if="successMessage" class="text-green-600 font-medium">
              {{ successMessage }}
            </p>
            <p v-if="errorMessage" class="text-red-600 font-medium">
              {{ errorMessage }}
            </p>
          </div>
        </form>
      </div>

      <!-- Preview Section -->
      <div class="bg-gray-50 shadow-md rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Receipt Preview</h2>
        <div class="bg-white p-4 rounded border">
          <div class="text-center mb-3">
            <h3 class="font-bold text-lg">{{ form.company_name || 'Company Name' }}</h3>
            <p class="text-sm text-gray-600">{{ form.company_address || 'Company Address' }}</p>
            <p class="text-xs text-gray-500">
              <span v-if="form.company_phone">Tel: {{ form.company_phone }}</span>
              <span v-if="form.company_email"> | Email: {{ form.company_email }}</span>
            </p>
            <p v-if="form.company_tax_id" class="text-xs text-gray-500">
              Tax ID: {{ form.company_tax_id }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CompanySettings',
  data() {
    return {
      form: {
        company_name: '',
        company_logo: '',
        company_address: '',
        company_phone: '',
        company_email: '',
        company_tax_id: '',
      },
      loading: false,
      successMessage: '',
      errorMessage: '',
    };
  },
  methods: {
    async fetchSettings() {
      try {
        const response = await axios.get('/api/company-settings');
        if (response.data.success && response.data.data) {
          this.form = {
            company_name: response.data.data.company_name || '',
            company_logo: response.data.data.company_logo || '',
            company_address: response.data.data.company_address || '',
            company_phone: response.data.data.company_phone || '',
            company_email: response.data.data.company_email || '',
            company_tax_id: response.data.data.company_tax_id || '',
          };
        }
      } catch (error) {
        console.error('Error fetching company settings:', error);
        this.errorMessage = 'Failed to load company settings';
      }
    },
    async saveSettings() {
      this.loading = true;
      this.successMessage = '';
      this.errorMessage = '';

      try {
        const response = await axios.put('/api/company-settings', this.form);

        if (response.data.success) {
          this.successMessage = 'Settings saved successfully!';
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        }
      } catch (error) {
        console.error('Error saving company settings:', error);
        this.errorMessage = error.response?.data?.message || 'Failed to save settings';
        setTimeout(() => {
          this.errorMessage = '';
        }, 3000);
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchSettings();
  },
};
</script>

<style scoped>
/* Add any custom styles if needed */
</style>
