"use strict";
// Uncompressed ZIP writer and XML text escaping shared by the XLSX exports. Load before export modules.
window.SidayaXlsx = (() => {
    function crc32(bytes) {
        let crc = 0xffffffff;
        for (const byte of bytes) {
            crc ^= byte;
            for (let bit = 0; bit < 8; bit++) crc = (crc >>> 1) ^ (crc & 1 ? 0xedb88320 : 0);
        }
        return (crc ^ 0xffffffff) >>> 0;
    }
    // Without tooLargeMessage the ZIP32 limits are not checked, matching callers that never checked them.
    function zip(parts, tooLargeMessage) {
        const encoder = new TextEncoder();
        const local = [], central = [];
        let offset = 0;
        for (const [path, content] of Object.entries(parts)) {
            const name = encoder.encode(path), bytes = encoder.encode(content), crc = crc32(bytes);
            const header = new Uint8Array(30), view = new DataView(header.buffer);
            view.setUint32(0, 0x04034b50, true); view.setUint16(4, 20, true); view.setUint16(6, 0x800, true); view.setUint16(12, 33, true);
            view.setUint32(14, crc, true); view.setUint32(18, bytes.length, true); view.setUint32(22, bytes.length, true); view.setUint16(26, name.length, true);
            local.push(header, name, bytes);
            const directory = new Uint8Array(46), item = new DataView(directory.buffer);
            item.setUint32(0, 0x02014b50, true); item.setUint16(4, 20, true); item.setUint16(6, 20, true); item.setUint16(8, 0x800, true); item.setUint16(14, 33, true);
            item.setUint32(16, crc, true); item.setUint32(20, bytes.length, true); item.setUint32(24, bytes.length, true); item.setUint16(28, name.length, true); item.setUint32(42, offset, true);
            central.push(directory, name); offset += header.length + name.length + bytes.length;
        }
        const count = central.length / 2, size = central.reduce((sum, bytes) => sum + bytes.length, 0);
        if (tooLargeMessage && (count > 65535 || offset + size > 0xffffffff)) throw new Error(tooLargeMessage);
        const end = new Uint8Array(22), view = new DataView(end.buffer);
        view.setUint32(0, 0x06054b50, true); view.setUint16(8, count, true); view.setUint16(10, count, true); view.setUint32(12, size, true); view.setUint32(16, offset, true);
        return new Blob([...local, ...central, end], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    }
    const xml = (value) =>
        String(value ?? "")
            .replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, "")
            .replace(/[<>&"']/g, (c) => ({ "<": "&lt;", ">": "&gt;", "&": "&amp;", '"': "&quot;", "'": "&apos;" }[c]));
    return Object.freeze({ crc32, zip, xml });
})();
