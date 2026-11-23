// Mengumpulkan semua sumber daftar pustaka ke laporan-pkl/daftar-pustaka dengan nomor urut pustaka.
const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');
const { urutkanPustaka } = require('./format');
const bab1 = require('./bab-1');
const bab2 = require('./bab-2');
const bab3 = require('./bab-3');
const bab4 = require('./bab-4');

const AKAR = path.join(__dirname, '..');
const JURNAL = path.join(AKAR, 'jurnal');
const TUJUAN = path.join(AKAR, 'daftar-pustaka');
const UNDUHAN = 'C:\\Users\\ACER\\Downloads';
const DINSOS = 'https://dinsos.banjarbarukota.go.id';

// Urutan mengikuti daftar pustaka. kunci dipakai untuk memastikan berkas cocok dengan entrinya.
const SUMBER = [
  { kunci: 'Anggi, N.', nama: 'Anggi-Ilhadi-2026-Dinsos-Lhokseumawe', jurnal: true, berkas: 'Anggi dan Ilhadi 2026', url: 'https://journal.stmikjayakarta.ac.id/index.php/JMIJayakarta/article/download/2267/1345' },
  { kunci: 'ANTARA News', nama: 'ANTARA-2017-Eks-Bangunan-RSUD', halaman: 'https://kalsel.antaranews.com/berita/44045/banjarbaru-hilangkan-kesan-angker-eks-bangunan-rsud' },
  { kunci: 'Darojat, E.', nama: 'Darojat-2022-Vulnerability-E-Government', jurnal: true, berkas: 'Darojat dkk. 2022', url: 'https://ejournal.undip.ac.id/index.php/jsinbis/article/download/47792/pdf/' },
  { kunci: '(t.t.-a)', nama: 'Dinsos-Banjarbaru-a-Bidang-Pemberdayaan-Sosial', halaman: `${DINSOS}/bid-pemberdayaan-sosial/` },
  { kunci: '(t.t.-b)', nama: 'Dinsos-Banjarbaru-b-Data-LKSA', unduh: `${DINSOS}/wp-content/uploads/2023/03/DATA-LKSA-2023-BARU-PER-MARET-2023.pdf`, url: `${DINSOS}/data-lksa-panti-anak/` },
  { kunci: '(t.t.-c)', nama: 'Dinsos-Banjarbaru-c-Visi-dan-Misi', halaman: `${DINSOS}/visi-dan-misi/` },
  { kunci: '(t.t.-d)', nama: 'Dinsos-Banjarbaru-d-Profil', unduh: 'https://ppid.kemendagri.go.id/storage/dokumen/9MAgSze6CzR6tpxQxzT92gh1tWZhwxxlNAsgBABv.pdf', url: 'https://ppid.kemendagri.go.id/storage/dokumen/9MAgSze6CzR6tpxQxzT92gh1tWZhwxxlNAsgBABv.pdf' },
  { kunci: 'UNISKA. (2020)', nama: 'FTI-UNISKA-2020-Pedoman-PKL', lokal: path.join(UNDUHAN, 'pedoman-pkl-fti-v2-0-2020 (1).pdf'), url: 'Berkas pedoman dari fakultas' },
  { kunci: 'Hendiyani, M.', nama: 'Hendiyani-Fansuri-2024-Clearance-SPBE', jurnal: true, berkas: 'Hendiyani dan Fansuri 2024', url: 'https://ejournal.ipdn.ac.id/JMB/article/view/4940/2010' },
  { kunci: 'Huripah, E.', nama: 'Huripah-2021-Kapabilitas-Relasional-LKS-Garut', jurnal: true, berkas: 'Huripah dkk. 2021', url: 'https://jurnal.poltekesos.ac.id/index.php/lindayasos/article/download/390/308' },
  { kunci: 'Kadim, A.', nama: 'Kadim-2023-Laravel-Notula', jurnal: true, berkas: 'Kadim dkk. 2023', url: 'https://ejournal.undip.ac.id/index.php/jsinbis/article/download/45297/pdf' },
  { kunci: 'Keban, Y.', nama: 'Keban-2024-E-Government-Maturity-TOE', jurnal: true, berkas: 'Keban dkk. 2024', url: 'https://jurnal.ugm.ac.id/jsp/article/download/81803/pdf' },
  { kunci: 'Kementerian Sosial', nama: 'Kemensos-2024-Permensos-5-LKS', unduh: 'https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf', url: 'https://peraturan.bpk.go.id/Download/371889/permensos-no-5-tahun-2024.pdf' },
  { kunci: 'Kusumadewa, S.', nama: 'Kusumadewa-2026-RBAC-Laravel', jurnal: true, berkas: 'Kusumadewa dkk. 2026', url: 'https://ejournal.itn.ac.id/jati/article/download/19234/10120/' },
  { kunci: 'Maisyarah,', nama: 'Maisyarah-2024-E-Readiness-Balangan', jurnal: true, berkas: 'Maisyarah dkk. 2024', url: 'https://ejournal.undip.ac.id/index.php/ilmusos/article/download/64120/26386' },
  { kunci: 'Malioy, R.', nama: 'Malioy-2024-PIECES-Maluku-Barat-Daya', jurnal: true, berkas: 'Malioy dkk. 2024', url: 'https://ejournal.undip.ac.id/index.php/jsinbis/article/download/60395/pdf' },
  { kunci: 'Nurmala, S.', nama: 'Nurmala-Wiraguna-2026-Tanggung-Jawab-DJP-Data-NPWP', jurnal: true, berkas: 'Nurmala dan Wiraguna 2026', url: 'https://ejurnal.ubharajaya.ac.id/index.php/JKI/article/download/4817/2747/14835' },
  { kunci: 'Pemerintah Kota Banjarbaru. (2008)', nama: 'Pemkot-Banjarbaru-2008-Perda-11', unduh: 'https://peraturan.bpk.go.id/Download/11840/11%20TAHUN%202008.pdf', url: 'https://peraturan.bpk.go.id/Download/11840/11%20TAHUN%202008.pdf' },
  { kunci: 'Pemerintah Kota Banjarbaru. (2016)', nama: 'Pemkot-Banjarbaru-2016-Perda-10', unduh: 'https://peraturan.bpk.go.id/Download/228757/Perda%20No.%2010%20Tahun%202016%20edit%20searchable.pdf', url: 'https://peraturan.bpk.go.id/Download/228757/Perda%20No.%2010%20Tahun%202016%20edit%20searchable.pdf' },
  { kunci: 'Pemerintah Kota Banjarbaru. (2021)', nama: 'Pemkot-Banjarbaru-2021-Perwali-73', unduh: 'https://peraturan.bpk.go.id/Download/283987/Perwal%2073%20Tahun%202021%20.pdf', url: 'https://peraturan.bpk.go.id/Download/283987/Perwal%2073%20Tahun%202021%20.pdf' },
  { kunci: 'Pemerintah Kota Banjarbaru. (2023)', nama: 'Pemkot-Banjarbaru-2023-Perwali-20', unduh: 'https://peraturan.bpk.go.id/Download/303610/Perwal%20BJB%20No%2020%20Tahun%202023.pdf', url: 'https://peraturan.bpk.go.id/Download/303610/Perwal%20BJB%20No%2020%20Tahun%202023.pdf' },
  { kunci: 'Purba, D.', nama: 'Purba-Hidayasari-2026-RBAC-Stok-Obat', jurnal: true, berkas: 'Purba dan Hidayasari 2026', url: 'https://sistemasi.ftik.unisi.ac.id/index.php/stmsi/article/view/5958/1168' },
  { kunci: 'Republik Indonesia', nama: 'RI-2009-UU-11-Kesejahteraan-Sosial', unduh: 'https://www.peraturan.go.id/files/uu11-2009.pdf', url: 'https://www.peraturan.go.id/files/uu11-2009.pdf' },
  { kunci: 'Ridla, M.', nama: 'Ridla-Bahauddin-2024-PMKS-Banyuwangi', jurnal: true, berkas: 'Ridla dan Baha\'uddin 2024', url: 'https://journal.ibrahimy.ac.id/index.php/JUSTIFY/article/download/5303/2319' },
  { kunci: 'Sinlae, F.', nama: 'Sinlae-2024-Framework-Laravel', jurnal: true, berkas: 'Sinlae dkk. 2024', url: 'https://research.e-siber.org/JSMD/article/download/186/116' },
  { kunci: 'Sumarhaeti, H.', nama: 'Sumarhaeti-2025-SPBE-Cianjur', jurnal: true, berkas: 'Sumarhaeti 2025', url: 'https://jurnal.apmd.ac.id/index.php/JIRReG/article/download/715/381/2891' },
  { kunci: 'Tahu, F.', nama: 'Tahu-2024-Pendataan-Anak-LKSA', jurnal: true, berkas: 'Tahu dkk. 2024', url: 'https://journal.ypmma.org/index.php/dj/article/download/181/150/494' },
  { kunci: 'Triandini, E.', nama: 'Triandini-2022-UML-Similarity', jurnal: true, berkas: 'Triandini dkk. 2022', url: 'https://journal.unipdu.ac.id/index.php/register/article/download/2248/pdf/7309' },
];

const bersih = (teks) => teks.replace(/\*/g, '');
const pustaka = urutkanPustaka(new Set([...bab1.isi, ...bab2.isi, ...bab3.isi, ...bab4.isi].filter((p) => p.teksPustaka).map((p) => p.teksPustaka)));

(async () => {
  if (pustaka.length !== SUMBER.length) throw new Error(`Pustaka ${pustaka.length}, sumber ${SUMBER.length}`);
  fs.mkdirSync(TUJUAN, { recursive: true });
  const browser = await puppeteer.launch({
    executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    headless: 'shell',
  });
  const daftar = ['DAFTAR SUMBER PUSTAKA', 'Laporan PKL Muhammad Iqbal, NPM 2310010439', ''];
  for (let i = 0; i < SUMBER.length; i += 1) {
    const s = SUMBER[i];
    const entri = pustaka.find((p) => p.includes(s.kunci));
    if (!entri) throw new Error(`Entri pustaka tidak ditemukan: ${s.kunci}`);
    const nomor = String(i + 1).padStart(2, '0');
    const keluar = path.join(TUJUAN, `${nomor}-${s.nama}.pdf`);
    if (s.jurnal) fs.copyFileSync(path.join(JURNAL, `${s.berkas}.pdf`), keluar);
    else if (s.lokal) fs.copyFileSync(s.lokal, keluar);
    else if (s.unduh) execFileSync('curl', ['-sSL', '--max-time', '60', '-A', 'Mozilla/5.0', '-o', keluar, s.unduh]);
    else {
      const page = await browser.newPage();
      await page.goto(s.halaman, { waitUntil: 'networkidle2', timeout: 90000 });
      await page.pdf({ path: keluar, format: 'A4', printBackground: true, margin: { top: '1cm', bottom: '1cm', left: '1cm', right: '1cm' } });
      await page.close();
    }
    const kepala = fs.readFileSync(keluar).subarray(0, 4).toString();
    if (kepala !== '%PDF') throw new Error(`Bukan PDF: ${keluar}`);
    const ukuran = Math.round(fs.statSync(keluar).size / 1024);
    console.log(`${nomor} ${s.nama}.pdf ${ukuran} KB`);
    daftar.push(`${nomor}. ${bersih(entri)}`, `    Berkas: ${nomor}-${s.nama}.pdf`, `    Sumber: ${s.url || s.halaman}`, '');
  }
  await browser.close();
  fs.writeFileSync(path.join(TUJUAN, '00-DAFTAR-SUMBER.txt'), daftar.join('\r\n'), 'utf8');
})().catch((galat) => { console.error(galat); process.exit(1); });
