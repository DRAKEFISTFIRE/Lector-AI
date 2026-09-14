/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        canvas: '#10131C',
        surface: '#171B2A',
        elevated: '#1E2333',
        border: '#2A3044',
        ink: '#ECEAE3',
        muted: '#8790A6',
        amber: {
          DEFAULT: '#F2A93B',
          soft: '#F7C577',
        },
        teal: {
          DEFAULT: '#5FD9C6',
          soft: '#9BEAE0',
        },
      },
      fontFamily: {
        display: ['"Fraunces"', 'serif'],
        sans: ['"Manrope"', 'sans-serif'],
      },
      borderRadius: {
        sheet: '18px',
      },
    },
  },
  plugins: [],
}