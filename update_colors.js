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
    
    // Replace colors
    content = content.replace(/text-red-/g, 'text-primary-');
    content = content.replace(/bg-red-/g, 'bg-primary-');
    content = content.replace(/border-red-/g, 'border-primary-');
    content = content.replace(/ring-red-/g, 'ring-primary-');
    content = content.replace(/from-red-/g, 'from-primary-');
    content = content.replace(/to-red-/g, 'to-primary-');
    
    content = content.replace(/text-orange-/g, 'text-secondary-');
    content = content.replace(/bg-orange-/g, 'bg-secondary-');
    content = content.replace(/from-orange-/g, 'from-secondary-');
    content = content.replace(/to-orange-/g, 'to-secondary-');
    
    // Replace hex / rgba values
    content = content.replace(/rgba\(239,\s*68,\s*68/g, 'rgba(63, 158, 143'); // red glow
    content = content.replace(/rgba\(239,68,68/g, 'rgba(63,158,143');
    content = content.replace(/rgba\(220,\s*38,\s*38/g, 'rgba(47, 131, 117'); // red darker glow
    content = content.replace(/rgba\(220,38,38/g, 'rgba(47,131,117');
    
    fs.writeFileSync(file, content);
    console.log(`Updated ${file}`);
});
