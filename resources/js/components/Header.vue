<template>
  <header class="bg-purple-700 text-white shadow flex items-center justify-between px-6 py-3">
    <div class="flex items-center gap-1">
      <!-- Sidebar Toggle Button -->
      <button @click="callParent" class="mr-4 p-2 rounded bg-purple-600 hover:bg-purple-500 focus:outline-none">
        <!-- You can use an icon or text -->
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>

      <!-- Logo -->
      <div class="text-xl font-bold text-purple-500">{{ companyName }}</div>
    </div>
    <!-- User Dropdown -->
    <div class="relative" v-if="userData">
      <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">
        <!-- Circle icon/avatar -->
        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-purple-700 font-bold">
          {{ userInitials }}
        </div>
        <span class="hidden md:block text-purple-500">{{ userData.fname }} {{ userData.lname }}</span>
      </button>

      <!-- Dropdown menu -->
      <transition name="fade">
        <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded shadow-lg z-50">
          <button @click="openProfileModal" class="w-full text-left px-4 py-2 hover:bg-gray-100">Profile</button>
          <button @click="openPasswordModal" class="w-full text-left px-4 py-2 hover:bg-gray-100">Change
            Password</button>
          <hr class="my-1" />
          <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-600">Logout</button>
        </div>
      </transition>
    </div>

    <!-- Profile Modal -->
    <div v-if="showProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
      <div class="bg-white rounded-lg w-full max-w-lg p-6 relative z-50">
        <h2 class="text-xl font-semibold mb-4">Profile</h2>
        <form @submit.prevent="updateProfileInfo">
          <h3 class="text-lg font-semibold mb-2">Update Information</h3>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-gray-700 mb-1">First Name</label>
              <input v-model="profile.fname" class="w-full border rounded px-2 py-1 text-black" />
            </div>
            <div>
              <label class="block text-gray-700 mb-1">Last Name</label>
              <input v-model="profile.lname" class="w-full border rounded px-2 py-1 text-black" />
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Email</label>
            <input v-model="profile.email" type="email" class="w-full border rounded px-2 py-1 text-black" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Contact Number</label>
            <input v-model="profile.contact_number" class="w-full border rounded px-2 py-1 text-black" />
          </div>
          <div class="flex justify-end space-x-2 mb-2">
            <button type="button" @click="showProfileModal = false"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800">Save
              Info</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Change Password Modal -->
    <div v-if="showPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
      <div class="bg-white rounded-lg w-full max-w-md p-6 relative z-50">
        <h2 class="text-xl font-semibold mb-4">Change Password</h2>
        <form @submit.prevent="changePassword">
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">New Password</label>
            <input type="password" v-model="profile.password" class="w-full border rounded px-2 py-1 text-black" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Confirm Password</label>
            <input type="password" v-model="profile.password_confirmation"
              class="w-full border rounded px-2 py-1 text-black" />
          </div>
          <div class="flex justify-end space-x-2">
            <button type="button" @click="showPasswordModal = false"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Close</button>
            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800">Change
              Password</button>
          </div>
        </form>
      </div>
    </div>

  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Header',
  props: {
    user: Object
  },
  data() {
    return {
      userData: null,
      dropdownOpen: false,
      showProfileModal: false,
      showPasswordModal: false,
      companyName: '', // Default fallback
      profile: {
        fname: '',
        lname: '',
        email: '',
        contact_number: '',
        password: '',
        password_confirmation: ''
      }
    };
  },
  computed: {
    userInitials() {
      if (!this.userData) return '';
      return (this.userData.fname[0] || '') + (this.userData.lname[0] || '');
    }
  },
  mounted() {
    this.fetchUser();
    this.fetchCompanySettings();
  },
  methods: {
    callParent() {
      this.$emit('callParentFunction');
    },
    toggleDropdown() { this.dropdownOpen = !this.dropdownOpen; },
    openProfileModal() { this.showProfileModal = true; this.dropdownOpen = false; },
    openPasswordModal() { this.showPasswordModal = true; this.dropdownOpen = false; },

    async fetchUser() {
      this.profile = { ...this.user, password: '', password_confirmation: '' };
      this.userData = this.user;
    },

    async fetchCompanySettings() {
      const token = sessionStorage.getItem('token');
      try {
        const response = await axios.get('/api/company-settings', {
          headers: { Authorization: `Bearer ${token}` }
        });
        if (response.data && response.data.data.company_name) {
          
          this.companyName = response.data.data.company_name;
        }
      } catch (err) {
        console.error('Failed to fetch company settings:', err);
        // Keep default fallback value
      }
    },

    async updateProfileInfo() {
      const token = sessionStorage.getItem('token');
      try {
        await axios.put('/api/profile', {
          fname: this.profile.fname,
          lname: this.profile.lname,
          email: this.profile.email,
          contact_number: this.profile.contact_number
        }, { headers: { Authorization: `Bearer ${token}` } });

        this.userData = { ...this.userData, ...this.profile };
        this.$toast.success('Profile info updated successfully!');
        this.$root.fetchUser();
        this.showProfileModal = false;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to update profile info.');
      }
    },

    async changePassword() {
      if (!this.profile.password || this.profile.password !== this.profile.password_confirmation) {
        this.$toast.warning('Passwords do not match.');
        return;
      }

      const token = sessionStorage.getItem('token');
      try {
        await axios.put('/api/profile', {
          password: this.profile.password,
          password_confirmation: this.profile.password_confirmation
        }, { headers: { Authorization: `Bearer ${token}` } });

        this.$toast.success('Password changed successfully!');
        this.profile.password = '';
        this.profile.password_confirmation = '';
        this.showPasswordModal = false;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to change password.');
      }
    },

    async logout() {
      const token = sessionStorage.getItem('token');
      try {
        if (token) await axios.post('/api/logout', {}, { headers: { Authorization: `Bearer ${token}` } });
      } catch (err) {
        console.error(err);
      } finally {
        sessionStorage.removeItem('token');
        location.reload();
      }
    }
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
