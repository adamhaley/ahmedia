import type { Config } from "tailwindcss";

export default {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        'neon-blue': '#00ffff',
        'neon-pink': '#ff00ff',
        'neon-purple': '#bc13fe',
        'dark-bg': '#005e5e',
      },
      textShadow: {
        'neon': '0 0 10px #00ffff, 0 0 20px #00ffff, 0 0 40px #00ffff',
      },
      boxShadow: {
        'neon': '0 0 15px #00ffff, inset 0 0 15px #00ffff',
      },
    },
  },
  plugins: [],
} satisfies Config;
