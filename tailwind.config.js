/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue', // make sure all Vue files are scanned
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
