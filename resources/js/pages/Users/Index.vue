<template>
  <div class="p-6">
    <!-- Header with Add User Button -->
    <div class="flex justify-between items-center mb-4">
      <input type="text" v-model="searchQuery" placeholder="Search users..."
        class="border rounded px-3 py-2 w-full max-w-sm" />
      <button @click="openModal()" class="ml-4 bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
        Add User
      </button>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="user in users" :key="user.id" class="bg-white shadow rounded overflow-hidden flex flex-col">
        <!-- Top section with background image -->
        <div class="gap-1 w-full h-40 bg-cover bg-center flex flex-col justify-end items-center text-white px-4 pb-2"
          :style="{ backgroundImage: `url('${user.header_image}')` }">
          <h3 class="p-1 rounded bg-gray-500 text-lg font-semibold">{{ user.fname }} {{ user.lname }}</h3>
          <p class="p-1 rounded bg-gray-500 text-sm font-medium">Role: {{ user.role.name }}</p>
        </div>

        <!-- Bottom content -->
        <div class="p-4 flex flex-col items-center">
          <p class="text-gray-500 mb-2">{{ user.email }}</p>
          <p class="text-gray-500 mb-2">{{ user.contact_number }}</p>
          <div class="flex space-x-2">
            <button @click="openModal(user)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
              Edit
            </button>
            <button @click="deleteUser(user.id)" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center items-center space-x-2">
      <button @click="prevPage" :disabled="currentPage === 1"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
        Prev
      </button>
      <span>Page {{ currentPage }} / {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded hover:bg-gray-100 disabled:opacity-50">
        Next
      </button>
    </div>

    <!-- Add/Edit User Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg w-full max-w-md p-6 relative">
        <h2 class="text-xl font-semibold mb-4">{{ editingUser ? 'Edit User' : 'Add User' }}</h2>
        <form @submit.prevent="saveUser">
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">First Name</label>
            <input v-model="form.fname" class="w-full border rounded px-2 py-1 text-black" required />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Last Name</label>
            <input v-model="form.lname" class="w-full border rounded px-2 py-1 text-black" required />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full border rounded px-2 py-1 text-black" required />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Contact Number</label>
            <input v-model="form.contact_number" type="contact_number"
              class="w-full border rounded px-2 py-1 text-black" required />
          </div>
          <div class="mb-4" v-if="!editingUser">
            <label class="block text-gray-700 mb-1">Password</label>
            <input v-model="form.password" type="password" class="w-full border rounded px-2 py-1 text-black"
              placeholder="Leave empty for default password" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 mb-1">Role</label>
            <select v-model="form.role_id" class="w-full border rounded px-2 py-1 text-black" required>
              <option v-for="role in roles" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
          </div>

          <div class="flex justify-end space-x-2">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800">
              {{ editingUser ? 'Update' : 'Add' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Users',
  data() {
    return {
      searchQuery: '',
      currentPage: 1,
      usersPerPage: 6,
      users: [],
      totalPages: 1,
      showModal: false,
      editingUser: null,
      form: {
        user_id: 0,
        fname: '',
        lname: '',
        email: '',
        contact_number: '',
        password: '',
        role: '',
      },
      roles: [], // add more as needed
    };
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
      this.fetchUsers();
    },
    currentPage() {
      this.fetchUsers();
    },
  },
  async mounted() {
    const user = JSON.parse(sessionStorage.getItem('user'));
    this.form.user_id = user?.id;
    await this.fetchRoles();
    this.fetchUsers();
  },
  methods: {
    async fetchRoles() {
      const token = sessionStorage.getItem('token');
      if (!token) return;

      try {
        const res = await axios.get('/api/roles', {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.roles = res.data;
      } catch (err) {
        console.error(err);
      }
    },
    getTokenHeader() {
      const token = sessionStorage.getItem('token');

      if (!token) {
        this.$router.push({ name: 'Login' });
        return null;
      }
      return { Authorization: `Bearer ${token}` };
    },

    async fetchUsers() {
      const user = sessionStorage.getItem('user');
      const headers = this.getTokenHeader();
      if (!headers) return;

      try {
        const res = await axios.get('/api/users', {
          headers,
          params: {
            user_id: JSON.parse(user)?.id,
            search: this.searchQuery,
            page: this.currentPage,
            per_page: this.usersPerPage,
          },
        });
        this.users = res.data.data;
        this.totalPages = res.data.last_page;
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to fetch users');
      }
    },

    openModal(user = null) {
      this.editingUser = user;
      if (user) {
        this.form = { ...user, password: '' };
      } else {
        this.form = { fname: '', lname: '', email: '', contact_number: '', password: '', role: '', user_id: this.form.user_id };
      }
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
      this.editingUser = null;
    },

    async saveUser() {
      const headers = this.getTokenHeader();
      if (!headers) return;

      try {
        if (this.editingUser) {
          await axios.put(`/api/users/${this.editingUser.id}`, this.form, { headers });
          this.$toast.success('User updated successfully');
        } else {
          await axios.post('/api/users', this.form, { headers });
          this.$toast.success('User added successfully');
        }
        this.fetchUsers();
        this.closeModal();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to save user');
      }
    },

    async deleteUser(userId) {
      if (!confirm('Are you sure you want to delete this user?')) return;

      const headers = this.getTokenHeader();
      if (!headers) return;

      try {
        await axios.delete(`/api/users/${userId}`, { headers });
        this.$toast.success('User deleted successfully');
        this.fetchUsers();
      } catch (err) {
        console.error(err);
        this.$toast.error('Failed to delete user');
      }
    },

    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },

    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
  },
};
</script>

<style scoped>
/* optional styling */
</style>
