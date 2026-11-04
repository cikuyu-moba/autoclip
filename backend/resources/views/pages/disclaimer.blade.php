@extends('layouts.app')

@section('title', 'Disclaimer (Penafian)')

@section('meta_tags')
    <meta name="description" content="Disclaimer dan Penafian resmi situs Arhana (arhana.my.id). Informasi mengenai batas tanggung jawab, akurasi konten, tautan pihak ketiga, dan direktori download software.">
    <link rel="canonical" href="{{ route('disclaimer') }}" />
@endsection

@section('content')
<section class="section-container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 860px; margin: 0 auto;">
        
        <!-- Breadcrumb & Header -->
        <div style="margin-bottom: 2.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none;">Beranda</a>
                <span>/</span>
                <span style="color: var(--accent-sky); font-weight: 600;">Disclaimer</span>
            </div>
            
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.75rem; letter-spacing: -0.5px; line-height: 1.2;">
                Disclaimer <span style="color: var(--accent-sky);">(Penafian)</span>
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                Terakhir diperbarui: <strong>{{ date('d F Y') }}</strong> &bull; Berlaku untuk domain <strong>arhana.my.id</strong>
            </p>
        </div>

        <!-- Main Document Body -->
        <div class="legal-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem 3rem; box-shadow: var(--shadow-sm); line-height: 1.8; color: var(--text-secondary);">
            
            <div style="background: rgba(2, 132, 199, 0.05); border-left: 4px solid var(--accent-sky); padding: 1.25rem 1.5rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; margin-bottom: 2rem;">
                <p style="margin: 0; font-size: 0.95rem; color: var(--text-primary);">
                    Jika Anda memerlukan informasi lebih lanjut atau memiliki pertanyaan tentang disclaimer situs kami, jangan ragu untuk menghubungi kami melalui formulir kontak atau email di <a href="mailto:cikuyu.rc@gmail.com" style="color: var(--accent-sky); font-weight: 600;">cikuyu.rc@gmail.com</a>.
                </p>
            </div>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                1. Penafian Umum Konten &amp; Edukasi
            </h2>
            <p>
                Semua informasi di situs web <strong>Arhana</strong> (<a href="{{ route('home') }}" style="color: var(--accent-sky);">https://arhana.my.id</a>) diterbitkan dengan itikad baik (good faith) dan hanya untuk tujuan pemberian informasi umum serta edukasi teknologi.
            </p>
            <p>
                Arhana tidak memberikan jaminan apa pun mengenai kelengkapan, keandalan, dan keakuratan informasi ini. Segala tindakan yang Anda ambil atas informasi yang Anda temukan di situs web ini sepenuhnya merupakan risiko Anda sendiri. Arhana tidak bertanggung jawab atas kerugian dan/atau kerusakan apa pun sehubungan dengan penggunaan situs web kami.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                2. Direktori Unduhan Software Gratis (Freeware &amp; Open Source)
            </h2>
            <p>
                Situs Arhana menyajikan direktori katalog dan ulasan perangkat lunak (software) gratis, freeware, dan open source. Dalam penyajian direktori ini:
            </p>
            <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
                <li style="margin-bottom: 0.5rem;">Kami berusaha semaksimal mungkin untuk memverifikasi keaslian tautan resmi pengembang (official vendor) dan memastikan software yang direkomendasikan bebas dari malware atau adware berbahaya.</li>
                <li style="margin-bottom: 0.5rem;">Meskipun demikian, Arhana tidak memiliki kendali langsung atas perubahan berkas installer, server pengembang pihak ketiga, atau pembaruan kode software yang dilakukan oleh pemilik aslinya setelah artikel dipublikasikan.</li>
                <li style="margin-bottom: 0.5rem;">Pengguna disarankan untuk selalu memastikan kecocokan sistem operasi, membaca lisensi perangkat lunak, dan memindai berkas unduhan dengan perangkat lunak antivirus yang terpercaya di perangkat masing-masing.</li>
            </ul>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                3. Tautan Eksternal (Outbound Links)
            </h2>
            <p>
                Dari situs web kami, Anda dapat mengunjungi situs web lain dengan mengikuti hyperlink ke situs eksternal tersebut. Meskipun kami berusaha keras untuk hanya menyediakan tautan berkualitas ke situs web yang bermanfaat dan etis, kami tidak memiliki kendali atas konten dan sifat situs tersebut.
            </p>
            <p>
                Tautan ke situs web lain tidak menyiratkan rekomendasi untuk semua konten yang ditemukan di situs tersebut. Pemilik situs dan konten dapat berubah tanpa pemberitahuan sebelumnya dan dapat terjadi sebelum kami memiliki kesempatan untuk menghapus tautan yang mungkin telah rusak atau tidak relevan lagi.
            </p>
            <p>
                Perlu diperhatikan juga bahwa ketika Anda meninggalkan situs web kami, situs lain mungkin memiliki kebijakan privasi dan ketentuan penggunaan yang berbeda yang berada di luar kendali kami. Harap pastikan untuk memeriksa Kebijakan Privasi situs-situs tersebut serta "Ketentuan Layanan" mereka sebelum terlibat dalam bisnis apa pun atau mengunggah informasi apa pun.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                4. Hak Cipta, Merek Dagang &amp; DMCA
            </h2>
            <p>
                Semua logo, merek dagang, nama produk, tangkapan layar, dan merek terdaftar yang ditampilkan atau disebutkan dalam situs ini merupakan hak milik dari masing-masing pemilik resminya. Penyebutan tersebut murni bertujuan untuk identifikasi, edukasi, ulasan, dan referensi yang adil (fair use).
            </p>
            <p>
                Jika Anda adalah pemilik hak cipta yang sah dari suatu materi atau tautan yang tercantum di situs ini dan menginginkan materi tersebut dihapus atau dikoreksi, silakan hubungi kami dengan menyertakan bukti kepemilikan yang valid melalui email <a href="mailto:cikuyu.rc@gmail.com" style="color: var(--accent-sky); font-weight: 600;">cikuyu.rc@gmail.com</a>. Kami akan merespons dan menindaklanjutinya dalam waktu 1x24 jam kerja.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                5. Iklan dan Sponsor (Google AdSense)
            </h2>
            <p>
                Situs web ini dapat menampilkan iklan yang dikelola oleh pihak ketiga, seperti Google AdSense. Keberadaan iklan di situs kami tidak secara otomatis mencerminkan rekomendasi langsung atau endorsement pribadi dari Arhana terhadap produk atau layanan yang diiklankan. Pembaca diharapkan melakukan penilaian mandiri sebelum melakukan transaksi dengan pihak pengiklan.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                6. Persetujuan
            </h2>
            <p>
                Dengan menggunakan situs web kami, Anda dengan ini menyetujui penafian kami dan menyetujui seluruh ketentuan yang tercantum di dalamnya.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                7. Pembaruan
            </h2>
            <p>
                Jika kami memperbarui, mengubah, atau membuat perubahan apa pun pada dokumen ini, perubahan tersebut akan dipublikasikan secara jelas di halaman ini.
            </p>

            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem; margin-top: 1.5rem;">
                <p style="margin: 0 0 0.5rem 0;"><strong>Kontak Resmi:</strong> Arhana Computer</p>
                <p style="margin: 0 0 0.5rem 0;"><strong>Email:</strong> <a href="mailto:cikuyu.rc@gmail.com" style="color: var(--accent-sky); font-weight: 600;">cikuyu.rc@gmail.com</a></p>
                <p style="margin: 0;"><strong>Halaman Kontak:</strong> <a href="{{ route('contact') }}" style="color: var(--accent-sky); font-weight: 600;">Hubungi Kami Disini</a></p>
            </div>
        </div>

    </div>
</section>
@endsection
