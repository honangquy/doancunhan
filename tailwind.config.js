/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#1e40af', // Blue 700
          dark: '#1e3a8a',    // Blue 800
          light: '#3b82f6',   // Blue 500
        },
        accent: {
          DEFAULT: '#f97316', // Orange 500
          dark: '#ea580c',    // Orange 600
          light: '#fb923c',   // Orange 400
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      fontSize: {
        'xs': '12px',
        'sm': '14px',
        'base': '16px',
        'lg': '18px',
        'xl': '20px',
        '2xl': '24px',
        '3xl': '30px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
