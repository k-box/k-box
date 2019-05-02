module.exports = {
  theme: {
    // we are not currently ready to 
    // use some of the Tailwind core
    // plugins
    colors: false,
    textColor: false,
    backgroundColor: false,
    borderColor: false,
    borderStyle: false,
    borderWidth: false,
    container: false,
    textTransform: false,

    extend: {
      zIndex : {
        "-1": "-1"
      }
    }
  },
  variants: {
    zIndex: [],
    tableLayout: [],
    opacity: [],
  },
  plugins: []
}
