// اسکریپت تولید آیکون‌های PWA
// نصب: npm install sharp
// اجرا: node generate-icons.js

import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// سایزهای مورد نیاز
const sizes = [72, 96, 128, 144, 152, 192, 384, 512];

// ایجاد پوشه icons اگر وجود ندارد
const iconsDir = path.join(__dirname, 'public', 'icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

// خواندن لوگوی اصلی
const logoPath = path.join(iconsDir, 'logo-original.png');
if (!fs.existsSync(logoPath)) {
    console.error('❌ فایل logo-original.png یافت نشد!');
    console.log('لطفاً لوگو را از آدرس زیر دانلود و در public/icons/ قرار دهید:');
    console.log('https://azmoonkade.com/storage/branding/01K8BVENAG3PBKHM0SDPYDZAHG.png');
    process.exit(1);
}

// تولید آیکون‌ها
async function generateIcons() {
    console.log('🎨 شروع تولید آیکون‌ها از لوگوی اصلی...\n');

    // رنگ بک‌گراند: سفید کلاسیک (#ffffff)
    const bgColor = { r: 255, g: 255, b: 255, alpha: 1 };
    
    for (const size of sizes) {
        try {
            await sharp(logoPath)
                .resize(size, size, {
                    fit: 'contain',
                    background: bgColor
                })
                .png()
                .toFile(path.join(iconsDir, `icon-${size}x${size}.png`));
            
            console.log(`✅ آیکون ${size}x${size} ساخته شد`);
        } catch (error) {
            console.error(`❌ خطا در ساخت ${size}x${size}:`, error.message);
        }
    }

    // ساخت maskable icon با padding و بک‌گراند برند
    try {
        await sharp(logoPath)
            .resize(410, 410, {
                fit: 'contain',
                background: bgColor
            })
            .extend({
                top: 51,
                bottom: 51,
                left: 51,
                right: 51,
                background: bgColor
            })
            .png()
            .toFile(path.join(iconsDir, 'maskable-icon-512x512.png'));
        
        console.log('✅ Maskable icon 512x512 ساخته شد');
    } catch (error) {
        console.error('❌ خطا در ساخت maskable icon:', error.message);
    }

    // ساخت آیکون‌های shortcut با لوگوی اصلی
    try {
        // آیکون شروع آزمون (لوگو با بک‌گراند سبز)
        await sharp({
            create: {
                width: 96,
                height: 96,
                channels: 4,
                background: { r: 16, g: 185, b: 129, alpha: 1 }
            }
        })
            .composite([{
                input: await sharp(logoPath)
                    .resize(70, 70, { fit: 'contain', background: { r: 16, g: 185, b: 129, alpha: 0 } })
                    .toBuffer(),
                gravity: 'center'
            }])
            .png()
            .toFile(path.join(iconsDir, 'shortcut-exam.png'));
        
        console.log('✅ Shortcut icon (آزمون) ساخته شد');

        // آیکون پروفایل (لوگو با بک‌گراند آبی)
        await sharp({
            create: {
                width: 96,
                height: 96,
                channels: 4,
                background: { r: 99, g: 102, b: 241, alpha: 1 }
            }
        })
            .composite([{
                input: await sharp(logoPath)
                    .resize(70, 70, { fit: 'contain', background: { r: 99, g: 102, b: 241, alpha: 0 } })
                    .toBuffer(),
                gravity: 'center'
            }])
            .png()
            .toFile(path.join(iconsDir, 'shortcut-profile.png'));
        
        console.log('✅ Shortcut icon (پروفایل) ساخته شد');
    } catch (error) {
        console.error('❌ خطا در ساخت shortcut icons:', error.message);
    }

    console.log('\n✨ تمام آیکون‌ها با موفقیت ساخته شدند!');
}

generateIcons().catch(console.error);
