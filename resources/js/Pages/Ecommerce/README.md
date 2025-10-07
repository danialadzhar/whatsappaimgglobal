# E-Commerce Module

## 📁 Struktur Folder
```
resources/js/Pages/Ecommerce/
├── Index.vue       # Halaman utama dengan senarai produk
├── Show.vue        # Halaman detail produk
└── README.md       # Dokumentasi

resources/js/Components/
└── ProductCard.vue # Komponen untuk paparan produk
```

## 🎨 Features

### Halaman Utama (Index.vue)
- **Hero Banner**: Banner promosi dengan gradient design
- **Filter & Search**: 
  - Filter by kategori (All, Earbuds, Headphones, Earphones)
  - Filter by harga (Under $100, $100-$500, dll)
  - Search bar untuk cari produk
  - Sort by (Featured, Price, Rating, Newest)
- **Product Grid**: Paparan produk dalam grid responsive
- **Responsive Design**: Fully responsive untuk mobile & desktop

### Halaman Detail (Show.vue)
- **Breadcrumb Navigation**: Navigasi untuk kembali ke senarai produk
- **Product Images**: Gambar produk besar dengan zoom effect
- **Product Info**: 
  - Nama & rating
  - Harga (dengan discount jika ada)
  - Deskripsi produk
  - Color selection
  - Quantity selector
- **Actions**: Add to Cart & Wishlist button

### Product Card Component
- **Wishlist Toggle**: Simpan produk kegemaran
- **Product Image**: Dengan hover effect
- **Rating Display**: Bintang rating visual
- **Price Display**: Dengan original price (jika ada)
- **Add to Cart**: Quick add button

## 🎯 Routes

```php
// E-commerce routes (authenticated)
Route::get('/ecommerce', [EcommerceController::class, 'index'])->name('ecommerce.index');
Route::get('/ecommerce/{id}', [EcommerceController::class, 'show'])->name('ecommerce.show');
```

## 🔧 Controller

**EcommerceController.php** menguruskan:
- `index()` - Paparkan senarai produk dengan filter
- `show($id)` - Paparkan detail produk

## 📱 Responsive Design

- **Mobile (< 640px)**: 1 column grid, stacked filters
- **Tablet (640px - 1024px)**: 2 column grid
- **Desktop (> 1024px)**: 3-4 column grid, side-by-side filters

## 🎨 Design Features

- **Minimalist & Clean UI**: Design yang simple dan elegan
- **Smooth Animations**: Hover effects & transitions
- **Color Scheme**: Neutral colors dengan blue accent
- **Typography**: Clear & readable fonts
- **Spacing**: Proper padding & margins untuk better UX

## 🚀 Usage

**Public Access (Tidak perlu login):**
1. Access `/ecommerce` dalam browser
2. Browse produk, guna filter/search jika perlu
3. Klik produk untuk lihat detail
4. Add to cart atau wishlist

**Kalau user dah login:**
- Navbar akan show profile dropdown
- Boleh access dashboard & pages lain
- Kalau belum login, show Login/Sign Up buttons

## 🔐 Access Control

E-commerce routes adalah **PUBLIC** - boleh access tanpa login.
Guna `EcommerceLayout` yang auto detect kalau user login atau tidak.

## 💡 Future Enhancements

- Shopping cart functionality
- Checkout process
- Payment integration
- Order history
- Product reviews & ratings
- Product comparison
- Related products suggestion

