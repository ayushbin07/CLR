import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'index.html'),
        assignment: resolve(__dirname, 'assignment.html'),
        habits: resolve(__dirname, 'habits.html'),
        mess: resolve(__dirname, 'mess.html'),
        settings: resolve(__dirname, 'settings.html')
      }
    }
  }
});
