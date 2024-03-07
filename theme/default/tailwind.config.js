/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.latte"],
  theme: {
    extend: {
      typography: (theme) => ({
        DEFAULT: {
          css: {
            a: {
              '&:hover': {
                color: theme('colors.blue.600'),
              },
            },
          },
        },
      }),
    },
  },
  plugins: [
    require('@tailwindcss/typography')
  ],
}