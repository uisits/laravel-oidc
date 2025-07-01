import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default {
    plugins:[
        tailwindcss(),
    ],
    build: {
        assetsDir: '',
        rollupOptions: {
            input: ['resources/css/app.css'],
            output: {
                assetFileNames: 'shibboleth-oidc.css',
            },
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm.js',
        },
    },
};