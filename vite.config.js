/* import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
 */         



import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5174,
        strictPort: true,
        cors: true,
        hmr: {
            host: '192.168.120.90',
            port: 5174,
        },
    },
}); 



/* import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
     server: {
        host: '0.0.0.0',
        port: 5173, // you can change if needed
        strictPort: tr  ue,
        hmr: {
            host: '192.168.1.108',
          },
      }, 
      
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ], 
   
}); */

