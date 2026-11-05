<?php $__env->startSection('title', 'Tentang Kami (About Us)'); ?>

<?php $__env->startSection('meta_tags'); ?>
    <meta name="description" content="Tentang Arhana (arhana.my.id) - Portal teknologi, direktori software gratis terkurasi, portofolio rekayasa perangkat lunak, dan toko produk komputer. Kenali visi, misi, dan nilai kami.">
    <link rel="canonical" href="<?php echo e(route('about')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="section-container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 900px; margin: 0 auto;">
        
        <!-- Breadcrumb & Header -->
        <div style="margin-bottom: 2.5rem; text-align: center;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <a href="<?php echo e(route('home')); ?>" style="color: var(--text-secondary); text-decoration: none;">Beranda</a>
                <span>/</span>
                <span style="color: var(--accent-sky); font-weight: 600;">Tentang Kami</span>
            </div>
            
            <span class="section-subtitle">Mengenal Lebih Dekat</span>
            <h1 style="font-size: 2.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; letter-spacing: -0.5px; line-height: 1.2;">
                Tentang <span>Arhana</span>
            </h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 650px; margin: 0 auto; line-height: 1.6;">
                Ruang digital terpadu untuk eksplorasi teknologi, kurasi software bermanfaat, rekayasa web modern, dan edukasi komputer.
            </p>
        </div>

        <!-- Hero Feature Card -->
        <div style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.08) 0%, rgba(2, 132, 199, 0.02) 100%); border: 1px solid rgba(2, 132, 199, 0.15); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 3rem; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px;">
                <span style="background: var(--accent-sky); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 4px 12px; border-radius: 100px; letter-spacing: 0.5px; text-transform: uppercase;">Profil Singkat</span>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin: 0.75rem 0 1rem 0;">Membangun Teknologi yang Bermanfaat &amp; Mudah Diakses</h2>
                <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem; margin: 0;">
                    <strong>Arhana (arhana.my.id)</strong> berawal dari dedikasi untuk menghadirkan referensi teknologi yang bersih, cepat, dan terpercaya bagi masyarakat luas di Indonesia. Kami menggabungkan ulasan teknologi terkini, kurasi software freeware dan open source berkualitas, serta katalog portofolio sistem informasi yang siap pakai.
                </p>
            </div>
            <div style="text-align: center; flex-shrink: 0; margin: 0 auto;">
                <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: inline-flex; align-items: center; justify-content: center;">
                    <img src="<?php echo e(asset('logo/logo.png')); ?>" alt="Arhana Logo" style="height: 90px; width: auto; object-fit: contain; mix-blend-mode: multiply;">
                </div>
            </div>
        </div>

        <!-- Vision and Mission Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; margin-bottom: 3.5rem;">
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm);">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(2, 132, 199, 0.1); color: var(--accent-sky); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Visi Kami</h3>
                <p style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem; margin: 0;">
                    Menjadi ekosistem dan rujukan teknologi terpercaya di Indonesia yang menginspirasi, memberdayakan talenta digital, dan mempermudah pengguna menemukan perkakas komputasi terbaik yang aman serta efisien.
                </p>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm);">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">Misi Kami</h3>
                <ul style="color: var(--text-secondary); line-height: 1.7; font-size: 0.95rem; padding-left: 1.25rem; margin: 0;">
                    <li style="margin-bottom: 0.4rem;">Menyajikan artikel dan berita teknologi dengan bahasa yang lugas, faktual, dan mudah dipahami.</li>
                    <li style="margin-bottom: 0.4rem;">Mengurasi direktori software gratis dan open-source yang aman bebas malware.</li>
                    <li style="margin-bottom: 0.4rem;">Mengembangkan karya portofolio web dan sistem informasi yang inovatif dan berkinerja tinggi.</li>
                    <li>Menyediakan layanan konsultasi dan produk pendukung teknologi yang andal.</li>
                </ul>
            </div>
        </div>

        <!-- 4 Core Pillars -->
        <div style="margin-bottom: 3.5rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="section-subtitle">Layanan &amp; Konten</span>
                <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-primary);">Pilar Utama Website Kami</h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem;">📰</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Berita &amp; Wawasan</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Artikel analisis mengenai perkembangan dunia pemrograman, web development, sistem operasi, dan teknologi cerdas masa kini.
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem;">💾</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Direktori Software Gratis</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Koleksi freeware dan open source teruji yang langsung menautkan ke sumber pengembang resmi tanpa perantara yang mengganggu.
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem;">💻</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Portofolio Proyek</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Showcase aplikasi web dan sistem rekayasa perangkat lunak modern yang dibangun dengan arsitektur bersih dan performa tinggi.
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem;">🛍️</div>
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Toko &amp; Produk</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin: 0;">
                        Perangkat keras komputer, aksesoris pilihan, dan produk digital bergaransi yang mendukung produktivitas kerja harian Anda.
                    </p>
                </div>
            </div>
        </div>

        <!-- Editorial Standards and Ethics -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem; margin-bottom: 3.5rem; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                Standar Editorial &amp; Transparansi
            </h3>
            <p style="color: var(--text-secondary); line-height: 1.8; font-size: 0.95rem; margin-bottom: 1rem;">
                Integritas konten adalah kunci utama kepercayaan pembaca kami. Dalam setiap konten yang dipublikasikan di Arhana:
            </p>
            <ul style="color: var(--text-secondary); line-height: 1.8; font-size: 0.95rem; padding-left: 1.5rem; margin: 0 0 1.5rem 0;">
                <li style="margin-bottom: 0.5rem;"><strong>Keaslian dan Riset:</strong> Kami menghindari plagiarisme dan memprioritaskan pengalaman pengujian langsung (hands-on experience).</li>
                <li style="margin-bottom: 0.5rem;"><strong>Keamanan Tautan Software:</strong> Tautan unduhan diverifikasi untuk memastikan keamanan perangkat pengguna dari risiko malware atau phishing.</li>
                <li style="margin-bottom: 0.5rem;"><strong>Koreksi &amp; Pembaruan:</strong> Jika terdapat informasi yang usang atau keliru, tim kami segera melakukan revisi faktual secara transparan.</li>
                <li><strong>Keterbukaan Iklan:</strong> Konten bersponsor atau iklan jaringan (seperti Google AdSense) disajikan dengan penanda yang jelas sesuai pedoman periklanan digital.</li>
            </ul>
        </div>

        <!-- Call to Action Banner -->
        <div style="background: linear-gradient(135deg, var(--accent-sky) 0%, #0369a1 100%); border-radius: var(--radius-md); padding: 3rem 2rem; text-align: center; color: #ffffff; box-shadow: var(--shadow-md);">
            <h3 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; color: #ffffff;">Ingin Berkolaborasi atau Memiliki Pertanyaan?</h3>
            <p style="font-size: 1rem; opacity: 0.9; max-width: 600px; margin: 0 auto 1.75rem auto; line-height: 1.6;">
                Kami selalu terbuka untuk berdiskusi, menerima saran pengembangan, atau menjawab pertanyaan seputar teknologi dan produk kami.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo e(route('contact')); ?>" style="background: #ffffff; color: var(--accent-sky); padding: 0.75rem 1.75rem; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; transition: transform 0.2s;" onmouseenter="this.style.transform='translateY(-2px)'" onmouseleave="this.style.transform='translateY(0)'">
                    Hubungi Kami
                </a>
                <a href="<?php echo e(route('portfolio')); ?>" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 0.75rem 1.75rem; border-radius: var(--radius-sm); font-weight: 600; text-decoration: none; transition: background 0.2s;" onmouseenter="this.style.background='rgba(255, 255, 255, 0.25)'" onmouseleave="this.style.background='rgba(255, 255, 255, 0.15)'">
                    Lihat Portofolio
                </a>
            </div>
        </div>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/autoclip/backend/resources/views/pages/about.blade.php ENDPATH**/ ?>