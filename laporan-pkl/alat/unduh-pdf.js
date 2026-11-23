// Mengunduh lima laporan PDF SiDaya dari server lokal yang memakai data contoh fiktif.
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

const DASAR = 'http://127.0.0.1:8123';
const UNDUHAN = path.join(__dirname, '..', 'tangkapan', 'unduhan');
const DAFTAR = [
  ['admin', 'laporan-daftar-lks', '/admin/organizations/lks'],
  ['admin', 'laporan-rekap-penghuni', '/admin/occupancy'],
  ['admin', 'laporan-semester', '/admin/reports/lks'],
  ['admin', 'laporan-surat', '/admin/letters/lks'],
  ['lks', 'laporan-pengurus', '/lks/members'],
];

async function tunggu(folder, sebelum) {
  for (let i = 0; i < 60; i += 1) {
    const baru = fs.readdirSync(folder).filter((f) => !sebelum.has(f) && !f.endsWith('.crdownload'));
    if (baru.length) return baru[0];
    await new Promise((selesai) => { setTimeout(selesai, 500); });
  }
  return null;
}

(async () => {
  fs.mkdirSync(UNDUHAN, { recursive: true });
  const browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: 'shell',
  });
  const halaman = {};
  for (const peran of ['admin', 'lks']) {
    const konteks = await browser.createBrowserContext();
    const page = await konteks.newPage();
    await page.setViewport({ width: 1280, height: 860 });
    await page.goto(`${DASAR}/login`, { waitUntil: 'networkidle2' });
    const tombol = await page.$(`form[action*="login/demo/${peran}"] button`);
    await Promise.all([page.waitForNavigation({ waitUntil: 'networkidle2' }), tombol.click()]);
    const cdp = await page.createCDPSession();
    await cdp.send('Browser.setDownloadBehavior', {
      behavior: 'allow',
      browserContextId: konteks.id,
      downloadPath: UNDUHAN,
    });
    halaman[peran] = page;
  }
  for (const [peran, nama, url] of DAFTAR) {
    const page = halaman[peran];
    await page.goto(DASAR + url, { waitUntil: 'networkidle2' });
    const sebelum = new Set(fs.readdirSync(UNDUHAN));
    await page.click('[data-export-url][data-export-format="pdf"]');
    const hasil = await tunggu(UNDUHAN, sebelum);
    if (!hasil) {
      console.log(nama, 'GAGAL');
    } else {
      fs.renameSync(path.join(UNDUHAN, hasil), path.join(UNDUHAN, `${nama}.pdf`));
      console.log(nama, hasil);
    }
  }
  await browser.close();
})().catch((galat) => { console.error(galat); process.exit(1); });
