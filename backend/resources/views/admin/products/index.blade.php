@extends('layouts.admin')

@section('title', 'Daftar Produk')
@section('page_title', 'Kelola Produk Toko')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h4 class="admin-card-title">Katalog Produk Toko Online</h4>
            <div style="display: flex; align-items: center; gap: 1rem; margin-left: auto;">
                <form action="" method="GET" style="display: inline-flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <span style="font-size: 0.85rem; color: var(--text-admin-secondary);">Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.85rem; background-color: var(--bg-admin-card); border-color: var(--border-color); color: var(--text-primary); border-radius: 4px; height: auto; cursor: pointer;">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <a href="{{ route('admin.products.create') }}" class="btn-admin btn-admin-primary btn-admin-sm" style="margin: 0;">+ Tambah Produk</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 80px;">Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Tautan Pembelian (Buy Link)</th>
                        <th style="width: 80px; text-align: center;">Dilihat</th>
                        <th style="width: 150px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-admin-secondary); font-size: 0.85rem;">
                                {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}
                            </td>
                            <td>
                                <div class="table-image" style="position: relative;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <span>📦</span>
                                    @endif
                                    
                                    @php
                                        $extraCount = 0;
                                        for ($j = 2; $j <= 5; $j++) {
                                            $colName = "image{$j}";
                                            if (!empty($product->$colName)) {
                                                $extraCount++;
                                            }
                                        }
                                    @endphp
                                    @if ($extraCount > 0)
                                        <span style="position: absolute; bottom: -2px; right: -2px; background: var(--accent-sky); color: #fff; font-size: 0.6rem; font-weight: 800; padding: 1px 4px; border-radius: 4px; line-height: 1;">+{{ $extraCount }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    {{ $product->name }}
                                </div>
                                <div style="margin-bottom: 0.35rem; display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                    @forelse($product->categories as $cat)
                                        <span class="badge-admin badge-blue" style="font-size: 0.65rem; padding: 2px 6px; font-weight: 600; border-radius: 4px; background: rgba(56, 189, 248, 0.1); color: var(--accent-sky); border: 1px solid rgba(56, 189, 248, 0.2);">{{ $cat->name }}</span>
                                    @empty
                                        <span class="badge-admin badge-gray" style="font-size: 0.65rem; padding: 2px 6px; font-weight: 600; border-radius: 4px; background: rgba(0,0,0,0.05); color: var(--text-muted); border: 1px solid var(--border-color);">Tanpa Kategori</span>
                                    @endforelse
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-admin-secondary); max-width: 300px;">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: var(--accent-amber); font-size: 0.95rem;">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                @if($product->tokopedia_link)
                                    <div style="margin-bottom: 4px;">
                                        <a href="{{ $product->tokopedia_link }}" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: #10B981; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #10B981;"></span>
                                            Tokopedia ↗
                                        </a>
                                    </div>
                                @endif
                                @if($product->shopee_link)
                                    <div style="margin-bottom: 4px;">
                                        <a href="{{ $product->shopee_link }}" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: #F97316; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #F97316;"></span>
                                            Shopee ↗
                                        </a>
                                    </div>
                                @endif
                                @if(!$product->tokopedia_link && !$product->shopee_link && $product->shop_link)
                                    <div>
                                        <a href="{{ $product->shop_link }}" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; color: var(--accent-blue);">
                                            {{ Str::limit($product->shop_link, 30) }} ↗
                                        </a>
                                    </div>
                                @endif
                                @if(!$product->tokopedia_link && !$product->shopee_link && !$product->shop_link)
                                    <span style="color: var(--text-admin-secondary); font-size: 0.75rem; font-style: italic;">Tidak ada tautan</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-admin badge-gray" style="font-size: 0.75rem;">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 2px; vertical-align: middle;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    {{ $product->views_count }}
                                </span>
                            </td>
                            <td class="actions-cell" style="justify-content: flex-end;">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem;">
                                Belum ada produk yang ditambahkan ke katalog.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages() || $products->total() > 0)
            <div class="admin-pagination-wrapper" style="margin-top: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                {{ $products->links('pagination::bootstrap-4') }}
                <div style="font-size: 0.8rem; color: var(--text-admin-secondary); font-weight: 500;">
                    Menampilkan {{ $products->firstItem() ?? 0 }} sampai {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} data
                </div>
            </div>
        @endif
    </div>
@endsection
