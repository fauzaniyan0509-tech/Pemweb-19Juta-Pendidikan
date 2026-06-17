<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hubungi Kami – 19JutaPendidikan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="landingpage.css">
  <style>
    :root { --biru: #148fcd; --teal: #35c7b6; --gelap: #0f2942; }
    body { background: #eef5f9; font-family: 'Poppins', sans-serif; }
    .navbar { background: #fff !important; box-shadow: 0 2px 16px rgba(20,143,205,.08); }
    .logo-text { color: var(--biru) !important; font-weight: 800; font-size: 1.4rem; text-decoration: none; }

    /* HERO */
    .hero-contact {
      background: linear-gradient(135deg, #0f2942 0%, #148fcd 55%, #35c7b6 100%);
      padding: 80px 0 100px; text-align: center; position: relative; overflow: hidden;
    }
    .hero-contact::after {
      content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 70px;
      background: #eef5f9; clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-badge {
      display: inline-block; background: rgba(255,255,255,.15); color: white;
      border: 1px solid rgba(255,255,255,.3); border-radius: 999px;
      font-size: 12px; font-weight: 600; padding: 5px 16px; margin-bottom: 16px;
    }
    .hero-contact h1 { font-size: 2.4rem; font-weight: 900; color: white; margin-bottom: 12px; }
    .hero-contact p  { font-size: 15px; color: rgba(255,255,255,.85); max-width: 500px; margin: 0 auto; line-height: 1.8; }

    /* LABEL */
    .label-biru {
      display: inline-block; background: #e0f2fe; color: var(--biru);
      font-size: 12px; font-weight: 700; padding: 4px 14px; border-radius: 999px; margin-bottom: 10px;
    }
    .s-judul { font-size: 1.6rem; font-weight: 800; color: var(--gelap); margin-bottom: 8px; }
    .s-sub   { color: #64748b; font-size: 14.5px; line-height: 1.8; }

    /* INFO CARDS */
    .info-card {
      background: white; border-radius: 20px; padding: 28px 24px; text-align: center;
      box-shadow: 0 4px 20px rgba(20,143,205,.08); height: 100%;
      transition: transform .2s;
    }
    .info-card:hover { transform: translateY(-4px); }
    .info-icon {
      width: 60px; height: 60px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem; margin: 0 auto 16px;
    }
    .info-card h6 { font-weight: 800; color: var(--gelap); font-size: 15px; margin-bottom: 6px; }
    .info-card p  { font-size: 13.5px; color: #64748b; line-height: 1.7; margin: 0; }

    /* FORM */
    .form-card {
      background: white; border-radius: 24px;
      box-shadow: 0 8px 36px rgba(20,143,205,.10); padding: 36px 40px;
    }
    @media(max-width: 576px) { .form-card { padding: 24px 20px; } }
    .form-label { font-size: 13px; font-weight: 700; color: var(--gelap); margin-bottom: 6px; }
    .form-control, .form-select {
      border: 1.5px solid #e2e8f0; border-radius: 14px;
      padding: 12px 16px; font-size: 14px; font-family: 'Poppins', sans-serif;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--biru); box-shadow: 0 0 0 3px rgba(20,143,205,.1); outline: none;
    }
    textarea.form-control { resize: vertical; min-height: 130px; }
    .btn-kirim {
      background: linear-gradient(90deg, var(--biru), var(--teal));
      color: white; border: none; border-radius: 999px;
      font-weight: 700; font-size: 15px; padding: 14px 40px;
      width: 100%; cursor: pointer; transition: opacity .2s;
    }
    .btn-kirim:hover { opacity: .88; }
    .alert-sukses {
      background: #d1fae5; color: #065f46; border-radius: 14px;
      padding: 16px 20px; font-size: 14px; font-weight: 600;
      display: none; margin-bottom: 20px;
    }

    /* MAP PLACEHOLDER */
    .map-box {
      background: linear-gradient(135deg, var(--gelap), #1a4a7a);
      border-radius: 20px; height: 280px; display: flex; flex-direction: column;
      align-items: center; justify-content: center; color: white; text-align: center;
      padding: 20px;
    }
    .map-box .emo { font-size: 3rem; margin-bottom: 12px; }
    .map-box h6   { font-weight: 800; margin-bottom: 6px; }
    .map-box p    { font-size: 13px; opacity: .8; margin: 0; }

    footer { background: var(--gelap); color: white; padding: 24px 0; text-align: center; }
    footer p { margin: 0; font-size: 13px; opacity: .6; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg py-3 sticky-top">
  <div class="container">
    <a class="navbar-brand logo-text" href="index.php">19JutaPendidikan</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center gap-lg-3">
        <li class="nav-item mt-2 mt-lg-0">
          <a href="contactUs.php" class="btn px-4 py-2" style="background:linear-gradient(90deg,#148fcd,#35c7b6); color:white; border:none; border-radius:999px; font-weight:600; font-size:14px;">
            Hubungi Kami
          </a>
        </li>
        <li class="nav-item mt-2 mt-lg-0">
          <a href="halamanLoginAdmin.php" class="btn px-4 py-2" style="background:#0f2942; color:white; border:none; border-radius:999px; font-weight:600; font-size:14px;">
            Admin Login
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero-contact">
  <div class="container">
    <span class="hero-badge">💬 Kontak</span>
    <h1>Ada pertanyaan?<br>Kami siap membantu!</h1>
    <p>Sampaikan pertanyaan, saran, atau laporkan masalah. Tim kami akan merespons secepatnya.</p>
  </div>
</section>

<!-- INFO CARDS -->
<section class="py-5">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="info-card">
          <div class="info-icon" style="background:#dbeafe;">📧</div>
          <h6>Email</h6>
          <p>info@19jutapendidikan.id<br><span style="font-size:12px; color:#94a3b8;">Respon dalam 1×24 jam</span></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-card">
          <div class="info-icon" style="background:#d1fae5;">📱</div>
          <h6>WhatsApp</h6>
          <p>+62 812-3456-7890<br><span style="font-size:12px; color:#94a3b8;">Senin–Jumat, 08.00–17.00</span></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-card">
          <div class="info-icon" style="background:#fef3c7;">📍</div>
          <h6>Lokasi Tim</h6>
          <p>Universitas Sebelas Maret<br><span style="font-size:12px; color:#94a3b8;">Surakarta, Jawa Tengah</span></p>
        </div>
      </div>
    </div>

    <!-- FORM + MAP -->
    <div class="row g-4 align-items-start">

      <!-- FORM -->
      <div class="col-lg-7">
        <div class="form-card">
          <span class="label-biru">✉️ Kirim Pesan</span>
          <h2 class="s-judul">Hubungi kami langsung</h2>
          <p class="s-sub mb-4">Isi formulir di bawah ini dan kami akan menghubungimu kembali sesegera mungkin.</p>

          <div id="alertSukses" class="alert-sukses">
            ✅ Pesan kamu berhasil dikirim! Kami akan merespons dalam 1×24 jam.
          </div>

          <form id="formKontak" onsubmit="kirimPesan(event)">
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" class="form-control" placeholder="Nama kamu" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" placeholder="email@kamu.com" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Topik</label>
              <select class="form-select">
                <option value="">-- Pilih topik --</option>
                <option>Pertanyaan umum</option>
                <option>Laporan bug / error</option>
                <option>Pengajuan tempat edukatif</option>
                <option>Saran & masukan</option>
                <option>Kerjasama / kolaborasi</option>
                <option>Lainnya</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="form-label">Pesan *</label>
              <textarea class="form-control" placeholder="Tulis pesanmu di sini..." required></textarea>
            </div>
            <button type="submit" class="btn-kirim">Kirim Pesan →</button>
          </form>
        </div>
      </div>

      <!-- KANAN -->
      <div class="col-lg-5 d-flex flex-column gap-4">

        <div class="map-box">
          <div class="emo">🗺️</div>
          <h6>Universitas Sebelas Maret</h6>
          <p>Jl. Ir. Sutami No.36A, Kentingan<br>Surakarta, Jawa Tengah 57126</p>
          <a href="https://maps.google.com/?q=Universitas+Sebelas+Maret+Surakarta" target="_blank"
             style="margin-top:14px; background:rgba(255,255,255,.2); color:white; border-radius:999px; font-size:12.5px; font-weight:700; padding:8px 18px; text-decoration:none; display:inline-block;">
            Buka di Google Maps →
          </a>
        </div>

        <!-- SOSIAL MEDIA -->
        <div style="background:white; border-radius:20px; padding:24px; box-shadow:0 4px 20px rgba(20,143,205,.08);">
          <h6 style="font-weight:800; color:var(--gelap); margin-bottom:16px;">🌐 Ikuti Kami</h6>
          <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="#" style="display:flex; align-items:center; gap:12px; text-decoration:none; padding:10px 14px; border-radius:12px; border:1.5px solid #e2e8f0; transition:border-color .2s;"
               onmouseover="this.style.borderColor='#148fcd'" onmouseout="this.style.borderColor='#e2e8f0'">
              <span style="font-size:1.3rem;">📷</span>
              <div><div style="font-size:13px; font-weight:700; color:#0f2942;">Instagram</div><div style="font-size:11.5px; color:#94a3b8;">@19jutapendidikan</div></div>
            </a>
            <a href="#" style="display:flex; align-items:center; gap:12px; text-decoration:none; padding:10px 14px; border-radius:12px; border:1.5px solid #e2e8f0; transition:border-color .2s;"
               onmouseover="this.style.borderColor='#148fcd'" onmouseout="this.style.borderColor='#e2e8f0'">
              <span style="font-size:1.3rem;">▶️</span>
              <div><div style="font-size:13px; font-weight:700; color:#0f2942;">YouTube</div><div style="font-size:11.5px; color:#94a3b8;">@19jutapendidikan</div></div>
            </a>
            <a href="#" style="display:flex; align-items:center; gap:12px; text-decoration:none; padding:10px 14px; border-radius:12px; border:1.5px solid #e2e8f0; transition:border-color .2s;"
               onmouseover="this.style.borderColor='#148fcd'" onmouseout="this.style.borderColor='#e2e8f0'">
              <span style="font-size:1.3rem;">✖️</span>
              <div><div style="font-size:13px; font-weight:700; color:#0f2942;">X (Twitter)</div><div style="font-size:11.5px; color:#94a3b8;">@19jutapendidikan</div></div>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- FAQ SINGKAT -->
<section class="py-5" style="background:white;">
  <div class="container" style="max-width:720px;">
    <div class="text-center mb-4">
      <span class="label-biru">❓ FAQ Kontak</span>
      <h2 class="s-judul">Pertanyaan umum</h2>
    </div>
    <div style="display:flex; flex-direction:column; gap:10px;">

      <div style="background:#eef5f9; border-radius:16px; padding:18px 22px;">
        <div style="font-weight:700; font-size:14px; color:#0f2942; margin-bottom:6px;">Berapa lama waktu respons pesan?</div>
        <div style="font-size:13.5px; color:#64748b; line-height:1.75;">Kami berusaha merespons semua pesan dalam 1×24 jam di hari kerja (Senin–Jumat). Untuk urusan mendesak, hubungi via WhatsApp.</div>
      </div>

      <div style="background:#eef5f9; border-radius:16px; padding:18px 22px;">
        <div style="font-weight:700; font-size:14px; color:#0f2942; margin-bottom:6px;">Bagaimana cara melaporkan tempat yang tidak sesuai?</div>
        <div style="font-size:13.5px; color:#64748b; line-height:1.75;">Pilih topik "Laporan bug / error" pada formulir di atas, lalu sertakan nama tempat dan penjelasan masalah yang kamu temukan. Admin kami akan segera menindaklanjuti.</div>
      </div>

      <div style="background:#eef5f9; border-radius:16px; padding:18px 22px;">
        <div style="font-weight:700; font-size:14px; color:#0f2942; margin-bottom:6px;">Apakah 19JutaPendidikan menerima kerjasama?</div>
        <div style="font-size:13.5px; color:#64748b; line-height:1.75;">Ya! Kami terbuka untuk kerjasama dengan sekolah, kampus, komunitas pendidikan, dan lembaga beasiswa. Pilih topik "Kerjasama / kolaborasi" pada formulir untuk memulai diskusi.</div>
      </div>

    </div>
  </div>
</section>

<footer>
  <p>© 2025 19JutaPendidikan — Kelompok 3 · Dibuat dengan ❤️ untuk pendidikan Indonesia.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function kirimPesan(e) {
    e.preventDefault();
    const form  = document.getElementById('formKontak');
    const alert = document.getElementById('alertSukses');
    alert.style.display = 'block';
    form.reset();
    form.style.opacity = '.5';
    form.style.pointerEvents = 'none';
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => {
      alert.style.display = 'none';
      form.style.opacity  = '1';
      form.style.pointerEvents = 'auto';
    }, 5000);
  }
</script>
</body>
</html>