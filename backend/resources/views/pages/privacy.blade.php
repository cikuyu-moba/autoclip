@extends('layouts.app')

@section('title', 'Kebijakan Privasi (Privacy Policy)')

@section('meta_tags')
    <meta name="description" content="Kebijakan Privasi situs Arhana (arhana.my.id). Ketahui bagaimana kami mengumpulkan, menggunakan, dan melindungi data serta informasi privasi Anda, termasuk kebijakan cookie dan Google AdSense.">
    <link rel="canonical" href="{{ route('privacy') }}" />
@endsection

@section('content')
<section class="section-container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 860px; margin: 0 auto;">
        
        <!-- Breadcrumb & Header -->
        <div style="margin-bottom: 2.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none;">Beranda</a>
                <span>/</span>
                <span style="color: var(--accent-sky); font-weight: 600;">Kebijakan Privasi</span>
            </div>
            
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.75rem; letter-spacing: -0.5px; line-height: 1.2;">
                Kebijakan Privasi <span style="color: var(--accent-sky);">(Privacy Policy)</span>
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                Terakhir diperbarui: <strong>{{ date('d F Y') }}</strong> &bull; Berlaku untuk domain <strong>arhana.my.id</strong>
            </p>
        </div>

        <!-- Main Document Body -->
        <div class="legal-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem 3rem; box-shadow: var(--shadow-sm); line-height: 1.8; color: var(--text-secondary);">
            
            <div style="background: rgba(2, 132, 199, 0.05); border-left: 4px solid var(--accent-sky); padding: 1.25rem 1.5rem; border-radius: 0 var(--radius-sm) var(--radius-sm) 0; margin-bottom: 2rem;">
                <p style="margin: 0; font-size: 0.95rem; color: var(--text-primary);">
                    Di <strong>Arhana</strong> (dapat diakses melalui <a href="{{ route('home') }}" style="color: var(--accent-sky); font-weight: 600;">https://arhana.my.id</a>), privasi pengunjung kami adalah salah satu prioritas utama kami. Dokumen Kebijakan Privasi ini menguraikan jenis informasi apa saja yang dikumpulkan dan dicatat oleh Arhana serta bagaimana kami menggunakannya.
                </p>
            </div>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                1. Informasi yang Kami Kumpulkan
            </h2>
            <p>
                Ketika Anda mengunjungi dan berinteraksi di situs kami, kami dapat mengumpulkan informasi dalam beberapa cara:
            </p>
            <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
                <li style="margin-bottom: 0.5rem;"><strong>Informasi Pribadi yang Diberikan Secara Sukarela:</strong> Meliputi nama lengkap, alamat email, atau pesan yang Anda kirimkan melalui formulir kontak kami, kolom komentar artikel, maupun pendaftaran akun belajar siswa.</li>
                <li style="margin-bottom: 0.5rem;"><strong>Informasi Penggunaan &amp; Perangkat:</strong> Data teknis seperti alamat Protokol Internet (IP address), jenis peramban (browser), penyedia layanan internet (ISP), rujukan halaman (referring/exit pages), cap waktu kunjungan, dan jumlah klik untuk analisis tren trafik.</li>
            </ul>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                2. Berkas Log (Log Files)
            </h2>
            <p>
                Arhana mengikuti prosedur standar dalam menggunakan berkas log. Berkas-berkas ini mencatat pengunjung saat mereka mengunjungi situs web. Informasi yang dikumpulkan oleh berkas log mencakup alamat IP, jenis peramban, ISP, tanggal dan stempel waktu, halaman rujukan/keluar, dan kemungkinan jumlah klik. Informasi ini tidak ditautkan ke informasi apa pun yang dapat diidentifikasi secara pribadi. Tujuannya murni untuk menganalisis tren, mengelola situs, melacak pergerakan pengguna di sekitar situs web, dan mengumpulkan informasi demografis secara agregat.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                3. Cookies dan Web Beacons
            </h2>
            <p>
                Seperti situs web modern lainnya, Arhana menggunakan 'cookies'. Cookies ini digunakan untuk menyimpan informasi termasuk preferensi pengunjung, dan halaman-halaman di situs web yang diakses atau dikunjungi pengguna. Informasi tersebut digunakan untuk mengoptimalkan pengalaman pengguna dengan menyesuaikan konten halaman web kami berdasarkan jenis peramban pengunjung dan/atau informasi lainnya.
            </p>
            <p>
                Anda dapat memilih untuk menonaktifkan cookies melalui opsi peramban web individu Anda. Informasi lebih rinci tentang manajemen cookie dengan peramban web tertentu dapat ditemukan di situs web masing-masing peramban (seperti Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari).
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                4. Kebijakan Google DoubleClick DART Cookie &amp; Google AdSense
            </h2>
            <p>
                Google adalah salah satu vendor pihak ketiga di situs kami. Google juga menggunakan cookies, yang dikenal sebagai <strong>DART cookie</strong>, untuk menayangkan iklan kepada pengunjung situs kami berdasarkan kunjungan mereka ke <em>arhana.my.id</em> dan situs-situs lain di internet.
            </p>
            <div style="background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.2); padding: 1.25rem; border-radius: var(--radius-sm); margin: 1.25rem 0;">
                <p style="margin: 0; font-size: 0.95rem; color: var(--text-primary);">
                    <strong>Pilihan Penolakan (Opt-out):</strong> Pengunjung dapat memilih untuk menolak penggunaan cookie DART dengan mengunjungi Kebijakan Privasi jaringan iklan dan konten Google di URL berikut:
                    <br>
                    <a href="https://policies.google.com/technologies/ads" target="_blank" rel="noopener noreferrer" style="color: var(--accent-sky); font-weight: 600; word-break: break-all;">https://policies.google.com/technologies/ads</a>
                    <br>
                    Atau mengelola preferensi personalisasi iklan melalui Google Ad Settings:
                    <br>
                    <a href="https://adssettings.google.com" target="_blank" rel="noopener noreferrer" style="color: var(--accent-sky); font-weight: 600; word-break: break-all;">https://adssettings.google.com</a>
                </p>
            </div>
            <p>
                Pihak ketiga server iklan atau jaringan iklan dapat menggunakan teknologi seperti cookies, JavaScript, atau Web Beacons yang digunakan dalam iklan masing-masing dan tautan yang muncul di Arhana, yang dikirim langsung ke peramban pengguna. Mereka secara otomatis menerima alamat IP Anda saat ini terjadi. Teknologi ini digunakan untuk mengukur efektivitas kampanye iklan mereka dan/atau untuk mempersonalisasi konten iklan yang Anda lihat di situs web yang Anda kunjungi.
            </p>
            <p style="font-style: italic;">
                Harap dicatat bahwa Arhana tidak memiliki akses atau kontrol terhadap cookies yang digunakan oleh pengiklan pihak ketiga tersebut.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                5. Kebijakan Privasi Pihak Ketiga
            </h2>
            <p>
                Kebijakan Privasi Arhana tidak berlaku untuk pengiklan atau situs web lain. Karena itu, kami menyarankan Anda untuk berkonsultasi dengan Kebijakan Privasi masing-masing dari server iklan pihak ketiga tersebut untuk informasi lebih rinci, termasuk instruksi mereka tentang cara memilih keluar dari opsi tertentu.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                6. Hak Perlindungan Data Pribadi (UU PDP, GDPR, &amp; CCPA)
            </h2>
            <p>
                Kami menghormati hak hukum Anda atas data pribadi Anda sesuai dengan peraturan perundang-undangan yang berlaku, termasuk Undang-Undang No. 27 Tahun 2022 tentang Perlindungan Data Pribadi (UU PDP) di Indonesia, GDPR, dan CCPA:
            </p>
            <ul style="padding-left: 1.5rem; margin-bottom: 1.5rem;">
                <li style="margin-bottom: 0.4rem;"><strong>Hak untuk Mengakses:</strong> Anda berhak meminta salinan data pribadi Anda yang kami simpan.</li>
                <li style="margin-bottom: 0.4rem;"><strong>Hak untuk Memperbaiki:</strong> Anda berhak meminta kami mengoreksi informasi yang Anda yakini tidak akurat atau melengkapi informasi yang belum lengkap.</li>
                <li style="margin-bottom: 0.4rem;"><strong>Hak untuk Menghapus (Right to Erasure):</strong> Anda berhak meminta kami menghapus data pribadi Anda dalam kondisi tertentu.</li>
                <li style="margin-bottom: 0.4rem;"><strong>Hak untuk Membatasi Pemrosesan:</strong> Anda berhak meminta kami membatasi pemrosesan data pribadi Anda.</li>
                <li style="margin-bottom: 0.4rem;"><strong>Hak Menolak Pemrosesan:</strong> Anda berhak untuk menolak pemrosesan data pribadi Anda oleh kami.</li>
            </ul>
            <p>
                Jika Anda mengajukan permintaan terkait hak-hak tersebut, kami akan merespons dalam jangka waktu paling lambat 30 hari. Silakan hubungi kami melalui formulir kontak atau email resmi kami.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                7. Perlindungan Privasi Anak (COPPA Compliance)
            </h2>
            <p>
                Bagian lain dari prioritas kami adalah menambahkan perlindungan bagi anak-anak saat menggunakan internet. Kami mendorong orang tua dan wali untuk mengamati, berpartisipasi, dan/atau memantau serta memandu aktivitas daring anak-anak mereka.
            </p>
            <p>
                Arhana tidak dengan sengaja mengumpulkan Informasi Identitas Pribadi apa pun dari anak-anak di bawah usia 13 tahun. Jika Anda merasa bahwa anak Anda memberikan informasi semacam ini di situs web kami, kami sangat menganjurkan Anda untuk segera menghubungi kami dan kami akan melakukan upaya terbaik kami untuk segera menghapus informasi tersebut dari catatan kami.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                8. Persetujuan
            </h2>
            <p>
                Dengan menggunakan situs web kami, Anda dengan ini menyetujui Kebijakan Privasi kami dan menyetujui syarat serta ketentuannya.
            </p>

            <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin: 2rem 0 1rem 0; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                9. Hubungi Kami
            </h2>
            <p>
                Jika Anda memiliki pertanyaan tambahan atau memerlukan informasi lebih lanjut tentang Kebijakan Privasi kami, jangan ragu untuk menghubungi kami melalui:
            </p>
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem; margin-top: 1rem;">
                <p style="margin: 0 0 0.5rem 0;"><strong>Website:</strong> Arhana (<a href="https://arhana.my.id" style="color: var(--accent-sky);">https://arhana.my.id</a>)</p>
                <p style="margin: 0 0 0.5rem 0;"><strong>Email:</strong> <a href="mailto:cikuyu.rc@gmail.com" style="color: var(--accent-sky); font-weight: 600;">cikuyu.rc@gmail.com</a></p>
                <p style="margin: 0;"><strong>Halaman Kontak:</strong> <a href="{{ route('contact') }}" style="color: var(--accent-sky); font-weight: 600;">Kirim Pesan Melalui Halaman Kontak Kami</a></p>
            </div>
        </div>

    </div>
</section>
@endsection
