import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  root: 'assets/js',
  build: {
    outDir: '../../public/assets/js',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'assets/js/app.jsx'),
      },
      output: {
        entryFileNames: '[name].js',
      },
    },
  },
}); 