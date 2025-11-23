<template>
  <div id="app" class="h-screen bg-gray-100 flex flex-col overflow-hidden">
    <!-- Header & Sidebar for non-login pages -->
    <div v-if="user" class="flex flex-1 flex-row h-full overflow-hidden">
      <!-- Sidebar -->
       
      <Sidebar v-if="user" :user="user" class="w-64 bg-white shadow-md " :class="activatedSidebar?'':'hidden'" />

      <!-- Main content -->
      <div class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <Header @callParentFunction="activatedSidebarFunction()" v-if="user" :user="user" class="bg-white shadow px-4 py-3" />

        <!-- Page content -->
        <main class="flex-1 p-6 overflow-auto">
          <router-view />
        </main>
      </div>
    </div>

    <!-- Full-width content for login page -->
    <div v-else class="flex-1">
      <router-view />
    </div>
  </div>
</template>

<script>
import Header from './components/Header.vue';
import Sidebar from './components/Sidebar.vue';
import axios from 'axios';
export default {
  name: 'App',
  data() {
    return {
      user: null,
      activatedSidebar: true
    }
  },
  components: { Header, Sidebar },
  computed: {
    // isLoginPage() {
    //   return this.$route.path === '/vue/login';
    // }
  },
  mounted() {
    this.$root.fetchUser = this.fetchUser;
    this.fetchUser();
  },
  methods: {
    activatedSidebarFunction(){
      this.activatedSidebar = !this.activatedSidebar;
    },
    async fetchUser() {
      const token = sessionStorage.getItem('token');
      if (!token) {
        if (this.$route.name !== 'Login') {
          this.$router.push({ name: 'Login' });
          
        }
        return;
      };

      try {
        const res = await axios.get('/api/me', {
          headers: { Authorization: `Bearer ${token}` }
        });
        
        sessionStorage.setItem('currentUserRole', res.data.role.name);
        this.user = res.data;
        
        
      } catch (err) {
        console.error(err);
        sessionStorage.removeItem('token');
        this.user = null;
        this.$router.push({ name: 'Login' });
      }
    },
    setUser(user) {
      this.user = user;
    }
  }
};
</script>

<style>
/* Optional: for custom scrollbar if you want */
main::-webkit-scrollbar {
  width: 8px;
}
main::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 4px;
}
</style>
