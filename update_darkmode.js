import fs from 'fs';
import path from 'path';

const dir = 'e:/cours laravel/ibda3-fresh/resources/views';

function walk(directory) {
    let results = [];
    const list = fs.readdirSync(directory);
    list.forEach(file => {
        file = path.join(directory, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else if (file.endsWith('.blade.php')) {
            results.push(file);
        }
    });
    return results;
}

const files = walk(dir);

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    
    // Avoid double replacing
    if (content.includes('dark:bg-[#111116]')) return;

    // Backgrounds
    content = content.replace(/bg-\[#111116\]/g, 'bg-gray-50 dark:bg-[#111116]');
    content = content.replace(/bg-\[#1a1a24\]/g, 'bg-white dark:bg-[#1a1a24]');
    content = content.replace(/bg-gray-800\/50/g, 'bg-gray-100 dark:bg-gray-800/50');
    content = content.replace(/bg-gray-900\/50/g, 'bg-gray-50 dark:bg-gray-900/50');
    content = content.replace(/bg-gray-800/g, 'bg-gray-100 dark:bg-gray-800');
    content = content.replace(/bg-gray-900/g, 'bg-gray-200 dark:bg-gray-900');
    
    // Texts
    content = content.replace(/text-gray-300/g, 'text-gray-700 dark:text-gray-300');
    content = content.replace(/text-gray-400/g, 'text-gray-600 dark:text-gray-400');
    content = content.replace(/text-white/g, 'text-gray-900 dark:text-white');
    content = content.replace(/text-gray-200/g, 'text-gray-800 dark:text-gray-200');
    
    // Borders
    content = content.replace(/border-gray-800/g, 'border-gray-200 dark:border-gray-800');
    content = content.replace(/border-gray-700/g, 'border-gray-300 dark:border-gray-700');
    
    // Hovers
    content = content.replace(/hover:bg-gray-800\/50/g, 'hover:bg-gray-200 dark:hover:bg-gray-800/50');
    content = content.replace(/hover:bg-gray-800/g, 'hover:bg-gray-200 dark:hover:bg-gray-800');
    content = content.replace(/hover:text-gray-200/g, 'hover:text-gray-900 dark:hover:text-gray-200');

    // Exceptions & Reverts (Things that MUST stay white/dark in both modes, like buttons text)
    content = content.replace(/text-gray-900 dark:text-white px-5/g, 'text-white px-5');
    content = content.replace(/text-gray-900 dark:text-white"\s+id="notificationBadge"/g, 'text-white" id="notificationBadge"');
    
    // Scrollbar fixes in app.blade.php
    content = content.replace(/::-webkit-scrollbar-track \{ background: #111116; \}/g, '::-webkit-scrollbar-track { background: #f9fafb; }\n        .dark ::-webkit-scrollbar-track { background: #111116; }');
    
    fs.writeFileSync(file, content);
    console.log(`Updated ${file}`);
});
