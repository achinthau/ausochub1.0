const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/wireui/wireui/resources/**/*.blade.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/View/**/*.php'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
    safelist: [
        'border-blue-300',
        'border-green-300',
        'border-orange-300',
        'border-red-300',
        'border-stone-300',
        'hover:border-blue-500',
        'hover:border-green-500',
        'hover:border-orange-500',
        'hover:border-stone-500',
        'hover:border-red-500',
        'text-blue-600 ',
        'dark:text-blue-400',
        'text-red-600 ',
        'dark:text-red-400',
        'text-green-600 ',
        'dark:text-green-400',
        'bg-green-200',
        'bg-red-200',
    ]
};
