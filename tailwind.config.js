const { blue, ...colors  } = require('@tailwindcss/ui/colors')


module.exports = {
  theme: {
    extend: {
      colors: {
        accent: {
          100: blue[100],
          200: blue[200],
          300: blue[300],
          400: blue[400],
          500: blue[500],
          600: blue[600],
          700: blue[700],
          800: blue[800],
          900: blue[900],
        },
        'success': '#A9E34B',
        'warning': '#FCC419',
        'error': '#e03131',
        'project-collection': '#f1c40f',
        'personal-collection': '#16a085',
      },
      height : {
        'page' : 'calc(100vh - 3rem)'
      }
    },
    customForms: theme => ({
      default: {
        'input, textarea, multiselect, select, checkbox': {
          borderColor: theme('colors.gray.500'),
        },
      },
    })
  },
  // variants: {
  //   opacity: ['responsive', 'hover', 'focus']
  // },
  plugins: [
    // require('@tailwindcss/custom-forms')
    require('@tailwindcss/ui'),
  ],
  corePlugins: {
    container: false
  }
}
