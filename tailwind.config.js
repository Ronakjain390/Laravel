module.exports = {
    content: [
      './resources/**/*.blade.php',
      "./resources/**/*.js",
      "./resources/**/*.vue",
      "./node_modules/flowbite/**/*.js",
      "./node_modules/daisyui/**/*.js",
      './src/**/*.html',
      './src/**/*.vue',
      './src/**/*.jsx',
    ],
    theme: {
      extend: {
        colors:{
          grays: "#F8F6F3",
          orange: "#F0AC49 !important",
          text_sidebar: "#747272 !important",
        }
      },
    },
    plugins: [
        require('flowbite/plugin'),
        require('daisyui'),
    ],
  }
