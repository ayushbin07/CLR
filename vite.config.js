import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'scripts.js'),
        styles: resolve(__dirname, 'styles.css')
      }
    }
  }
});
