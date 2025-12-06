import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      vue$: 'vue/dist/vue.esm.js',
      '@components': path.resolve(__dirname, 'resources/js/components'),
      '@pages': path.resolve(__dirname, 'resources/js/pages'),
    },
  },
});
