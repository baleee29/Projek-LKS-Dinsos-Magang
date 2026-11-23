// Pengujian black box lewat antarmuka peramban pada server lokal dengan data contoh fiktif.
// Status tiap skenario ditentukan dari hasil yang teramati, bukan dari anggapan.
const { execFileSync } = require('child_process');
const fs = require('fs');
const os = require('os');
const path = require('path');
const puppeteer = require('puppeteer');

const DASAR = 'http://127.0.0.1:8123';
const FOLDER = path.join(__dirname, '..', 'tangkapan', 'black-box');
const SEMENTARA = fs.mkdtempSync(path.join(os.tmpdir(), 'uji-bb-'));
const MYSQL = 'C:\\xampp\\mysql\\bin\\mysql.exe';
const AKUN = { admin: ['admin@sidaya.example', 'SidayaAdmin2026'], lks: ['lks-001@sidaya.example', 'SidayaLks2026'] };

fs.mkdirSync(FOLDER, { recursive: true });
const PDF_CONTOH = path.join(SEMENTARA, 'contoh.pdf');
const SALAH = path.join(SEMENTARA, 'program.exe');
fs.writeFileSync(PDF_CONTOH, '%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n');
fs.writeFileSync(SALAH, 'MZ bukan dokumen');

const sql = (kueri) => execFileSync(MYSQL, ['-u', 'root', '-N', '-B', 'sidaya_laporan', '-e', kueri], { encoding: 'utf8' }).replace(/\r/g, '').trim();
const jeda = (ms) => new Promise((r) => { setTimeout(r, ms); });
const hasil = [];
let browser;

async function halamanBaru() {
  const konteks = await browser.createBrowserContext();
  const page = await konteks.newPage();
  await page.setViewport({ width: 1280, height: 860 });
  page.on('dialog', (d) => d.accept());
  return page;
}

const buka = (page, url) => page.goto(DASAR + url, { waitUntil: 'networkidle2', timeout: 60000 });

async function masukCepat(peran) {
  const page = await halamanBaru();
  await buka(page, '/login');
  const t = await page.$(`form[action*="login/demo/${peran}"] button`);
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle2' }), t.click()]);
  return page;
}

async function masukForm(page, email, sandi) {
  for (let i = 0; i < 2; i += 1) {
    await buka(page, '/login');
    await page.evaluate((e, s) => {
      document.querySelector('#email').value = e;
      document.querySelector('#password').value = s;
      document.querySelector('#email').form.noValidate = true;
    }, email, sandi);
    const [resp] = await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 30000 }),
      page.evaluate(() => document.querySelector('#email').form.requestSubmit()),
    ]);
    if (resp && resp.status() !== 429) return resp;
    await jeda(62000);
  }
  return null;
}

const jalur = (page) => new URL(page.url()).pathname;

// Klik lewat halaman, karena klik bawaan bisa menggantung pada tab yang tidak aktif.
const klik = (page, pemilih) => page.evaluate((p) => {
  const el = document.querySelector(p);
  if (!el) throw new Error(`Elemen tidak ditemukan: ${p}`);
  el.click();
}, pemilih);

async function setNilai(page, pemilih, nilai) {
  await page.evaluate((p, n) => {
    const el = document.querySelector(p);
    if (!el) throw new Error(`Isian tidak ditemukan: ${p}`);
    el.value = n;
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }, pemilih, nilai);
}

async function klikTeks(page, teks, cakupan = 'body') {
  const ok = await page.evaluate((t, c) => {
    const akar = document.querySelector(c) || document.body;
    const b = [...akar.querySelectorAll('button,a')].find((el) => el.offsetParent !== null && el.textContent.trim().replace(/\s+/g, ' ').includes(t));
    if (!b) return false;
    b.click();
    return true;
  }, teks, cakupan);
  if (!ok) throw new Error(`Tombol tidak ditemukan: ${teks}`);
}

// Menjalankan aksi lalu menunggu kiriman ke server. Bila tidak ada kiriman, hasilnya null.
async function kirim(page, aksi, batas = 6000) {
  const tunggu = page.waitForResponse((r) => r.request().method() !== 'GET' && r.url().startsWith(DASAR), { timeout: batas }).catch(() => null);
  await aksi();
  const r = await tunggu;
  await jeda(500);
  return r ? r.status() : null;
}

// Menyetujui dialog konfirmasi buatan aplikasi.
const setujui = (page) => page.evaluate(() => {
  const d = [...document.querySelectorAll('dialog[open]')].pop();
  if (!d) throw new Error('Dialog konfirmasi tidak terbuka');
  const t = [...d.querySelectorAll('button')].filter((b) => !/batal|tutup|kembali/i.test(b.textContent));
  t[t.length - 1].click();
});

const pesan = (page) => page.evaluate(() => {
  const bagian = [...document.querySelectorAll('.field-error,[data-file-error],[data-review-error],[role="alert"],.error,.form-error,#app-toast,[class*="error"]')]
    .filter((e) => e.offsetParent !== null || e.id === 'app-toast')
    .map((e) => e.textContent.trim().replace(/\s+/g, ' '))
    .filter((t) => t.length > 2 && t.length < 200);
  return [...new Set(bagian)].slice(0, 2).join(' | ');
});

async function uji(kode, fitur, skenario, masukan, diharapkan, fungsi, pageLayar) {
  try {
    const { lolos, nyata, layar } = await fungsi();
    let gambar = '';
    const halaman = layar || pageLayar;
    if (halaman) {
      gambar = `${kode}.png`;
      await halaman.bringToFront();
      await jeda(1200);
      await halaman.screenshot({ path: path.join(FOLDER, gambar) });
    }
    hasil.push({ kode, fitur, skenario, masukan, diharapkan, nyata, status: lolos ? 'Sesuai' : 'Tidak sesuai', gambar });
    console.log(`${lolos ? 'OK ' : 'XX '} ${kode} ${skenario} => ${nyata}`);
  } catch (galat) {
    hasil.push({ kode, fitur, skenario, masukan, diharapkan, nyata: `Skrip berhenti: ${galat.message}`, status: 'Belum teruji', gambar: '' });
    console.log(`?? ${kode} ${skenario} => ${galat.message}`);
  }
}

async function main() {
  browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: 'shell',
    protocolTimeout: 45000,
    args: ['--disable-renderer-backgrounding', '--disable-background-timer-throttling', '--disable-backgrounding-occluded-windows'],
  });
  const tamu = await halamanBaru();
  const stamp = Date.now().toString().slice(-6);

  // Masuk dan keluar
  const pMasuk = await halamanBaru();
  await uji('M-01', 'Masuk', 'Admin masuk dengan email dan kata sandi benar', 'Email dan sandi akun Admin contoh', 'Dasbor Admin terbuka', async () => {
    await masukForm(pMasuk, ...AKUN.admin);
    return { lolos: jalur(pMasuk) === '/admin/overview', nyata: `Membuka ${jalur(pMasuk)}`, layar: pMasuk };
  });
  await uji('M-02', 'Keluar', 'Admin keluar dari aplikasi', 'Tombol keluar', 'Sesi berakhir dan dasbor tidak bisa dibuka lagi', async () => {
    await pMasuk.evaluate(() => document.querySelector('form.logout-actions').requestSubmit());
    await jeda(1500);
    await buka(pMasuk, '/admin/overview');
    return { lolos: jalur(pMasuk) === '/login', nyata: `Membuka dasbor mengarah ke ${jalur(pMasuk)}`, layar: pMasuk };
  });
  const pLks = await halamanBaru();
  await uji('M-03', 'Masuk', 'Pengurus LKS masuk dengan username', 'Username lks_001_demo dan sandi benar', 'Dasbor LKS terbuka', async () => {
    await masukForm(pLks, 'lks_001_demo', AKUN.lks[1]);
    return { lolos: jalur(pLks) === '/lks/overview', nyata: `Membuka ${jalur(pLks)}`, layar: pLks };
  });
  const pSalah = await halamanBaru();
  await uji('M-04', 'Masuk', 'Masuk dengan kata sandi salah', 'Email benar, sandi salah', 'Akses ditolak dan pesan kesalahan tampil', async () => {
    await masukForm(pSalah, AKUN.admin[0], 'SandiKeliru123');
    await pSalah.addStyleTag({ content: '.demo-accounts { display: none !important; }' });
    const teks = await pesan(pSalah);
    return { lolos: jalur(pSalah) === '/login' && teks.length > 0, nyata: `Tetap di ${jalur(pSalah)}. Pesan: ${teks}`, layar: pSalah };
  });
  await uji('M-05', 'Masuk', 'Masuk dengan formulir kosong', 'Email dan sandi kosong', 'Akses ditolak dan pesan kesalahan tampil', async () => {
    await masukForm(pSalah, '', '');
    await pSalah.addStyleTag({ content: '.demo-accounts { display: none !important; }' });
    const teks = await pesan(pSalah);
    return { lolos: jalur(pSalah) === '/login' && teks.length > 0, nyata: `Tetap di ${jalur(pSalah)}. Pesan: ${teks}`, layar: pSalah };
  });

  // Batas akses
  await uji('A-01', 'Hak akses', 'Pengunjung membuka dasbor Admin', 'Alamat /admin/overview tanpa masuk', 'Diarahkan ke halaman masuk', async () => {
    await buka(tamu, '/admin/overview');
    return { lolos: jalur(tamu) === '/login', nyata: `Diarahkan ke ${jalur(tamu)}`, layar: tamu };
  });
  const lks = await masukCepat('lks');
  const admin = await masukCepat('admin');
  await uji('A-02', 'Hak akses', 'Pengurus LKS membuka dasbor Admin', 'Alamat /admin/overview', 'Akses ditolak dengan kode 403', async () => {
    const r = await buka(lks, '/admin/overview');
    return { lolos: r.status() === 403, nyata: `Kode ${r.status()}`, layar: lks };
  });
  await uji('A-03', 'Hak akses', 'Admin membuka dasbor LKS', 'Alamat /lks/overview', 'Akses ditolak dengan kode 403', async () => {
    const r = await buka(admin, '/lks/overview');
    return { lolos: r.status() === 403, nyata: `Kode ${r.status()}`, layar: admin };
  });
  const lembagaSaya = sql("select lembaga_id from pengguna where email='lks-001@sidaya.example'");
  await uji('A-04', 'Hak akses', 'Pengurus LKS membuka klien lembaga lain', 'Alamat detail klien milik lembaga lain', 'Data tidak dapat dibuka', async () => {
    const id = sql(`select id from klien_lks where lembaga_id<>'${lembagaSaya}' limit 1`);
    const r = await buka(lks, `/lks/resident/${id}`);
    return { lolos: r.status() >= 400, nyata: `Kode ${r.status()}`, layar: lks };
  });
  await uji('A-05', 'Hak akses', 'Admin membuka laporan yang masih draf', 'Alamat detail laporan berstatus Draf', 'Data tidak dapat dibuka', async () => {
    const id = sql("select id from laporan_semester where status='Draf' limit 1");
    const r = await buka(admin, `/admin/report/${id}`);
    return { lolos: r.status() >= 400, nyata: `Kode ${r.status()}`, layar: admin };
  });

  await uji('A-06', 'Hak akses', 'Pengurus LKS membuka halaman ganti sandi', 'Alamat /account/password', 'Halaman tidak dapat dibuka', async () => {
    const r = await buka(lks, '/account/password');
    return { lolos: r.status() >= 400, nyata: `Kode ${r.status()}`, layar: lks };
  });

  // Pendaftaran LKS
  const jumlahDaftar = () => Number(sql('select count(*) from pendaftaran_lks'));
  const namaDaftar = `LKS Uji Kotak Hitam ${stamp}`;
  await uji('D-01', 'Pendaftaran LKS', 'Mengirim formulir kosong', 'Semua isian kosong', 'Formulir menampilkan kesalahan dan data tidak tersimpan', async () => {
    await buka(tamu, '/pilar/pendaftaran-lks.html');
    const awal = jumlahDaftar();
    await kirim(tamu, () => tamu.evaluate(() => document.getElementById('btn-review-submit').click()), 2000);
    const teks = await pesan(tamu);
    return { lolos: jumlahDaftar() === awal && teks.length > 0, nyata: `Data tetap ${awal}. Pesan: ${teks}`, layar: tamu };
  });
  await uji('D-02', 'Pendaftaran LKS', 'Mengunggah berkas berjenis salah', 'Berkas program.exe pada berkas 1', 'Berkas ditolak dan pesan kesalahan tampil', async () => {
    await buka(tamu, '/pilar/pendaftaran-lks.html');
    const input = await tamu.$('input[name="lks-surat_permohonan-input"]');
    await input.uploadFile(SALAH);
    await jeda(500);
    const teks = await tamu.evaluate(() => {
      const w = document.querySelector('input[name="lks-surat_permohonan-input"]').closest('[data-file-field],.file-field,div');
      const e = [...document.querySelectorAll('[data-file-error]')].find((x) => x.textContent.trim());
      return e ? e.textContent.trim() : (w ? '' : '');
    });
    return { lolos: teks.length > 0, nyata: `Pesan: ${teks || 'tidak ada'}`, layar: tamu };
  });
  await uji('D-03', 'Pendaftaran LKS', 'Mengirim data lengkap dan 16 berkas', 'Data contoh, wilayah, dan 16 berkas PDF', 'Pendaftaran tersimpan dengan status Menunggu Verifikasi', async () => {
    await buka(tamu, '/pilar/pendaftaran-lks.html');
    await klik(tamu, '#btn-fill-demo');
    await jeda(800);
    await setNilai(tamu, '#lks-nama', namaDaftar);
    for (const nama of await tamu.$$eval('input[type=file][name^="lks-"]', (e) => e.map((x) => x.name))) {
      const input = await tamu.$(`input[name="${nama}"]`);
      await input.uploadFile(PDF_CONTOH);
    }
    await jeda(800);
    await tamu.evaluate(() => document.getElementById('btn-review-submit').click());
    await jeda(800);
    await tamu.evaluate(() => document.getElementById('btn-confirm').click());
    await jeda(500);
    const kode = await kirim(tamu, () => tamu.evaluate(() => document.getElementById('btn-proceed').click()), 20000);
    const status = sql(`select status from pendaftaran_lks where nama_lembaga='${namaDaftar}'`);
    return { lolos: status === 'Menunggu Verifikasi', nyata: `Respons ${kode}, status ${status || 'tidak tersimpan'}`, layar: tamu };
  });

  await uji('D-04', 'Pendaftaran LKS', 'Membaca pesan hasil setelah pendaftaran terkirim', 'Halaman hasil dari D-03', 'Judul halaman menyatakan pendaftaran sudah terkirim', async () => {
    const judul = await tamu.$eval('#success-title', (e) => e.textContent.trim());
    return { lolos: !/belum dikirim|mode demo/i.test(judul), nyata: `Judul tertulis "${judul}"`, layar: tamu };
  });

  // Peninjauan pendaftaran
  const antrean = sql("select id from pendaftaran_lks where status='Menunggu Verifikasi' order by dibuat_pada").split('\n');
  const idTolak = antrean[0];
  const idTerima = sql(`select id from pendaftaran_lks where nama_lembaga='${namaDaftar}'`) || antrean[1];
  const tinjauTolak = async (alasan) => {
    await buka(admin, `/admin/registration/${idTolak}`);
    await setNilai(admin, '#registration-review-note', alasan);
    await klik(admin, '[data-action="reject"]');
    await jeda(400);
    const adaDialog = await admin.$('dialog[open]');
    const kode = adaDialog ? await kirim(admin, () => setujui(admin)) : null;
    return { kode, teks: await pesan(admin), status: sql(`select status from pendaftaran_lks where id='${idTolak}'`) };
  };
  await uji('T-01', 'Peninjauan', 'Menolak tanpa alasan', 'Alasan kosong', 'Penolakan ditolak dan status tetap', async () => {
    const r = await tinjauTolak('');
    return { lolos: r.status === 'Menunggu Verifikasi', nyata: `Status ${r.status}. Pesan: ${r.teks}`, layar: admin };
  });
  await uji('T-02', 'Peninjauan', 'Menolak dengan alasan terlalu pendek', 'Alasan "kurang"', 'Penolakan ditolak dan status tetap', async () => {
    const r = await tinjauTolak('kurang');
    return { lolos: r.status === 'Menunggu Verifikasi', nyata: `Status ${r.status}. Pesan: ${r.teks}`, layar: admin };
  });
  await uji('T-03', 'Peninjauan', 'Menolak dengan alasan lengkap', 'Alasan "Berkas akta pendirian belum lengkap"', 'Status berubah menjadi Ditolak', async () => {
    const r = await tinjauTolak('Berkas akta pendirian belum lengkap');
    return { lolos: r.status === 'Ditolak', nyata: `Status ${r.status}`, layar: admin };
  });
  await uji('T-04', 'Peninjauan', 'Menerima pendaftaran', 'Tombol Terima Pendaftaran', 'Status berubah dan data lembaga terbentuk', async () => {
    await buka(admin, `/admin/registration/${idTerima}`);
    await klik(admin, '[data-action="approve"]');
    await jeda(400);
    await kirim(admin, () => setujui(admin));
    const [status, lembaga] = sql(`select status, lembaga_id from pendaftaran_lks where id='${idTerima}'`).split('\t');
    return { lolos: status === 'Disetujui' && lembaga && lembaga !== 'NULL', nyata: `Status ${status}, lembaga ${lembaga && lembaga !== 'NULL' ? 'terbentuk' : 'belum ada'}`, layar: admin };
  });

  // Akun LKS
  const usernameBaru = `uji_${stamp}`;
  let sandiBaru = '';
  await uji('K-01', 'Akun LKS', 'Membuat akun dengan username yang sudah dipakai', 'Username admin', 'Akun ditolak dan pesan kesalahan tampil', async () => {
    await buka(admin, '/admin/partner-accounts');
    await setNilai(admin, '#acc-whatsapp', '080000009991');
    await setNilai(admin, '#acc-name', `LKS Username Ganda ${stamp}`);
    await setNilai(admin, '#acc-username', 'admin');
    await setNilai(admin, '#acc-password', 'SandiUji#2026xx');
    const kode = await kirim(admin, async () => {
      await admin.evaluate(() => document.getElementById('btn-submit-account').click());
      await jeda(400);
      await setujui(admin);
    });
    const teks = await pesan(admin);
    return { lolos: kode === 422 || (kode === null && teks.length > 0), nyata: `Respons ${kode}. Pesan: ${teks}`, layar: admin };
  });
  await uji('K-02', 'Akun LKS', 'Membuat akun dari pendaftaran yang diterima', 'Pendaftaran diterima, username baru, sandi acak', 'Akun tersimpan pada lembaga yang sama', async () => {
    await buka(admin, '/admin/partner-accounts');
    await admin.evaluate((id) => {
      const s = document.getElementById('acc-reg-select');
      s.value = id;
      s.dispatchEvent(new Event('change', { bubbles: true }));
    }, idTerima);
    await jeda(300);
    await setNilai(admin, '#acc-whatsapp', '080000009992');
    await setNilai(admin, '#acc-username', usernameBaru);
    await klik(admin, '#btn-gen-password');
    sandiBaru = await admin.$eval('#acc-password', (e) => e.value);
    const kode = await kirim(admin, async () => {
      await admin.evaluate(() => document.getElementById('btn-submit-account').click());
      await jeda(400);
      await setujui(admin);
    });
    const lembaga = sql(`select lembaga_id from pengguna where nama_pengguna='${usernameBaru}'`);
    const harus = sql(`select lembaga_id from pendaftaran_lks where id='${idTerima}'`);
    return { lolos: lembaga && lembaga === harus, nyata: `Respons ${kode}, akun ${lembaga ? 'tersimpan' : 'tidak tersimpan'}`, layar: admin };
  });
  const pBaru = await halamanBaru();
  await uji('K-03', 'Akun LKS', 'Pengurus masuk dengan akun baru', 'Username dan sandi dari K-02', 'Dasbor LKS terbuka', async () => {
    await masukForm(pBaru, usernameBaru, sandiBaru);
    return { lolos: jalur(pBaru) === '/lks/overview', nyata: `Membuka ${jalur(pBaru)}`, layar: pBaru };
  });
  const idBaru = sql(`select id from pengguna where nama_pengguna='${usernameBaru}'`);
  await uji('K-04', 'Akun LKS', 'Admin mengatur ulang sandi akun LKS', 'Tombol Reset Sandi', 'Sesi akun berakhir dan sandi lama tidak berlaku', async () => {
    await buka(admin, '/admin/partner-accounts');
    await admin.evaluate((nama) => {
      const baris = [...document.querySelectorAll('tr')].find((t) => t.textContent.includes(nama));
      [...baris.querySelectorAll('button')].find((b) => /Reset/.test(b.textContent)).click();
    }, usernameBaru);
    await jeda(400);
    const kode = await kirim(admin, async () => {
      await klik(admin, '[data-modal-confirm]');
      await jeda(400);
      await setujui(admin);
    });
    await buka(pBaru, '/lks/overview');
    const sesi = jalur(pBaru);
    await masukForm(pBaru, usernameBaru, sandiBaru);
    return { lolos: sesi === '/login' && jalur(pBaru) === '/login', nyata: `Respons ${kode}. Sesi lama ke ${sesi}, sandi lama ${jalur(pBaru) === '/login' ? 'ditolak' : 'masih berlaku'}`, layar: pBaru };
  });
  await uji('K-05', 'Akun LKS', 'Admin mencabut akun LKS', 'Tombol Cabut Akses', 'Akun nonaktif dan data lembaga tetap ada', async () => {
    await buka(admin, '/admin/partner-accounts');
    await kirim(admin, async () => {
      await admin.evaluate((id) => document.querySelector(`[data-action-url="/admin/partner-accounts/${id}"]`).click(), idBaru);
      await jeda(400);
      await setujui(admin);
    });
    const aktif = sql(`select aktif from pengguna where id=${idBaru}`);
    const lembaga = sql(`select count(*) from lembaga l join pengguna p on p.lembaga_id=l.id where p.id=${idBaru}`);
    return { lolos: aktif === '0' && lembaga === '1', nyata: `Aktif=${aktif}, data lembaga ${lembaga === '1' ? 'tetap ada' : 'hilang'}`, layar: admin };
  });

  // Profil lembaga
  await uji('P-01', 'Profil lembaga', 'Menyimpan profil tanpa nama lembaga', 'Nama lembaga kosong', 'Data ditolak dan pesan kesalahan tampil', async () => {
    await buka(lks, '/lks/profile');
    await lks.evaluate(() => document.querySelector('[data-open-dialog]')?.click());
    await jeda(400);
    const lama = sql(`select nama from lembaga where id='${lembagaSaya}'`);
    await setNilai(lks, '#organization-form [name="title"]', '');
    await kirim(lks, () => lks.evaluate(() => document.querySelector('#organization-form').requestSubmit()), 2500);
    const teks = await pesan(lks);
    return { lolos: sql(`select nama from lembaga where id='${lembagaSaya}'`) === lama && teks.length > 0, nyata: `Nama tetap. Pesan: ${teks}`, layar: lks };
  });
  await uji('P-02', 'Profil lembaga', 'Mengubah kontak lembaga', 'Kontak 080000007777', 'Perubahan tersimpan dan versi bertambah', async () => {
    await buka(lks, '/lks/profile');
    const versi = Number(sql(`select versi from lembaga where id='${lembagaSaya}'`));
    await setNilai(lks, '#organization-form [name="contact"]', '080000007777');
    const kode = await kirim(lks, () => lks.evaluate(() => document.querySelector('#organization-form').requestSubmit()));
    const [kontak, baru] = sql(`select kontak, versi from lembaga where id='${lembagaSaya}'`).split('\t');
    return { lolos: kontak === '080000007777' && Number(baru) === versi + 1, nyata: `Respons ${kode}, kontak ${kontak}, versi ${versi} ke ${baru}`, layar: lks };
  });

  // Pengurus
  const jumlahPengurus = () => Number(sql(`select count(*) from pengurus_lks where lembaga_id='${lembagaSaya}'`));
  const isiPengurus = async (nik) => {
    await buka(lks, '/lks/new-member');
    await setNilai(lks, '[name="title"]', `Personel Uji ${stamp}`);
    await setNilai(lks, '[name="nik"]', nik);
    await setNilai(lks, '[name="birthPlace"]', 'Banjarbaru');
    await setNilai(lks, '[name="birthDate"]', '1990-01-15');
    await lks.evaluate(() => { const s = document.querySelector('[name="gender"]'); s.selectedIndex = s.options.length - 1; s.dispatchEvent(new Event('change', { bubbles: true })); });
    await setNilai(lks, '[name="contact"]', '080000006666');
    await setNilai(lks, '[name="ktpAddress"]', 'Jl. Contoh No. 1, Banjarbaru');
    await lks.evaluate(() => { const s = document.querySelector('[name="category"]'); s.selectedIndex = s.options.length - 1; s.dispatchEvent(new Event('change', { bubbles: true })); });
  };
  await uji('U-01', 'Pengurus', 'Menyimpan personel dengan formulir kosong', 'Semua isian kosong', 'Data ditolak dan pesan kesalahan tampil', async () => {
    await buka(lks, '/lks/new-member');
    const awal = jumlahPengurus();
    await kirim(lks, () => lks.evaluate(() => document.querySelector('.q-member-form').requestSubmit()), 2500);
    const teks = await pesan(lks);
    return { lolos: jumlahPengurus() === awal && teks.length > 0, nyata: `Data tetap ${awal}. Pesan: ${teks}`, layar: lks };
  });
  await uji('U-02', 'Pengurus', 'Menyimpan personel dengan NIK kurang digit', 'NIK 15 digit', 'Data ditolak dan pesan kesalahan tampil', async () => {
    await isiPengurus('637100000000001');
    const awal = jumlahPengurus();
    await kirim(lks, () => lks.evaluate(() => document.querySelector('.q-member-form').requestSubmit()), 2500);
    const teks = await pesan(lks);
    return { lolos: jumlahPengurus() === awal && teks.length > 0, nyata: `Data tetap ${awal}. Pesan: ${teks}`, layar: lks };
  });
  await uji('U-03', 'Pengurus', 'Menyimpan personel dengan data benar', 'NIK 16 digit dan isian wajib', 'Personel tersimpan pada lembaga pengguna', async () => {
    await isiPengurus(`63710000${stamp}01`);
    const awal = jumlahPengurus();
    const kode = await kirim(lks, () => lks.evaluate(() => document.querySelector('.q-member-form').requestSubmit()));
    return { lolos: jumlahPengurus() === awal + 1, nyata: `Respons ${kode}, jumlah ${awal} ke ${jumlahPengurus()}`, layar: lks };
  });

  // Klien
  const jumlahKlien = () => Number(sql(`select count(*) from klien_lks where lembaga_id='${lembagaSaya}'`));
  await uji('L-01', 'Klien', 'Menyimpan klien dengan formulir kosong', 'Semua isian kosong', 'Data ditolak dan pesan kesalahan tampil', async () => {
    await buka(lks, '/lks/new-resident');
    const awal = jumlahKlien();
    await kirim(lks, () => klikTeks(lks, 'Simpan Klien'), 2500);
    const teks = await pesan(lks);
    return { lolos: jumlahKlien() === awal && teks.length > 0, nyata: `Data tetap ${awal}. Pesan: ${teks}`, layar: lks };
  });
  const isiKlien = async () => {
    await buka(lks, '/lks/new-resident');
    await klikTeks(lks, 'Isi data contoh');
    await jeda(500);
  };
  const kodeUji = `UJI-${stamp}`;
  await uji('L-02', 'Klien', 'Menyimpan klien dengan data benar', 'Data contoh dan kode baru', 'Klien tersimpan pada lembaga pengguna', async () => {
    await isiKlien();
    await setNilai(lks, '[name="code"]', kodeUji);
    await setNilai(lks, '[name="nik"]', `63720000${stamp}01`);
    const awal = jumlahKlien();
    const kode = await kirim(lks, () => klikTeks(lks, 'Simpan Klien'));
    const teks = await pesan(lks);
    return { lolos: jumlahKlien() === awal + 1, nyata: `Respons ${kode}, jumlah ${awal} ke ${jumlahKlien()}. ${teks}`, layar: lks };
  });
  await uji('L-03', 'Klien', 'Menyimpan klien dengan kode yang sudah dipakai', `Kode ${kodeUji}`, 'Data ditolak dan pesan kesalahan tampil', async () => {
    await isiKlien();
    await setNilai(lks, '[name="code"]', kodeUji);
    await setNilai(lks, '[name="nik"]', `63730000${stamp}01`);
    const awal = jumlahKlien();
    const kode = await kirim(lks, () => klikTeks(lks, 'Simpan Klien'));
    const teks = await pesan(lks);
    return { lolos: jumlahKlien() === awal && kode !== 201, nyata: `Respons ${kode}, jumlah tetap ${jumlahKlien()}. Pesan: ${teks}`, layar: lks };
  });

  // Surat
  const perihal = `Surat Uji ${stamp}`;
  await uji('S-01', 'Surat', 'Menyimpan surat tanpa perihal', 'Perihal kosong', 'Data ditolak dan pesan kesalahan tampil', async () => {
    await buka(lks, '/lks/letters?tab=outgoing');
    await klik(lks, '#btn-toggle-add-letter');
    await jeda(300);
    await setNilai(lks, '#input-subject', '');
    await kirim(lks, () => lks.evaluate(() => document.getElementById('lks-letter-form').requestSubmit()), 2500);
    const teks = await pesan(lks);
    return { lolos: teks.length > 0, nyata: `Pesan: ${teks}`, layar: lks };
  });
  await uji('S-02', 'Surat', 'LKS mengirim surat dengan berkas', 'Perihal dan berkas PDF', 'Surat tersimpan dan Admin menerima notifikasi', async () => {
    await buka(lks, '/lks/letters?tab=outgoing');
    await klik(lks, '#btn-toggle-add-letter');
    await jeda(300);
    await setNilai(lks, '#input-letter-number', `UJI/${stamp}`);
    await setNilai(lks, '#input-subject', perihal);
    await (await lks.$('#input-letter-file')).uploadFile(PDF_CONTOH);
    await jeda(300);
    const kode = await kirim(lks, () => lks.evaluate(() => document.getElementById('lks-letter-form').requestSubmit()));
    const arah = sql(`select arah from surat_lks where perihal='${perihal}'`);
    const notif = sql(`select count(*) from notifikasi where data like '%${perihal}%'`);
    return { lolos: arah === 'lks_ke_dinas' && Number(notif) > 0, nyata: `Respons ${kode}, arah ${arah || 'tidak tersimpan'}, notifikasi ${notif}`, layar: lks };
  });
  await uji('S-03', 'Surat', 'Admin menandai surat masuk sudah dibaca', 'Surat dari S-02, tombol Tandai dibaca', 'Waktu baca tercatat', async () => {
    const id = sql(`select id from surat_lks where perihal='${perihal}'`);
    await buka(admin, '/admin/letters/lks');
    await admin.evaluate((i) => document.querySelector(`[data-open-dialog="letter-detail-${i}"]`).click(), id);
    await jeda(400);
    await kirim(admin, () => admin.evaluate((i) => document.querySelector(`[data-action-url$="/letters/${i}/read"]`).click(), id));
    await jeda(500);
    const baca = sql(`select dibaca_pada from surat_lks where id='${id}'`);
    return { lolos: baca && baca !== 'NULL', nyata: `Waktu baca ${baca}`, layar: admin };
  });

  // Laporan semester
  await uji('R-01', 'Laporan semester', 'Mengirim laporan pada periode yang masih berjalan', 'Laporan Semester 2 tahun 2026', 'Pengiriman ditolak, draf tetap bisa disimpan', async () => {
    const id = sql(`select id from laporan_semester where lembaga_id='${lembagaSaya}' and tahun=2026 and semester=2 limit 1`);
    await buka(lks, `/lks/edit/${id}`);
    const ada = await lks.$('[data-report-send]');
    const tutup = ada ? await lks.$eval('[data-report-send]', (b) => b.disabled) : true;
    let kode = null;
    if (ada && !tutup) kode = await kirim(lks, () => klik(lks, '[data-report-send]'), 3000);
    const status = sql(`select status from laporan_semester where id='${id}'`);
    return { lolos: status === 'Draf', nyata: `Tombol kirim ${ada ? (tutup ? 'nonaktif' : 'aktif') : 'tidak tersedia'}, respons ${kode}, status ${status}`, layar: lks };
  });
  await uji('R-02', 'Laporan semester', 'Menyimpan draf laporan', 'Tombol Simpan draf', 'Draf tersimpan dan status tetap Draf', async () => {
    const id = sql(`select id from laporan_semester where lembaga_id='${lembagaSaya}' and status='Draf' limit 1`);
    await buka(lks, `/lks/edit/${id}`);
    const kode = await kirim(lks, () => klikTeks(lks, 'Simpan draf'));
    const status = sql(`select status from laporan_semester where id='${id}'`);
    return { lolos: kode >= 200 && kode < 300 && status === 'Draf', nyata: `Respons ${kode}, status ${status}`, layar: lks };
  });

  let idLaporan = '';
  await uji('R-03', 'Laporan semester', 'Mengirim laporan pada periode yang sudah berakhir', 'Laporan Semester 2 tahun 2025', 'Status menjadi Terkirim dan Admin menerima notifikasi', async () => {
    idLaporan = sql(`select id from laporan_semester where lembaga_id='${lembagaSaya}' and tahun=2025 and semester=2 limit 1`);
    if (!idLaporan) {
      await buka(lks, '/lks/reports');
      await setNilai(lks, '#new-report-year', '2025');
      await lks.evaluate(() => {
        const s = document.querySelector('#new-report-quarter');
        s.selectedIndex = s.options.length - 1;
        s.dispatchEvent(new Event('change', { bubbles: true }));
      });
      await kirim(lks, () => klik(lks, '#create-quarterly'));
      await jeda(1500);
      idLaporan = sql(`select id from laporan_semester where lembaga_id='${lembagaSaya}' and tahun=2025 and semester=2 limit 1`);
    }
    await buka(lks, `/lks/edit/${idLaporan}`);
    await klik(lks, '[data-report-send]');
    await jeda(500);
    const adaDialog = await lks.$('dialog[open]');
    const kode = adaDialog ? await kirim(lks, () => setujui(lks)) : null;
    await jeda(1000);
    const status = sql(`select status from laporan_semester where id='${idLaporan}'`);
    const teks = await pesan(lks);
    return { lolos: status === 'Terkirim', nyata: `Respons ${kode}, status ${status}. ${teks}`, layar: lks };
  });
  await uji('R-04', 'Laporan semester', 'Admin membaca laporan yang sudah terkirim', 'Laporan dari R-03', 'Detail laporan terbuka', async () => {
    const r = await buka(admin, `/admin/report/${idLaporan}`);
    return { lolos: r.status() === 200, nyata: `Kode ${r.status()}`, layar: admin };
  });

  // Ekspor dan notifikasi
  await uji('N-01', 'Notifikasi', 'Menandai semua notifikasi sudah dibaca', 'Tombol tandai semua', 'Tidak ada notifikasi yang belum dibaca', async () => {
    await buka(admin, '/admin/notifications');
    const idAdmin = sql("select id from pengguna where email='admin@sidaya.example'");
    const awal = sql(`select count(*) from notifikasi where notifiable_id=${idAdmin} and read_at is null`);
    await kirim(admin, () => klik(admin, '#mark-notifications-read'));
    const akhir = sql(`select count(*) from notifikasi where notifiable_id=${idAdmin} and read_at is null`);
    return { lolos: akhir === '0', nyata: `Belum dibaca ${awal} ke ${akhir}`, layar: admin };
  });
  await uji('E-01', 'Ekspor', 'Mengunduh data pengurus sebagai PDF', 'Tombol Unduh PDF pada Pengurus', 'Berkas PDF terunduh', async () => {
    const folder = fs.mkdtempSync(path.join(SEMENTARA, 'unduh-'));
    await buka(lks, '/lks/members');
    const cdp = await lks.createCDPSession();
    await cdp.send('Browser.setDownloadBehavior', { behavior: 'allow', browserContextId: lks.browserContext().id, downloadPath: folder });
    await klik(lks, '[data-export-url][data-export-format="pdf"]');
    for (let i = 0; i < 40 && !fs.readdirSync(folder).some((f) => f.endsWith('.pdf')); i += 1) await jeda(500);
    const f = fs.readdirSync(folder).find((x) => x.endsWith('.pdf'));
    return { lolos: Boolean(f), nyata: f ? `Terunduh ${f}` : 'Tidak terunduh', layar: lks };
  });

  await browser.close();
  fs.writeFileSync(path.join(FOLDER, 'hasil.json'), JSON.stringify(hasil, null, 2));
  const hitung = (s) => hasil.filter((h) => h.status === s).length;
  console.log(`\nTotal ${hasil.length}: Sesuai ${hitung('Sesuai')}, Tidak sesuai ${hitung('Tidak sesuai')}, Belum teruji ${hitung('Belum teruji')}`);
}

main().catch(async (galat) => {
  console.error(galat);
  fs.writeFileSync(path.join(FOLDER, 'hasil.json'), JSON.stringify(hasil, null, 2));
  if (browser) await browser.close();
  process.exit(1);
});
