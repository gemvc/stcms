import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  root: 'assets',
  build: {
    outDir: '../public/assets/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'assets/js/app.jsx'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: 'css/[name].[ext]',
      },
    },
  },
});