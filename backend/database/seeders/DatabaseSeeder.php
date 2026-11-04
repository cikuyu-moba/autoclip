<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::create([
            'name' => 'Admin Arhana',
            'email' => 'admin@arhana.com',
            'password' => Hash::make('admin123'),
        ]);

        // 2. Seed Dynamic Menus
        $home = Menu::create([
            'title' => 'HOME',
            'url' => '/',
            'order' => 1,
            'is_external' => false,
        ]);

        $news = Menu::create([
            'title' => 'NEWS',
            'url' => '/news',
            'order' => 2,
            'is_external' => false,
        ]);

        $shop = Menu::create([
            'title' => 'SHOP',
            'url' => '/shop',
            'order' => 3,
            'is_external' => false,
        ]);

        $portfolio = Menu::create([
            'title' => 'PORTFOLIO',
            'url' => '/portfolio',
            'order' => 4,
            'is_external' => false,
        ]);

        $project = Menu::create([
            'title' => 'PROJECT',
            'url' => 'https://arhana.com/another-project',
            'order' => 5,
            'is_external' => true,
        ]);

        // Submenus under PROJECT
        Menu::create([
            'parent_id' => $project->id,
            'title' => 'MaiBook AI',
            'url' => 'https://maibook.example.com',
            'order' => 1,
            'is_external' => true,
        ]);

        Menu::create([
            'parent_id' => $project->id,
            'title' => 'SmartDMS Portal',
            'url' => 'https://smartdms.example.com',
            'order' => 2,
            'is_external' => true,
        ]);

        // 3. Seed Categories
        $tech = Category::create(['name' => 'Tech & Coding', 'slug' => 'tech-coding']);
        $personal = Category::create(['name' => 'Personal Development', 'slug' => 'personal-development']);
        $creative = Category::create(['name' => 'Business & Creative', 'slug' => 'business-creative']);

        // 4. Seed News/Posts
        Post::create([
            'category_id' => $tech->id,
            'title' => 'Membangun Arsitektur Web Minimalis Modern',
            'slug' => 'membangun-arsitektur-web-minimalis-modern',
            'excerpt' => 'Menjelajahi cara merancang aplikasi web yang cepat, berkinerja tinggi, dan elegan menggunakan pendekatan minimalis tanpa beban library berlebih.',
            'content' => 'Di dunia pengembangan web modern, sering kali kita terjebak dalam pusaran library dan framework yang membuat aplikasi menjadi lambat dan berat. Artikel ini mengupas bagaimana kembali ke dasar dengan Vanilla CSS, struktur HTML5 semantik, dan optimalisasi server-side menggunakan Laravel untuk menciptakan pengalaman pengguna yang memukau, responsif, dan hemat bandwidth. Kita akan belajar merancang layout grid yang dinamis dan menyusun variabel CSS untuk dark mode yang ramah mata.',
            'image' => null,
        ]);

        Post::create([
            'category_id' => $personal->id,
            'title' => 'Seni Produktivitas Tanpa Kelelahan (Burnout)',
            'slug' => 'seni-produktivitas-tanpa-kelelahan-burnout',
            'excerpt' => 'Mengapa bekerja lebih lama tidak selalu menghasilkan karya lebih baik, dan bagaimana mengelola energi kognitif Anda untuk bekerja dengan tenang.',
            'content' => 'Banyak dari kita mengukur produktivitas dari durasi duduk di depan layar. Namun, kelelahan mental seringkali menghambat kreativitas dan pemecahan masalah yang efektif. Melalui artikel ini, kita membahas teknik manajemen energi kognitif, pentingnya istirahat mikro terstruktur (seperti teknik Pomodoro yang disesuaikan), dan pentingnya mendesain antarmuka kerja dengan warna gelap/teduh (zinc/slate) yang aman untuk kesehatan mata jangka panjang.',
            'image' => null,
        ]);

        Post::create([
            'category_id' => $creative->id,
            'title' => 'Menjual Produk Digital di Era AI',
            'slug' => 'menjual-produk-digital-di-era-ai',
            'excerpt' => 'Bagaimana memanfaatkan keahlian unik Anda untuk meluncurkan produk digital bernilai tinggi dengan pemasaran toko online yang terintegrasi.',
            'content' => 'Era kecerdasan buatan membuka peluang tak terbatas bagi kreator digital untuk memaketkan pengetahuan mereka. Dari e-book panduan praktis, template desain, hingga kustomisasi solusi berbasis web. Artikel ini mengulas strategi menentukan harga yang tepat, menyusun deskripsi produk yang persuasif, serta memanfaatkan tautan e-commerce seperti Shopee dan Tokopedia untuk transaksi yang aman dan tepercaya tanpa perlu pusing memikirkan payment gateway sendiri.',
            'image' => null,
        ]);

        // 5. Seed Products
        Product::create([
            'name' => 'Ebook: Panduan Laravel Modern',
            'description' => 'Buku digital setebal 180 halaman yang membahas implementasi Laravel 11/13, pola arsitektur bersih, dan integrasi API modern.',
            'price' => 149000.00,
            'image' => null,
            'shop_link' => 'https://shopee.co.id/',
        ]);

        Product::create([
            'name' => 'Premium UI Dashboard Template',
            'description' => 'Template dashboard admin responsif bermaterialkan Vanilla CSS murni dengan skema warna zinc yang elegan, clean, dan profesional.',
            'price' => 299000.00,
            'image' => null,
            'shop_link' => 'https://tokopedia.com/',
        ]);

        Product::create([
            'name' => 'Jasa Kustomisasi Website Personal',
            'description' => 'Layanan kustomisasi penuh untuk website portofolio Anda, integrasi CMS, optimasi SEO, serta deploy ke server hosting.',
            'price' => 1500000.00,
            'image' => null,
            'shop_link' => 'https://shopee.co.id/',
        ]);

        // 6. Seed Projects (Portfolio)
        Project::create([
            'title' => 'MaiBook AI Ebook Platform',
            'description' => 'Platform pembuatan ebook bertenaga AI dengan fitur editor manual yang canggih, penulisan naratif berbasis konteks AI Gemini, manajemen metadata buku, dan ekspor format PDF kustom dengan DomPDF.',
            'technologies' => 'Laravel, React, Vanilla CSS, SQLite, Gemini AI, DomPDF',
            'image' => null,
            'project_link' => 'https://maibook.example.com',
        ]);

        Project::create([
            'title' => 'SmartDMS Portal',
            'description' => 'Sistem manajemen dokumen cerdas dengan fitur kategorisasi dokumen dinamis, pengunggahan aman, manajemen peran admin, dan visualisasi statistik dokumen melalui antarmuka premium React.',
            'technologies' => 'Laravel, React, TailwindCSS, PostgreSQL, Chart.js',
            'image' => null,
            'project_link' => 'https://smartdms.example.com',
        ]);

        Project::create([
            'title' => 'HiTV Plus Streaming Source Optimizer',
            'description' => 'Optimasi pemutar streaming Android TV untuk menangani base64 decryption, bypass anti-scraping portal eksternal, dan penyesuaian WebView teroptimal untuk kelancaran pemutaran video.',
            'technologies' => 'Kotlin, Android SDK, Android TV, ExpressJS, Cheerio',
            'image' => null,
            'project_link' => 'https://hitv.example.com',
        ]);
    }
}
