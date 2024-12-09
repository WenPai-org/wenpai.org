import { defineConfig } from 'vite';
import path from 'path'; // 引入path模块

export default defineConfig({
    build: {
        outDir: path.resolve(__dirname, '../dist'), // 设置输出目录为上一级的dist目录
        lib: {
        entry: path.resolve(__dirname, 'src/variant.js'),
        name: 'WPCSVariant', // 全局变量名
        fileName: (format) => `wpcs-variant.${format}.js`
        },
        // 确保外部化处理某些你不希望打包进库的依赖
        rollupOptions: {
            external: [],
            output: {
                // 提供全局变量名
                globals: {}
            }
        },
    },
    server: {
        // 在此处配置你的开发服务器
        port: 3000, // 选择启动服务器的端口
        // 服务0.0.0.0
        host: '0.0.0.0',
    },
});