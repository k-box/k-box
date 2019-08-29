module.exports = {
  theme: {
    extend: {}
  },
  variants: {
    opacity: ['responsive', 'hover', 'focus']
  },
  plugins: [
    require('@tailwindcss/custom-forms')
  ],
  corePlugins: {
    container: false
  }
}
