const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')


module.exports = {
  purge: {
    content: [
      './resources/**/*.php',
      './resources/**/*.js',
      './packages/**/*.php',
      './packages/**/*.js',
      './workbench/**/*.php',
      './workbench/**/*.js',
    ],
    safelist: [
        /item--selectable/,
        /item--selected/,
        /description/,
        /c-panel/,
        /c-cache/,
        /select2/,
        /dialog/,
        /dropzone/,
        /dz-drag/,
        /preview/,
        /leaflet/,
        /map/,
    ]
  },
  theme: {
    extend: {
      colors: {
        accent: {
          50: colors.blue[50],
          100: colors.blue[100],
          200: colors.blue[200],
          300: colors.blue[300],
          400: colors.blue[400],
          500: colors.blue[500],
          600: colors.blue[600],
          700: colors.blue[700],
          800: colors.blue[800],
          900: colors.blue[900],
        },
        orange: colors.orange,
        'success': '#A9E34B',
        'warning': '#FCC419',
        'error': '#e03131',
        'project-collection': '#f1c40f',
        'personal-collection': '#16a085',
      },
      height : {
        'header' : defaultTheme.spacing[12],
        'page' : `calc(100vh - ${defaultTheme.spacing[12]})`,
      },
    },
  },
  variants: {
    
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
  corePlugins: {
    container: false
  }
}
