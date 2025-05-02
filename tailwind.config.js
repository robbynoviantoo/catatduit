/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './resources/**/*.{vue,js,ts,jsx,tsx}',
      './index.html'
    ],
    theme: {
      extend: {
        colors: {
          primary: '#553df2',
        },
      },
    },
    plugins: [],
  }
  