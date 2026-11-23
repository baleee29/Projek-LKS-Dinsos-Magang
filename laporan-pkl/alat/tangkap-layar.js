// Mengambil tangkapan layar SiDaya dari server lokal yang memakai data contoh fiktif.
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

const DASAR = 'http://127.0.0.1:8123';
const FOLDER = path.join(__dirname, '..', 'tangkapan');
const UNDUHAN = path.join(FOLDER, 'unduhan');

const halamanPublik = [
  ['beranda', '/'],
  ['login', '/login'],
  ['pendaftaran-lks', '/pilar/pendaftaran-lks.html'],
];
const halamanAdmin = [
  ['admin-ringkasan', '/admin/overview'],
  ['admin-pendaftaran', '/admin/registrations/lks'],
  ['admin-akun-lks', '/admin/partner-accounts'],
  ['admin-data-lks', '/admin/organizations/lks'],
  ['admin-rekap-penghuni', '/admin/occupancy'],
  ['admin-laporan', '/admin/reports/lks'],
  ['admin-surat', '/admin/letters/lks'],
];
const halamanLks = [
  ['lks-ringkasan', '/lks/overview'],
  ['lks-profil', '/lks/profile'],
  ['lks-pengurus', '/lks/members'],
  ['lks-tambah-pengurus', '/lks/new-member'],
  ['lks-tambah-klien', '/lks/new-resident'],
  ['lks-rekap-klien', '/lks/occupancy'],
  ['lks-laporan', '/lks/reports'],
  ['lks-surat', '/lks/letters'],
];

async function buka(page, nama, url, sesudah) {
  const respons = await page.goto(DASAR + url, { waitUntil: 'networkidle2', timeout: 60000 });
  if (sesudah) await sesudah();
  await page.screenshot({ path: path.join(FOLDER, `${nama}.png`) });
  console.log(nama, respons.status());
}

async function tautanPertama(page, url, pola) {
  await page.goto(DASAR + url, { waitUntil: 'networkidle2', timeout: 60000 });
  return page.evaluate((p) => {
    const a = [...document.querySelectorAll('a[href]')].find((el) => el.getAttribute('href').includes(p));
    return a ? new URL(a.href).pathname : null;
  }, pola);
}

async function masuk(browser, peran) {
  const konteks = await browser.createBrowserContext();
  const page = await konteks.newPage();
  await page.setViewport({ width: 1280, height: 860 });
  await page.goto(`${DASAR}/login`, { waitUntil: 'networkidle2' });
  const tombol = await page.$(`form[action*="login/demo/${peran}"] button`);
  if (!tombol) throw new Error(`Tombol masuk cepat ${peran} tidak ditemukan`);
  await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle2' }), tombol.click()]);
  return page;
}

(async () => {
  fs.mkdirSync(UNDUHAN, { recursive: true });
  const browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: 'shell',
  });

  const umum = await browser.newPage();
  await umum.setViewport({ width: 1280, height: 860 });
  for (const [nama, url] of halamanPublik) await buka(umum, nama, url);
  // Tombol masuk cepat hanya ada di lingkungan lokal, jadi tangkapan layar laporan tidak menampilkannya.
  await buka(umum, 'login', '/login', () => umum.addStyleTag({ content: '.demo-accounts { display: none !important; }' }));
  await buka(umum, 'pendaftaran-lks', '/pilar/pendaftaran-lks.html', () => umum.evaluate(() => {
    const isian = document.querySelector('[name="lks-nama"], #lks-nama');
    if (isian) window.scrollTo({ top: isian.getBoundingClientRect().top + window.scrollY - 220, behavior: 'instant' });
  }));

  const admin = await masuk(browser, 'admin');
  for (const [nama, url] of halamanAdmin) await buka(admin, nama, url);
  const detail = await tautanPertama(admin, '/admin/registrations/lks', '/admin/registration/');
  if (detail) await buka(admin, 'admin-detail-pendaftaran', detail);

  const lks = await masuk(browser, 'lks');
  for (const [nama, url] of halamanLks) await buka(lks, nama, url);
  const editor = await tautanPertama(lks, '/lks/drafts', '/lks/edit/');
  if (editor) await buka(lks, 'lks-isi-laporan', editor);
  await buka(lks, 'akses-ditolak-403', '/admin/overview');

  for (const [peran, page, daftar] of [['admin', admin, halamanAdmin.slice(3)], ['lks', lks, halamanLks.slice(2, 3)]]) {
    for (const [nama, url] of daftar) {
      await page.goto(DASAR + url, { waitUntil: 'networkidle2' });
      const info = await page.evaluate(() => [...document.querySelectorAll('[data-export-url], [data-export], [data-export-format], [data-download-url]')]
        .slice(0, 6).map((el) => `${el.tagName}.${el.className} ${el.textContent.trim().slice(0, 30)} ${[...el.attributes].map((a) => a.name).filter((n) => n.startsWith('data-')).join(',')}`));
      console.log('EKSPOR', peran, nama, JSON.stringify(info));
    }
  }
  await browser.close();
})().catch((galat) => { console.error(galat); process.exit(1); });
