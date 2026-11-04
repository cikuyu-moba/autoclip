<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Question;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create HTML Dasar Course
        $course = Course::updateOrCreate(
            ['slug' => 'html-dasar'],
            [
                'title' => 'HTML Dasar',
                'description' => 'Pelajari dasar-dasar HTML untuk membangun struktur website. Materi ini dirancang untuk pemula yang ingin memahami tag, elemen, dan struktur dokumen HTML.',
                'materials' => '
                    <p>Selamat datang di Kursus Kilat HTML Dasar! HTML (Hyper Text Markup Language) adalah bahasa standar yang digunakan untuk membuat struktur halaman web.</p>
                    
                    <h3>1. Struktur Dasar Dokumen HTML</h3>
                    <p>Setiap dokumen HTML harus memiliki struktur dasar sebagai berikut:</p>
                    <pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Judul Halaman Anda&lt;/title&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Halo Dunia!&lt;/h1&gt;
    &lt;p&gt;Ini adalah paragraf pertama saya.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>

                    <h3>2. Elemen dan Tag HTML Penting</h3>
                    <ul>
                        <li><strong>&lt;h1&gt; sampai &lt;h6&gt;</strong>: Digunakan untuk membuat judul (heading). <code>&lt;h1&gt;</code> adalah yang terbesar dan <code>&lt;h6&gt;</code> adalah yang terkecil.</li>
                        <li><strong>&lt;p&gt;</strong>: Digunakan untuk membungkus teks menjadi paragraf.</li>
                        <li><strong>&lt;a&gt;</strong>: Digunakan untuk membuat tautan (hyperlink). Atribut wajibnya adalah <code>href</code>. Contoh: <code>&lt;a href="https://arhana.com"&gt;Kunjungi Arhana&lt;/a&gt;</code>.</li>
                        <li><strong>&lt;img&gt;</strong>: Digunakan untuk menampilkan gambar. Tag ini tidak memiliki penutup dan membutuhkan atribut <code>src</code> (sumber gambar) dan <code>alt</code> (teks alternatif). Contoh: <code>&lt;img src="gambar.jpg" alt="Pemandangan indah"&gt;</code>.</li>
                    </ul>

                    <h3>3. Membuat Daftar (List)</h3>
                    <p>Ada dua tipe daftar yang paling sering digunakan:</p>
                    <ol>
                        <li><strong>Unordered List (&lt;ul&gt;)</strong>: Daftar tanpa nomor urut (menggunakan bullet point).</li>
                        <li><strong>Ordered List (&lt;ol&gt;)</strong>: Daftar berurutan dengan nomor (1, 2, 3, dst).</li>
                    </ol>
                    <p>Di dalam kedua tag tersebut, setiap baris item dideskripsikan menggunakan tag <strong>&lt;li&gt;</strong> (List Item).</p>
                '
            ]
        );

        // 2. Create Pre Test Questions
        Question::where('course_id', $course->id)->where('type', 'pre_test')->delete();
        
        Question::create([
            'course_id' => $course->id,
            'type' => 'pre_test',
            'question_text' => 'Apa kepanjangan dari HTML?',
            'options' => [
                'Hyper Text Markup Language',
                'High Text Markup Language',
                'Hyperlinks and Text Markup Language',
                'Home Tool Markup Language'
            ],
            'correct_option' => 0
        ]);

        Question::create([
            'course_id' => $course->id,
            'type' => 'pre_test',
            'question_text' => 'Tag mana yang digunakan untuk membuat judul utama paling besar?',
            'options' => [
                '<head>',
                '<h6>',
                '<h1>',
                '<heading>'
            ],
            'correct_option' => 2
        ]);

        Question::create([
            'course_id' => $course->id,
            'type' => 'pre_test',
            'question_text' => 'Tag mana yang digunakan untuk membuat tautan/link?',
            'options' => [
                '<link>',
                '<a>',
                '<href>',
                '<url>'
            ],
            'correct_option' => 1
        ]);

        // 3. Create Post Test Questions
        Question::where('course_id', $course->id)->where('type', 'post_test')->delete();

        Question::create([
            'course_id' => $course->id,
            'type' => 'post_test',
            'question_text' => 'Atribut mana yang digunakan untuk menentukan URL sumber gambar pada tag <img>?',
            'options' => [
                'href',
                'link',
                'src',
                'alt'
            ],
            'correct_option' => 2
        ]);

        Question::create([
            'course_id' => $course->id,
            'type' => 'post_test',
            'question_text' => 'Bagaimana cara membuat daftar berurutan (ordered list) di HTML?',
            'options' => [
                '<ul>',
                '<list>',
                '<ol>',
                '<dl>'
            ],
            'correct_option' => 2
        ]);

        Question::create([
            'course_id' => $course->id,
            'type' => 'post_test',
            'question_text' => 'Di dalam bagian mana tag <title> dan tag <meta> biasanya diletakkan?',
            'options' => [
                '<body>',
                '<head>',
                '<footer>',
                '<header>'
            ],
            'correct_option' => 1
        ]);

        // 4. Create Public Dynamic Menu & Submenu for Course
        $courseMenu = Menu::updateOrCreate(
            ['title' => 'COURSE'],
            [
                'url' => '#',
                'order' => 5,
                'is_external' => false
            ]
        );

        Menu::updateOrCreate(
            ['parent_id' => $courseMenu->id, 'title' => 'HTML Dasar'],
            [
                'url' => '/courses/html-dasar',
                'order' => 1,
                'is_external' => false
            ]
        );
    }
}
