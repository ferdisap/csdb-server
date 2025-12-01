import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  darkMode: 'class',   // HARUS class
  content: ['./index.html', './src/**/*.{vue,js,ts}', './resources/**/*.{vue,js,ts}'],
  plugins: [
    tailwindcss(),
    // …
  ],
})