<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-sm">
      <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>

      <form @submit.prevent="loginUser">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2">Email</label>
          <input
            v-model="email"
            type="email"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-purple-500"
            required
          />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 mb-2">Password</label>
          <input
            v-model="password"
            type="password"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-purple-500"
            required
          />
        </div>

        <button
          type="submit"
          class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition"
          :disabled="loading"
        >
          <span v-if="!loading">Login</span>
          <span v-else>Logging in...</span>
        </button>

        <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      loading: false,
      error: ''
    }
  },
  methods: {
    async loginUser() {
      this.loading = true
      this.error = ''

      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        })

        if (response.data.success) {
          // Save session data
          sessionStorage.setItem('user', JSON.stringify(response.data.user))
          sessionStorage.setItem('token', response.data.token)

          // Redirect to dashboard
          this.$root.fetchUser();
          this.$router.push({ name: 'Dashboard' });
        } else {
          this.error = 'Invalid credentials.'
        }
      } catch (err) {
        console.error(err)
        this.error = 'Login failed. Please check your credentials.'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
body {
  background-color: #f9fafb;
}
</style>
