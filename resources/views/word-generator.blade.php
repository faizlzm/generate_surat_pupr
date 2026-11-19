<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Generator Surat Teguran — Buat Surat</title>
<style>
:root{--bg:#f4f6fa;--card:#fff;--ink:#142239;--muted:#64748b;--line:#e3e8ef;--blue:#284d88;--blue2:#1e3a69;--light:#edf3fc;--green:#16744b;--greenbg:#e8f7ef;--orange:#b75b16;--orangebg:#fff1df;--red:#ad3540;--redbg:#fff0f2;--shadow:0 8px 35px rgba(23,39,71,.055)}
*{box-sizing:border-box}html,body{margin:0}body{background:var(--bg);color:var(--ink);font-family:Inter,"Segoe UI",Arial,sans-serif;font-size:14px;line-height:1.4}button,input,textarea,select{font:inherit}button{cursor:pointer}button:disabled{cursor:not-allowed;opacity:.5}button:focus-visible,input:focus-visible,select:focus-visible,textarea:focus-visible{outline:3px solid #a7c5fd;outline-offset:2px}.hidden{display:none!important}
.topbar{height:64px;display:flex;align-items:center;justify-content:space-between;gap:15px;background:#fff;border-bottom:1px solid var(--line);padding:0 max(25px,calc((100vw - 1510px)/2));position:sticky;top:0;z-index:10}
.brand{display:flex;align-items:center;gap:12px;min-width:0}.brandmark{width:36px;height:36px;background:var(--blue);color:white;border-radius:11px;display:grid;place-items:center;font-size:18px;font-weight:800}.brand b{font-size:14px;letter-spacing:.15px}.brand small{display:block;color:var(--muted);font-size:11px}.top-right{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.offline{padding:6px 9px;background:var(--greenbg);color:var(--green);border-radius:30px;font-size:11px;font-weight:700}.btn{border:1px solid var(--line);background:white;color:var(--ink);border-radius:9px;padding:9px 13px;display:inline-flex;align-items:center;justify-content:center;gap:7px;white-space:nowrap;font-size:12.5px;font-weight:650;min-height:35px}.btn:hover{background:#f8fafc}.btn.primary{background:var(--blue);border-color:var(--blue);color:white}.btn.primary:hover{background:var(--blue2)}.btn.danger{color:var(--red)}.btn.sm{padding:6px 9px;min-height:30px;font-size:12px}.btn.ghost{border-color:transparent;background:transparent}.btn.ghost:hover{background:var(--light)}.icon{font-size:15px;line-height:1}
.container{max-width:1510px;margin:0 auto;padding:26px 25px 105px}.heading{display:flex;justify-content:space-between;gap:15px;align-items:end;margin-bottom:20px}.eyebrow{text-transform:uppercase;letter-spacing:1.3px;color:var(--blue);font-size:10px;font-weight:750}.heading h1{margin:5px 0;font-size:26px;letter-spacing:-.75px;line-height:1.2}.heading p{color:var(--muted);margin:0;font-size:13px}.heading-actions{display:flex;gap:8px;align-items:center}.grid-main{display:grid;grid-template-columns:minmax(0,1fr) 290px;gap:19px;align-items:start}.maincol{display:flex;flex-direction:column;gap:16px;min-width:0}.card{background:var(--card);border:1px solid var(--line);border-radius:15px;box-shadow:var(--shadow);overflow:hidden}.cardhead{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:16px 19px;border-bottom:1px solid var(--line)}.cardhead h2{font-size:15px;margin:0;letter-spacing:-.2px}.cardhead p{margin:4px 0 0;color:var(--muted);font-size:11.5px}.cardbody{padding:17px 19px}.pill{font-size:10.5px;padding:5px 8px;border-radius:999px;background:#edf2f9;color:var(--blue);font-weight:750;white-space:nowrap}.fields{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.field{display:flex;flex-direction:column;gap:5px;min-width:0}.field label{font-size:11px;font-weight:700;color:#43536a}.field input,.field select,.field textarea,.searchbox,input.cell-input{border:1px solid #d9e0e9;border-radius:8px;background:white;padding:9px 10px;min-height:35px;color:#17243a;width:100%;font-size:12.5px}.field textarea{resize:vertical;min-height:59px}.full{grid-column:1/-1}.half{grid-column:span 2}.sep{border-top:1px solid var(--line);margin:17px 0}.subrow{display:flex;justify-content:space-between;align-items:center;gap:10px;margin:0 0 10px}.subrow strong{font-size:12.5px}.subrow span{font-size:11px;color:var(--muted)}.reference-list{display:flex;flex-direction:column;gap:8px}.reference-row{display:grid;grid-template-columns:22px minmax(0,1fr) 54px 27px;align-items:start;gap:8px}.reference-row .refn{color:var(--muted);padding-top:8px;text-align:right}.reference-row textarea{border:1px solid #d9e0e9;border-radius:8px;width:100%;padding:9px 10px;font-size:12px;resize:vertical;min-height:40px;max-height:118px}.refpunct{font-weight:800;color:var(--blue);font-size:12px;padding-top:10px}.mini-muted{font-size:11px;color:var(--muted)}.reftools{margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.tab-tools{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center;padding:14px 19px}.left-tools,.right-tools{display:flex;gap:6px;flex-wrap:wrap;align-items:center}.searchbox{max-width:205px;height:35px}.table-wrap{width:100%;max-height:423px;overflow:auto;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}table{border-collapse:separate;border-spacing:0;width:100%}.data-table{min-width:1230px}.data-table th{position:sticky;top:0;background:#f6f8fc;color:#52627a;font-size:10.8px;text-transform:uppercase;letter-spacing:.25px;font-weight:750;text-align:left;z-index:2;padding:10px 10px;border-bottom:1px solid var(--line)}.data-table td{padding:10px;border-bottom:1px solid #edf0f5;vertical-align:middle;font-size:12px}.data-table tr:hover td{background:#f9fbff}.data-table tr.selected td{background:#f0f5ff}.data-table td:first-child,.data-table th:first-child{text-align:center}.name-cell strong{display:block;font-weight:750}.name-cell span{font-size:11px;color:var(--muted)}.one-line{white-space:nowrap;max-width:145px;overflow:hidden;text-overflow:ellipsis}.two-line{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:245px;color:#40516b;line-height:1.3}.status{display:inline-flex;align-items:center;gap:4px;padding:5px 7px;border-radius:30px;font-size:10.5px;font-weight:750}.status.ok{background:var(--greenbg);color:var(--green)}.status.bad{background:var(--orangebg);color:var(--orange)}.tablefoot{padding:12px 19px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}.hint{color:var(--muted);font-size:11.5px}.side{position:sticky;top:85px;display:flex;flex-direction:column;gap:14px}.summary{padding:19px}.summary h3{margin:0 0 4px;font-size:15px}.summary p{margin:0;color:var(--muted);font-size:12px}.summary-big{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:16px 0}.sumstat{padding:12px;border-radius:11px;background:#f5f8fd}.sumstat b{font-size:22px;display:block;line-height:1}.sumstat span{font-size:11px;color:var(--muted);margin-top:7px;display:block}.progress{height:7px;background:#edf1f7;border-radius:999px;overflow:hidden}.progress>span{display:block;height:100%;width:0;background:var(--green);border-radius:999px;transition:width .2s}.stack{display:flex;flex-direction:column;gap:10px}.summaryline{display:flex;align-items:center;justify-content:space-between;font-size:12px}.summaryline span{color:var(--muted)}.summaryline b{font-size:12px}.side-note{padding:16px 18px;background:#f0f5fc;border:1px solid #dce8fb;border-radius:13px}.side-note h3{font-size:12.5px;margin:0 0 8px}.side-note p{font-size:11.5px;line-height:1.6;margin:0;color:#46607e}.side-actions{display:flex;flex-direction:column;gap:8px;margin-top:15px}.side-actions .btn{width:100%}
.bottom{position:fixed;bottom:0;left:0;right:0;border-top:1px solid var(--line);background:rgba(255,255,255,.97);backdrop-filter:blur(12px);z-index:9}.bottom-inner{max-width:1510px;margin:auto;padding:12px 25px;display:flex;align-items:center;justify-content:space-between;gap:14px}.bottom-inner small{font-size:11.5px;color:var(--muted);display:block;margin-top:3px}.bottom-actions{display:flex;align-items:center;gap:8px}
.overlay{position:fixed;inset:0;background:rgba(20,33,55,.53);z-index:40;display:flex;justify-content:center;align-items:center;padding:20px}.modal{background:white;border-radius:16px;box-shadow:0 26px 100px rgba(0,0,0,.22);width:min(100%,660px);max-height:calc(100vh - 40px);display:flex;flex-direction:column;overflow:hidden}.modal.wide{width:min(100%,1130px)}.modal-head{border-bottom:1px solid var(--line);padding:17px 21px;display:flex;justify-content:space-between;align-items:flex-start;gap:10px}.modal-head h2{margin:0;font-size:17px}.modal-head p{margin:4px 0 0;color:var(--muted);font-size:12px}.modal-body{padding:20px 22px;overflow:auto}.modal-foot{border-top:1px solid var(--line);padding:13px 20px;display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap}.close{border:0;background:transparent;border-radius:8px;padding:5px 9px;color:#718096;font-size:24px}.close:hover{background:#f3f6fa}.drawer-wrap{position:fixed;inset:0;z-index:45;background:rgba(20,33,55,.48);display:flex;justify-content:flex-end}.drawer{width:min(100%,510px);height:100%;background:white;display:flex;flex-direction:column;box-shadow:-25px 0 80px #1421372e}.drawer .modal-body{flex:1}.drawer .modal-foot{margin-top:auto}.notice{background:#eff5fd;border:1px solid #d9e6fa;color:#3c587e;font-size:12px;padding:11px 13px;border-radius:10px;line-height:1.55}.notice.warn{background:var(--orangebg);border-color:#f6d7b4;color:#965716}.row{display:flex;gap:10px;align-items:center}.twocol{display:grid;grid-template-columns:1fr 1fr;gap:12px}.table-preview{border:1px solid var(--line);border-radius:8px;overflow:auto;max-height:180px}.table-preview th,.table-preview td{text-align:left;border-bottom:1px solid var(--line);padding:7px 9px;font-size:11px;max-width:170px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.table-preview th{background:#f4f7fc}.file-drop{display:block;border:2px dashed #cbd8ea;background:#fafcff;border-radius:12px;padding:14px;text-align:center;font-size:12px;color:var(--muted);cursor:pointer}.file-drop:hover{background:#f0f6ff}
.preview-layout{display:grid;grid-template-columns:235px minmax(0,1fr);gap:17px;background:#f1f4f9;min-height:350px}.preview-sidebar{background:#fff;border-radius:11px;padding:14px;align-self:start;position:sticky;top:0}.preview-list{max-height:270px;overflow:auto;display:flex;flex-direction:column;gap:4px}.preview-link{border:1px solid transparent;background:transparent;text-align:left;border-radius:8px;padding:10px;width:100%;font-size:12px;display:flex;justify-content:space-between;gap:6px;align-items:center}.preview-link.active{background:var(--light);border-color:#c9daf8;color:var(--blue);font-weight:750}.preview-sheet-wrap{display:flex;flex-direction:column;gap:11px;align-items:center;overflow:auto;max-height:65vh;padding:8px}.preview-switch{display:flex;align-items:center;gap:8px;position:sticky;top:0;z-index:3;background:#f1f4f9;padding:4px;border-radius:9px}.preview-switch .btn.active{background:var(--blue);color:white;border-color:var(--blue)}
.letter-page{width:650px;height:919px;flex-shrink:0;background:white;color:#111;font-family:Arial,sans-serif;font-size:11.6px;line-height:1.24;box-shadow:0 8px 25px rgba(21,31,49,.13);padding:40px 46px 32px;display:flex;flex-direction:column;overflow:hidden}.letter-head{display:flex;align-items:center;justify-content:center;border-bottom:1.4px solid #141414;padding-bottom:3px;gap:10px}.pu-logo{width:56px;height:56px;object-fit:contain}.letter-headtext{text-align:center}.letter-headtext .ministry{font-size:19px;letter-spacing:.3px;line-height:1.04}.letter-headtext .agency{font-size:17px;font-weight:750;line-height:1.08}.letter-headtext .address{font-size:8px}.memo-title{text-align:center;font-size:13px;margin:18px 0 14px}.memo-meta{display:grid;grid-template-columns:77px 8px 1fr;row-gap:3px;margin-bottom:3px}.memo-rule{border-bottom:1px solid #333;margin:4px 0 13px}.memo-ref{margin:0 0 16px}.memo-ref>p{margin:0 0 5px}.memo-ref ol{padding-left:37px;margin:0;list-style:decimal}.memo-ref li{padding-left:3px;margin-bottom:3px;text-align:justify}.memo-ref li::marker{font-size:11px}.memo-content{flex:1;display:flex;flex-direction:column;min-height:0}.memo-content p{margin:0 0 8px}.memo-content ol{margin:0 0 0 16px;padding-left:16px}.memo-content ol>li{margin-bottom:8px;text-align:justify;padding-left:2px}.id-table{margin:7px 0 8px 0;width:100%;border-collapse:collapse}.id-table td{vertical-align:top;padding:2px 0}.id-table td:first-child{width:94px}.id-table td:nth-child(2){width:13px}.id-table td:last-child{text-align:justify}.memo-bottom{margin-top:auto}.signzone{display:grid;grid-template-columns:46% 54%;min-height:87px;align-items:start;gap:5px;margin-top:9px}.signarea{text-align:center;font-size:11px}.signname{margin-top:58px}.approval{border-collapse:collapse;width:100%;font-size:9px;text-align:center}.approval th,.approval td{border:1px solid #444;font-weight:normal;text-align:center}.approval td{height:32px}.approval th{padding:3px}.cc{margin-top:9px;font-size:10.5px}.cc ol{margin:0;padding-left:20px}.cc li{margin:2px 0}.print-stage{display:none}
.toast{position:fixed;bottom:91px;right:25px;background:#173054;color:white;padding:12px 17px;border-radius:10px;box-shadow:0 10px 30px #0003;z-index:90;max-width:390px;font-size:12.5px;opacity:0;pointer-events:none;transform:translateY(12px);transition:.2s}.toast.show{opacity:1;transform:translateY(0)}
@media(max-width:1220px){.grid-main{grid-template-columns:1fr}.side{position:static;display:grid;grid-template-columns:1fr 1fr}.fields{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.topbar{padding:0 15px}.brand small,.offline{display:none}.container{padding:20px 14px 130px}.heading{display:block}.heading-actions{margin-top:13px}.heading h1{font-size:22px}.fields{grid-template-columns:1fr 1fr}.half{grid-column:1/-1}.side{display:flex}.cardhead,.cardbody,.tab-tools,.tablefoot{padding-left:13px;padding-right:13px}.bottom-inner{padding:10px 13px;align-items:flex-start;flex-direction:column}.bottom-actions{width:100%}.bottom-actions .btn{flex:1}.preview-layout{grid-template-columns:1fr}.preview-sidebar{position:static}.preview-list{max-height:88px;flex-direction:row;overflow:auto}.preview-link{width:auto;white-space:nowrap}.modal-body{padding:14px}.twocol{grid-template-columns:1fr}.letter-page{transform-origin:top center;scale:.63;margin-bottom:-340px}.top-right .btn .hide-mobile{display:none}}@media(max-width:410px){.fields{grid-template-columns:1fr}.letter-page{scale:.46;margin-bottom:-500px}}
@media print{@page{size:A4;margin:0}body{background:white}.topbar,.container,.bottom,.toast,.overlay,.drawer-wrap{display:none!important}.print-stage{display:block!important;position:static;width:210mm;margin:0;padding:0}.print-stage .letter-page{width:210mm;height:297mm;max-height:297mm;overflow:hidden;box-shadow:none;transform:none;scale:1;margin:0;padding:15mm 16mm 13mm;font-size:10pt;line-height:1.25;break-after:page;page-break-after:always}.print-stage .letter-page:last-child{break-after:auto;page-break-after:auto}.print-stage .pu-logo{width:16mm;height:16mm}.print-stage .letter-headtext .ministry{font-size:16pt}.print-stage .letter-headtext .agency{font-size:14pt}.print-stage .letter-headtext .address{font-size:7.5pt}.print-stage .memo-title{font-size:11pt}.print-stage .signarea{font-size:9.5pt}.print-stage .approval{font-size:8pt}.print-stage .cc{font-size:9pt}}

/* Multi-audit extension */
.audit-tabs-row{display:flex;align-items:center;gap:9px;flex-wrap:wrap;margin-bottom:13px}.audit-tabs{display:flex;gap:7px;flex-wrap:wrap;flex:1}.audit-tab{border:1px solid #dbe3ee;background:#f9fbff;color:#40536d;border-radius:9px;padding:8px 11px;display:inline-flex;gap:8px;align-items:center;font-size:12px;font-weight:700;min-height:36px}.audit-tab.active{background:#eaf1ff;color:var(--blue);border-color:#8eaddb;box-shadow:0 0 0 1px #b4cae7}.audit-tab small{background:#e0e9f5;color:#415a7d;padding:2px 6px;border-radius:15px;font-size:10px}.audit-tab.active small{background:#fff}.audit-editor{background:#f9fbfe;border:1px solid #e1e8f1;border-radius:12px;padding:14px}.audit-editor-head{display:flex;align-items:flex-end;gap:12px;justify-content:space-between;flex-wrap:wrap;margin-bottom:6px}.audit-name{flex:1;min-width:245px}.audit-edit-actions{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:1px}.audit-usage{font-size:11px;color:#61728a;margin-bottom:12px}.audit-picker{border:1px solid #dbe2eb;border-radius:7px;max-width:208px;width:100%;min-width:160px;padding:7px 6px;background:white;font-size:11px;color:var(--ink)}.audit-cell small{display:block;margin-top:5px;color:var(--muted);font-size:10px}.audit-cell small.custom-label{color:#7558a9;font-weight:700}.bulk-select{border:1px solid #dce4ef;background:white;border-radius:8px;padding:6px 8px;max-width:190px;font-size:12px;min-height:31px}.group-distribution{margin-top:15px;padding-top:13px;border-top:1px solid var(--line);display:grid;gap:9px}.group-dist-row{display:flex;gap:6px;align-items:center;justify-content:space-between;color:#52627a;font-size:11px}.group-dist-row span{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px}.group-dist-row b{color:var(--ink)}.reference-override{padding:12px;border:1px solid #e1e7f1;background:#f7f9fd;border-radius:10px;display:grid;gap:9px}.reference-override-row{display:grid;grid-template-columns:18px 1fr 40px 30px;gap:7px;align-items:start}.reference-override-row textarea{width:100%;min-height:55px;border:1px solid #d9e0e9;border-radius:7px;padding:8px;resize:vertical;font-size:12px}.reference-override-row .refpunct{padding-top:9px}.override-toggle{display:flex;align-items:center;gap:10px;font-weight:650;color:#3b4d67}.override-toggle input{width:16px;height:16px}.group-preview-tag{display:block;font-size:10px;color:#5271a0;font-weight:650;margin-top:3px}.preview-link .group-preview-tag{max-width:215px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.audit-inline-msg{color:#a85030;font-size:11px;margin-top:4px}
@media(max-width:760px){.audit-editor-head{align-items:stretch}.audit-name{min-width:0;width:100%}.audit-edit-actions{width:100%}.bulk-select{max-width:145px}}

/* v3 — penerima toolbar: aksi di kiri, pencarian dan filter sejajar di kanan */
.tab-tools{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;align-items:center;padding:15px 19px}
.tab-tools .left-tools{display:flex;flex-wrap:wrap;gap:8px;align-items:center;min-width:0}
.tab-tools .right-tools{display:grid;grid-template-columns:minmax(230px,1fr) 162px;align-items:center;gap:9px;width:min(100%,445px);min-width:0;flex-wrap:nowrap}
.search-control,.filter-control{display:block;min-width:0;position:relative}
.search-control .searchbox,.filter-control .searchbox{width:100%;max-width:none;height:36px;min-height:36px}
.search-control .searchbox{padding-left:33px}
.search-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:19px;color:#7183a0;pointer-events:none;line-height:1}
.filter-control .searchbox{padding-left:11px;cursor:pointer}
.visually-hidden{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(max-width:1050px){.tab-tools{grid-template-columns:1fr;gap:12px}.tab-tools .right-tools{width:100%;grid-template-columns:minmax(0,1fr) 180px}}
@media(max-width:510px){.tab-tools .right-tools{grid-template-columns:minmax(0,1fr) 132px;gap:7px}.tab-tools .left-tools{gap:6px}.tab-tools .left-tools .btn{font-size:11px}}
@media(max-width:375px){.tab-tools .right-tools{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="topbar">
  <div class="brand"><div class="brandmark">L<span style="color:#95baff">.</span></div><div><b>Generator Surat Teguran</b><small>Pembuatan dokumen Word secara massal</small></div></div>
 <div class="top-right"><span class="offline">● MODE LOKAL</span><button class="btn ghost" id="settingsOpen">⚙ <span class="hide-mobile">Pengaturan</span></button></div>
</header>
<main class="container">
 <div class="heading"><div><div class="eyebrow">Buat Surat</div><h1>Buat banyak surat dengan lebih cepat</h1><p>Isi informasi yang sama satu kali, lengkapi data setiap penerima, kemudian buat dokumen Word sekaligus.</p></div><div class="heading-actions"><span class="pill">Memorandum Teguran Tertulis</span><button class="btn" id="fillExamples">✦ Tampilkan 30 contoh</button></div></div>
 <div class="grid-main">
  <div class="maincol">
   <section class="card" id="commonCard"><div class="cardhead"><div><h2>01 — Informasi yang berlaku untuk semua surat</h2><p>Informasi ini digunakan pada seluruh surat, kecuali data yang ditentukan secara khusus untuk penerima tertentu.</p></div><span class="pill">Cukup diisi sekali</span></div><div class="cardbody">
     <div class="fields">
       <div class="field half"><label for="batchName">Nama kumpulan surat</label><input id="batchName" placeholder="Contoh: Surat Teguran Audit Maluku – November 2026"></div>
       <div class="field"><label for="letterDate">Tanggal surat</label><input id="letterDate" type="date"></div>
       <div class="field"><label for="letterSubject">Perihal surat</label><input id="letterSubject" value="Teguran Tertulis"></div>
       <div class="field half"><label for="sender">Jabatan pengirim</label><input id="sender" value="Plt. Direktur Jenderal Prasarana Strategis"></div>
     </div>
     <div class="sep"></div>
     <div class="subrow"><div><strong>Kelompok referensi audit</strong><div class="mini-muted">Buat kelompok referensi sesuai kebutuhan. Setiap penerima dapat menggunakan kelompok yang berbeda. Tanda baca akhir ditambahkan secara otomatis.</div></div><span id="groupCount">0 kelompok</span></div>
     <div class="audit-tabs-row"><div class="audit-tabs" id="auditGroupTabs" role="tablist" aria-label="Kelompok audit"></div><button class="btn sm" id="addAuditGroup">＋ Tambah kelompok</button></div>
     <div class="audit-editor"><div class="audit-editor-head"><div class="field audit-name"><label for="auditGroupName">Nama kelompok audit</label><input id="auditGroupName" placeholder="Contoh: Audit Maluku"></div><div class="audit-edit-actions"><button class="btn sm" id="saveAuditGroups">💾 Simpan kelompok</button><button class="btn sm" id="duplicateAuditGroup">⧉ Salin kelompok</button><button class="btn sm danger" id="removeAuditGroup">⌫ Hapus</button></div></div>
     <div class="audit-usage" id="auditGroupUsage">Belum digunakan oleh penerima</div>
     <div class="reference-list" id="referenceList"></div><div class="reftools"><button class="btn sm" id="addRef">＋ Tambah referensi</button><span class="mini-muted" id="refAmount">0 referensi</span><span class="mini-muted">Tanda baca akhir diatur otomatis. Pastikan isi referensi sudah benar sebelum membuat surat resmi.</span></div></div>
   </div></section>
   <section class="card"><div class="cardhead"><div><h2>02 — Daftar penerima surat <span class="pill" id="recipientAmount">0 penerima</span></h2><p>Tentukan kelompok audit setiap penerima. Jika diperlukan, atur referensi khusus untuk surat tertentu.</p></div><button class="btn sm" id="addRowTop">＋ Tambah baris</button></div>
    <div class="tab-tools">
     <div class="left-tools" aria-label="Alat impor data penerima">
      <button class="btn sm primary" id="pasteOpen">▦ Tempel data Excel</button>
      <label class="btn sm" for="importCsv" style="cursor:pointer">↥ Impor berkas CSV/TSV</label><input type="file" id="importCsv" accept=".csv,.tsv,.txt,text/csv,text/tab-separated-values" class="hidden">
      <button class="btn sm" id="downloadTemplate">↓ Unduh format CSV</button>
     </div>
     <div class="right-tools" role="search" aria-label="Pencarian dan filter penerima">
      <label class="search-control" for="search"><span class="search-icon" aria-hidden="true">⌕</span><input class="searchbox" id="search" type="search" placeholder="Cari nama, NIP, atau jabatan..." aria-label="Cari penerima surat"></label>
      <label class="filter-control" for="filter"><span class="visually-hidden">Filter berdasarkan status</span><select id="filter" class="searchbox" aria-label="Filter status penerima"><option value="all">Semua status</option><option value="ok">Data lengkap</option><option value="bad">Perlu diperbaiki</option></select></label>
     </div>
    </div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th style="width:32px"><input type="checkbox" id="selectAll" aria-label="Pilih semua"></th><th style="width:33px">No.</th><th style="min-width:156px">Nama dan NIP</th><th style="min-width:110px">Pangkat/Golongan</th><th style="min-width:160px">Jabatan</th><th style="min-width:185px">Kelompok audit</th><th style="min-width:200px">Alasan teguran</th><th style="min-width:142px">Nomor memorandum</th><th style="min-width:85px">Status</th><th style="min-width:85px">Tindakan</th></tr></thead><tbody id="recipientRows"></tbody></table></div>
    <div class="tablefoot"><div class="left-tools"><select class="bulk-select" id="bulkAuditGroup" aria-label="Pilih kelompok audit untuk baris terpilih"></select><button class="btn sm" id="applyBulkAudit">↳ Terapkan kelompok</button><button class="btn sm" id="duplicateSelected">⧉ Salin baris terpilih</button><button class="btn sm danger" id="removeSelected">⌫ Hapus baris terpilih</button></div><span class="hint" id="rowHint">Petunjuk: pilih tombol Ubah untuk mengedit data penerima.</span></div>
   </section>
  </div>
   <aside class="side"><div class="card"><div class="summary"><h3>Ringkasan pembuatan surat</h3><p>Kelengkapan data diperiksa sebelum surat dibuat.</p><div class="summary-big"><div class="sumstat"><b id="sumTotal">0</b><span>Total surat</span></div><div class="sumstat"><b id="sumReady" style="color:var(--green)">0</b><span>Data lengkap</span></div></div><div class="progress"><span id="progressBar"></span></div><div class="stack" style="margin-top:14px"><div class="summaryline"><span>Perlu perbaikan</span><b id="sumError" style="color:var(--orange)">0 surat</b></div><div class="summaryline"><span>Kelompok audit</span><b id="sumRefs">0 kelompok</b></div><div class="summaryline"><span>Perkiraan hasil</span><b id="sumOutput">0 dokumen Word • 1 ZIP</b></div><div class="summaryline"><span>Hasil unduhan</span><b>1 berkas ZIP</b></div></div><div class="group-distribution" id="groupDistribution"></div><div class="side-actions"><button class="btn" id="previewFirst">◉ Lihat surat</button><button class="btn primary" id="simulateGenerate">▣ Buat dokumen Word (ZIP)</button></div></div></div><div class="side-note"><h3>✦ Cara membuat surat dengan cepat</h3><p><b>1.</b> Isi informasi bersama satu kali.<br><b>2.</b> Tempel data penerima dari Excel atau CSV/TSV.<br><b>3.</b> Perbaiki data penerima yang ditandai.<br><b>4.</b> Periksa pratinjau, lalu buat dokumen Word.<br><br>Setiap penerima menghasilkan <b>1 berkas DOCX</b> (dua versi memorandum: dengan tabel paraf dan tanpa). Seluruh hasil diunduh sebagai <b>1 berkas ZIP</b>. Pratinjau bersifat visual; jumlah halaman dan tata letak akhir tetap perlu diperiksa di <b>Microsoft Word</b>.</p></div></aside>
 </div>
</main>
 <div class="bottom"><div class="bottom-inner"><div><b id="bottomStatus">Memuat data...</b><small>Data penerima tidak disimpan otomatis (tanpa draft). Kelompok audit dan pengaturan dapat disimpan untuk digunakan kembali.</small></div><div class="bottom-actions"><button class="btn" id="bottomPreview">◉ Lihat surat</button><button class="btn primary" id="bottomGenerate">▣ Buat dokumen Word (ZIP)</button></div></div></div>
<div id="modalRoot"></div><div class="print-stage" id="printStage"></div><div class="toast" id="toast" role="status"></div>
<script>
window.GENERATOR_BOOT = @json($generatorBoot ?? ['settings'=>[], 'groups'=>[], 'use_default_samples'=>true]);
(()=>{'use strict';
const SAMPLE_REFS=[
'Instruksi Tindak Lanjut atas Laporan Hasil Audit Kinerja Berbasis Risiko Level Program-Optimalisasi Pelaksanaan Pengentasan Kemiskinan dan Penghapusan Kemiskinan Ekstrem (Pembangunan Sekolah Rakyat) pada Satuan Kerja Pelaksanaan Prasarana Strategis Maluku',
'Nota Dinas Inspektur Jenderal Kementerian Pekerjaan Umum Nomor: PW0103/R/lj/2026/2317 Tanggal 7 Oktober 2026 Hal Laporan Hasil Audit Kinerja Berbasis Risiko Level Program-Optimalisasi Pelaksanaan Pengentasan Kemiskinan dan Penghapusan Kemiskinan Ekstrem (Pembangunan Sekolah Rakyat) pada Satuan Kerja Pelaksanaan Prasarana Strategis Maluku',
'Memorandum Plt. Direktur Jenderal Prasarana Strategis Nomor PW0103/R/Ds/2026/14 Hal Instruksi Tindak Lanjut atas Laporan Hasil Audit Kinerja Berbasis Risiko Level Program-Optimalisasi Pelaksanaan Pengentasan Kemiskinan dan Penghapusan Kemiskinan Ekstrem (Pembangunan Sekolah Rakyat) pada Satuan Kerja Pelaksanaan Prasarana Strategis Maluku'];
// Referensi contoh mengikuti beberapa materi dalam Teguran.docx; jangan gunakan tanpa pemeriksaan administrasi.
const SAMPLE_REFS_KALSEL=[
 'Instruksi Tindak Lanjut atas Laporan Hasil Audit Kinerja Berbasis Risiko Level Program-Optimalisasi Pelaksanaan Pengentasan Kemiskinan dan Penghapusan Kemiskinan Ekstrem (Pembangunan Sekolah Rakyat) pada Satker Pelaksanaan Prasarana Strategis Kalimantan Selatan',
 'Nota Dinas Inspektur Jenderal Kementerian Pekerjaan Umum Nomor: PW0103/R/15/2026/2316 Tanggal 7 Oktober 2026 Hal Laporan Hasil Audit Kinerja Berbasis Risiko Level Program-Optimalisasi Pelaksanaan Pengentasan Kemiskinan dan Penghapusan Kemiskinan Ekstrem (Pembangunan Sekolah Rakyat) pada Satker Pelaksanaan Prasarana Strategis Kalimantan Selatan'];
const SAMPLE_REFS_KALTARA=[
 'Instruksi Tindak Lanjut atas Laporan Hasil Audit Dengan Tujuan Tertentu Pemberian Rekomendasi Sanksi Daftar Hitam pada Pelaksanaan Paket Pekerjaan Rehabilitasi dan Renovasi Madrasah PHTC Kalimantan Utara 1',
 'Nota Dinas Inspektur Jenderal Kementerian Pekerjaan Umum Nomor: PW0101/R/lj/2026/1241 Tanggal 3 Juli 2026 Hal Laporan Hasil Audit Dengan Tujuan Tertentu-Pemberian Rekomendasi Sanksi Daftar Hitam pada Pelaksanaan Paket Pekerjaan Rehabilitasi dan Renovasi Madrasah PHTC Kalimantan Utara 1',
 'Memorandum Plt. Direktur Jenderal Prasarana Strategis Nomor PW0101/R/Ds/2026/05 Hal Instruksi Tindak Lanjut atas Laporan Hasil Audit Dengan Tujuan Tertentu Pemberian Rekomendasi Sanksi Daftar Hitam pada Pelaksanaan Paket Pekerjaan Rehabilitasi dan Renovasi Madrasah PHTC Kalimantan Utara 1'];
const sampleGroups=()=>[
 {id:'g1',name:'Audit Maluku',references:[...SAMPLE_REFS]},
 {id:'g2',name:'Audit Kalimantan Selatan',references:[...SAMPLE_REFS_KALSEL]},
 {id:'g3',name:'Audit Kalimantan Utara',references:[...SAMPLE_REFS_KALTARA]}
];
const BOOT=(window.GENERATOR_BOOT||{});
const defaultSettings=()=>({ministry:'KEMENTERIAN PEKERJAAN UMUM',agency:'DIREKTORAT JENDERAL PRASARANA STRATEGIS',address:'Jalan Pattimura No. 20, Selong, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12110',signTitle:'Plt. Direktur Jenderal Prasarana Strategis',signName:'Kuswara',cc:'Plt. Sekretaris Direktorat Jenderal Prasarana Strategis\nDirektur Kepatuhan Intern, Direktorat Jenderal Prasarana Strategis',sender:'Plt. Direktur Jenderal Prasarana Strategis',subject:'Teguran Tertulis'});
function bootSettings(){const d=defaultSettings(),b=(BOOT.settings||{});const out={...d};for(const k of Object.keys(out)){if(typeof b[k]==='string')out[k]=b[k];}return out;}
function bootGroups(){const bg=Array.isArray(BOOT.groups)?BOOT.groups:[];if(BOOT.use_default_samples||!bg.length)return sampleGroups();return bg.map((g,i)=>({id:String(g.id||('g'+(i+1))),name:String(g.name||''),references:Array.isArray(g.references)&&g.references.length?g.references.map(x=>String(x??'')):['']}));}
const initialGroups=bootGroups;
let nextGroupId=(function(){let m=0;for(const g of initialGroups()){const n=parseInt(String(g.id).replace(/^g/,''),10);if(!isNaN(n)&&n>m)m=n;}return m+1;})();
const EXAMPLE_ROLES=['Kepala Satuan Kerja Pelaksanaan Prasarana Strategis Maluku','PPK Pelaksanaan Prasarana Strategis Maluku','Ketua Tim Panitia Peneliti Kontrak','PPSPM Pelaksanaan Prasarana Strategis Maluku'];
const EXAMPLE_FINDINGS=['Kelemahan pengendalian dan pengawasan pelaksanaan pekerjaan kontraktual fisik, pekerjaan MK, kegiatan administrasi umum, dan pengelolaan lingkungan','Ketidakcermatan dan kelemahan pengendalian pelaksanaan kontrak, pengawasan pekerjaan, pengelolaan keuangan, dan pengelolaan lingkungan','Ketidakcermatan dalam penelitian kelayakan perubahan kontrak','Ketidakcermatan dalam pengujian dokumen pertanggungjawaban keuangan dan pembayaran belanja sesuai ketentuan SBM'];
const LOGO='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJgAAACYCAMAAAAvHNATAAAANlBMVEX9uBMfM2iVejorOmPZoiBmXkz8txO7kCw9RVw8RFx0ZkdOT1ViW001QF9TUlOyiy55aUUtO2Lmhk4RAAAB8UlEQVR4nO3b2XLDIAwFUNym+/7/P9vpMlPbSMAFYanJvY8ZJA48OE6MU9KydEdtaRPCCCOMsOnCo2Aw7kgYZDsY1k47HJbSVVRY2665wFpoTrA6zQ1Wk/nBKjJHWJnmCivJfGEFmTNMl3nDVJm3S5V5swLDFJm36ithYaLM2/SdsDBJ5k36DWFwCINDGJqwsL3Mm/OXsLDrqLBEGJqwsEQYGsLQEIaGMDSnqLDNlt14Y9aJCjtFhS2EgfkfsEjXC8LQEAZmIQwMYWAWwrDsXYTVEhWWuQgrJ3cRVozgCgGTXIQVIroCwGQXYajLHaa5vGGqi0fuUJcrrORyhN0WXTw6jLqcYFUWz82jLL6bAaqOhd0BrAt+Y+oeFlnCeicnjLBGD2GEEUZYm2sObEBG2LnAVi3aZ3v4qX2cKUNhpQ6WsHWHgdXbywBYcyML2KbBAKtImwnDu43BtvUGS7aStcG6O3bLdtUGLoU21kKGPYFNDWRZscFaLWR5rZFrUCaUWrmGZFJl9slzL6z/oapYZ7Zh/TK5zNDVd+nRigxdPTKlIoO9jMHQLzh1eAYbdEG3aKWxe9ioq/Ex02tt1ATYW8OcTdnenBkkLOx9AszCZbZlFwQzkq1/+xLmBLORrf73IYywfwMzkcWHfUSFGbrOG/YJxNUamMAnpW0AAAAASUVORK5CYII=';
const initialCommon=()=>{const today=new Date().toISOString().slice(0,10);const st=bootSettings();const useDef=!!(BOOT.use_default_samples||!(Array.isArray(BOOT.groups)&&BOOT.groups.length));return {batchName:useDef?'Teguran Audit — Contoh':'',letterDate:today,subject:st.subject||'Teguran Tertulis',sender:st.sender||st.signTitle||'Plt. Direktur Jenderal Prasarana Strategis'};};
const initialSettings=bootSettings;
let nextId=1;
const makeExample=(i,groupId)=>({id:nextId++,number:`CONTOH-${String(i+1).padStart(3,'0')}`,name:`Pegawai Contoh ${String(i+1).padStart(2,'0')}`,nip:`${String(100000000000000001n+BigInt(i))}`,rank:['IV/a (Pembina)','III/c (Penata)','III/a (Penata Muda)','III/b (Penata Muda Tingkat I)'][i%4],job:EXAMPLE_ROLES[i%4],greeting:i%7===0?'Saudari':'Saudara',finding:EXAMPLE_FINDINGS[i%4],auditGroupId:groupId||'g1',referenceMode:'group',customReferences:[],requestedGroupName:''});
const _bootGroups=initialGroups();
const _useDefaultSamples=!!(BOOT.use_default_samples||!(Array.isArray(BOOT.groups)&&BOOT.groups.length));
const S={common:initialCommon(),settings:initialSettings(),groups:_bootGroups,activeGroupId:(_bootGroups[0]&&_bootGroups[0].id)||'g1',rows:_useDefaultSamples?[makeExample(0,(_bootGroups[0]||{}).id||'g1'),makeExample(1,(_bootGroups[0]||{}).id||'g1'),makeExample(2,(_bootGroups[1]||_bootGroups[0]||{}).id||'g1'),makeExample(3,(_bootGroups[1]||_bootGroups[0]||{}).id||'g1'),makeExample(4,(_bootGroups[2]||_bootGroups[0]||{}).id||'g1')]:[],selected:new Set(),search:'',filter:'all',previewId:null,page:1,dirty:false};
const $=id=>document.getElementById(id);
const esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const punctuation=(i,len)=>i===len-1?'.':i===len-2?'; dan':';';
const refClean=t=>String(t||'').trim().replace(/(?:\s*;\s*dan\s*|[;.\s])+$/gi,'').trim();
const activeGroup=()=>S.groups.find(g=>g.id===S.activeGroupId)||S.groups[0];
const groupForRow=r=>S.groups.find(g=>g.id===r.auditGroupId);
const effectiveRefInputs=r=>r.referenceMode==='custom'?(r.customReferences||[]):(groupForRow(r)?.references||[]);
const safeRefsFor=r=>effectiveRefInputs(r).map(refClean).filter(Boolean);
const groupLabel=r=>r.referenceMode==='custom'?'Referensi khusus':(groupForRow(r)?.name||r.requestedGroupName||'Belum dipilih');
const normalizedGroup=v=>String(v||'').toLowerCase().trim().replace(/\s+/g,' ');
const resolveGroupId=name=>S.groups.find(g=>g.id===name||normalizedGroup(g.name)===normalizedGroup(name))?.id||'';
const groupSelectorOptions=(selected,allowSpecial=true)=>S.groups.map(g=>`<option value="${esc(g.id)}" ${selected===g.id?'selected':''}>${esc(g.name)}</option>`).join('')+(allowSpecial?`<option value="__custom" ${selected==='__custom'?'selected':''}>✎ Referensi khusus</option>`:'');
const ccList=()=>S.settings.cc.split(/\n/).map(t=>t.trim()).filter(Boolean);
function rowErrors(r){
 const e=[];
 if(!r.name.trim())e.push('Nama pegawai belum diisi');
 if(!/^\d{18}$/.test(r.nip.trim()))e.push('NIP harus terdiri atas 18 angka');
 if(!r.rank.trim())e.push('Pangkat dan golongan belum diisi');
 if(!r.job.trim())e.push('Jabatan belum diisi');
 if(!r.finding.trim())e.push('Uraian teguran belum diisi');
 if(!r.number.trim())e.push('Nomor memorandum belum diisi');
 if(!['Saudara','Saudari'].includes(r.greeting))e.push('Sapaan belum dipilih');
 if(!groupForRow(r))e.push(`Kelompok audit tidak ditemukan${r.requestedGroupName?' ('+r.requestedGroupName+')':''}`);
 const refs=effectiveRefInputs(r);
 if(!refs.length||!refs.some(x=>refClean(x)))e.push('Referensi audit belum diisi');
 else if(refs.some(x=>!refClean(x)))e.push('Masih ada referensi audit yang kosong');
 const duplicates=S.rows.filter(x=>x.number.trim()&&x.number.trim()===r.number.trim());
 if(duplicates.length>1)e.push('Nomor memorandum digunakan oleh lebih dari satu penerima');
 return e;
}
function commonErrors(){
 const e=[];
 if(!S.common.letterDate)e.push('Tanggal surat belum diisi');
 if(!S.common.sender.trim())e.push('Pengirim belum diisi');
 if(!S.common.subject.trim())e.push('Perihal surat belum diisi');
 if(!S.settings.signName.trim())e.push('Penandatangan belum diisi');
 const names=S.groups.map(g=>normalizedGroup(g.name));
 if(names.some(n=>!n))e.push('Nama kelompok audit tidak boleh kosong');
 if(new Set(names).size!==names.length)e.push('Nama setiap kelompok audit harus berbeda');
 return e;
}
function markDirty(){S.dirty=true;}
function toast(msg){const t=$('toast');t.textContent=msg;t.classList.add('show');clearTimeout(toast._timer);toast._timer=setTimeout(()=>t.classList.remove('show'),3400)}
function renderReferences(){
 const g=activeGroup(); if(!g)return;
 const used=S.rows.filter(r=>r.auditGroupId===g.id).length;
 $('groupCount').textContent=`${S.groups.length} kelompok`;
 $('auditGroupTabs').innerHTML=S.groups.map(group=>{
  const count=S.rows.filter(r=>r.auditGroupId===group.id).length;
  return `<button type="button" role="tab" aria-selected="${group.id===g.id}" class="audit-tab ${group.id===g.id?'active':''}" data-group-tab="${esc(group.id)}">${esc(group.name||'Tanpa nama')} <small>${count}</small></button>`;
 }).join('');
 $('auditGroupTabs').querySelectorAll('[data-group-tab]').forEach(b=>b.addEventListener('click',()=>{S.activeGroupId=b.dataset.groupTab;renderReferences();renderRows();renderSummary()}));
 $('auditGroupName').value=g.name;
 $('auditGroupUsage').textContent=`Digunakan oleh ${used} penerima dalam kumpulan surat ini · ${g.references.length} referensi. Perubahan kelompok otomatis diterapkan pada surat yang menggunakan kelompok ini, kecuali surat dengan referensi khusus.`;
 $('removeAuditGroup').disabled=S.groups.length===1||used>0;
 $('removeAuditGroup').title=used?'Kelompok sedang digunakan oleh penerima; pindahkan dahulu semua penerima.':'';
 $('referenceList').innerHTML=g.references.map((r,i)=>`<div class="reference-row"><span class="refn">${i+1}.</span><textarea data-ref="${i}" rows="2" aria-label="Referensi ${i+1} untuk ${esc(g.name)}" placeholder="Tulis referensi ${i+1}...">${esc(r)}</textarea><span class="refpunct">${esc(punctuation(i,g.references.length))}</span><button type="button" class="btn sm ghost" data-remove-ref="${i}" aria-label="Hapus referensi ${i+1}">×</button></div>`).join('');
 $('refAmount').textContent=`${g.references.filter(v=>refClean(v)).length} referensi`;
 $('referenceList').querySelectorAll('[data-ref]').forEach(t=>t.addEventListener('input',e=>{
  g.references[+e.target.dataset.ref]=e.target.value;markDirty();$('refAmount').textContent=`${g.references.filter(v=>refClean(v)).length} referensi`;renderRows();renderSummary();
 }));
 $('referenceList').querySelectorAll('[data-remove-ref]').forEach(b=>b.addEventListener('click',()=>{
  if(g.references.length===1)g.references[0]='';else g.references.splice(+b.dataset.removeRef,1);
  markDirty();renderReferences();renderRows();renderSummary();
 }));
 renderBulkAuditOptions();
}
function renderBulkAuditOptions(){
 const sel=$('bulkAuditGroup');const prev=sel.value;
 sel.innerHTML=S.groups.map(g=>`<option value="${esc(g.id)}">${esc(g.name)}</option>`).join('');
 sel.value=S.groups.some(g=>g.id===prev)?prev:S.activeGroupId;
}
function addAuditGroup(){
 const id='g'+nextGroupId++;
 S.groups.push({id,name:`Kelompok Audit ${nextGroupId-1}`,references:['']});
 S.activeGroupId=id;markDirty();renderReferences();renderRows();renderSummary();$('auditGroupName').focus();$('auditGroupName').select();
}
function duplicateAuditGroup(){
 const g=activeGroup();if(!g)return;const id='g'+nextGroupId++;
 S.groups.push({id,name:g.name+' (Salinan)',references:[...g.references]});
 S.activeGroupId=id;markDirty();renderReferences();renderRows();renderSummary();toast('Salinan kelompok berhasil dibuat. Periksa dan sesuaikan namanya.');
}
function removeAuditGroup(){
 const g=activeGroup();if(!g)return;
 if(S.groups.length===1||S.rows.some(r=>r.auditGroupId===g.id)){toast('Kelompok ini masih digunakan. Pindahkan seluruh penerima ke kelompok lain terlebih dahulu.');return;}
 if(!confirm(`Hapus kelompok "${g.name}"?`))return;
 S.groups=S.groups.filter(x=>x.id!==g.id);S.activeGroupId=S.groups[0].id;markDirty();renderReferences();renderRows();renderSummary();
}
function renderRows(){
 let rows=S.rows;
 const search=S.search.trim().toLowerCase();
 if(search)rows=rows.filter(r=>[r.name,r.nip,r.job,r.rank,r.finding,r.number,groupLabel(r)].some(v=>String(v).toLowerCase().includes(search)));
 if(S.filter!=='all')rows=rows.filter(r=>(rowErrors(r).length?'bad':'ok')===S.filter);
 $('recipientRows').innerHTML=rows.length?rows.map(r=>{
  const i=S.rows.indexOf(r)+1;const errs=rowErrors(r);
  const sel=r.referenceMode==='custom'?'__custom':r.auditGroupId;
  const choices=`<option value="" ${!sel?'selected':''} disabled>Pilih kelompok audit</option>${groupSelectorOptions(sel)}`;
  return `<tr class="${S.selected.has(r.id)?'selected':''}">
   <td><input type="checkbox" data-select="${r.id}" ${S.selected.has(r.id)?'checked':''} aria-label="Pilih ${esc(r.name)}"></td>
   <td style="color:var(--muted)">${i}</td>
   <td class="name-cell"><strong>${esc(r.name||'Nama pegawai belum diisi')}</strong><span>${esc(r.nip||'NIP belum diisi')}</span></td>
   <td><span class="one-line">${esc(r.rank||'—')}</span></td>
   <td><div class="two-line">${esc(r.job||'—')}</div></td>
   <td class="audit-cell"><select class="audit-picker" data-row-group="${r.id}" aria-label="Kelompok audit ${esc(r.name)}">${choices}</select><small class="${r.referenceMode==='custom'?'custom-label':''}">${r.referenceMode==='custom'?'Referensi khusus penerima':(r.requestedGroupName?`Hasil impor: ${esc(r.requestedGroupName)}`:`${effectiveRefInputs(r).filter(refClean).length} referensi`)}</small></td>
   <td><div class="two-line">${esc(r.finding||'—')}</div></td>
   <td><div class="one-line" title="${esc(r.number)}">${esc(r.number||'—')}</div></td>
   <td><span title="${esc(errs.join('; '))}" class="status ${errs.length?'bad':'ok'}">${errs.length?'! Perlu diperbaiki':'✓ Lengkap'}</span></td>
   <td><button type="button" class="btn sm" data-edit="${r.id}">✎ Ubah</button></td></tr>`;
 }).join(''):`<tr><td colspan="10" style="text-align:center;padding:40px;color:#64748b">Tidak ditemukan penerima sesuai pencarian atau status yang dipilih. Ubah kriteria pencarian atau tambahkan penerima.</td></tr>`;
 $('recipientAmount').textContent=`${S.rows.length} penerima`;
 $('rowHint').textContent=`Menampilkan ${rows.length} dari ${S.rows.length} penerima • ${S.selected.size} baris dipilih`;
 $('recipientRows').querySelectorAll('[data-select]').forEach(i=>i.addEventListener('change',e=>{
  const id=+e.target.dataset.select;e.target.checked?S.selected.add(id):S.selected.delete(id);renderRows();
 }));
 $('recipientRows').querySelectorAll('[data-edit]').forEach(b=>b.addEventListener('click',()=>openEdit(+b.dataset.edit)));
 $('recipientRows').querySelectorAll('[data-row-group]').forEach(sel=>sel.addEventListener('change',()=>{
  const r=S.rows.find(x=>x.id===+sel.dataset.rowGroup);if(!r)return;
  if(sel.value==='__custom'){
   renderRows();openEdit(r.id,true);
  }else{
   r.auditGroupId=sel.value;r.referenceMode='group';r.requestedGroupName='';markDirty();renderRows();renderSummary();renderReferences();
  }
 }));
 $('selectAll').checked=!!rows.length&&rows.every(r=>S.selected.has(r.id));
}
function renderSummary(){
 const total=S.rows.length;const ready=S.rows.filter(r=>!rowErrors(r).length).length;
 const bad=total-ready;const common=commonErrors();
 $('sumTotal').textContent=total;$('sumReady').textContent=ready;$('sumError').textContent=`${bad} surat`;
 $('sumRefs').textContent=`${S.groups.length} kelompok`;
 $('sumOutput').textContent=`${total} dokumen Word • 1 ZIP`;
 $('progressBar').style.width=(total?(ready/total)*100:0)+'%';
 $('bottomStatus').textContent=total?`${ready}/${total} penerima dengan data lengkap • ${bad} perlu diperbaiki${common.length?' • '+common.length+' kekurangan pada informasi bersama':''}`:'Belum ada penerima yang ditambahkan';
 $('groupDistribution').innerHTML=S.groups.map(g=>`<div class="group-dist-row"><span title="${esc(g.name)}">${esc(g.name)}</span><b>${S.rows.filter(r=>r.auditGroupId===g.id).length} surat</b></div>`).join('');
}
function renderAll(){renderReferences();renderRows();renderSummary();}
function syncInputs(){const entries=[['batchName','batchName'],['letterDate','letterDate'],['letterSubject','subject'],['sender','sender']];for(const [dom,key] of entries){$(dom).value=S.common[key];}}
function attachInputs(){[['batchName','batchName'],['letterDate','letterDate'],['letterSubject','subject'],['sender','sender']].forEach(([dom,key])=>$(dom).addEventListener('input',e=>{S.common[key]=e.target.value;markDirty();renderSummary()}));}
function closeUI(){const node=$('activeUI');if(node)node.remove();document.body.style.overflow='';}
function showModal(title,subtitle,body,footer='',wide=false){closeUI();document.body.style.overflow='hidden';const el=document.createElement('div');el.id='activeUI';el.className='overlay';el.innerHTML=`<section class="modal ${wide?'wide':''}" role="dialog" aria-modal="true"><div class="modal-head"><div><h2>${title}</h2><p>${subtitle}</p></div><button class="close" data-close aria-label="Tutup">×</button></div><div class="modal-body">${body}</div>${footer?`<div class="modal-foot">${footer}</div>`:''}</section>`;$('modalRoot').append(el);el.addEventListener('click',e=>{if(e.target===el||e.target.closest('[data-close]'))closeUI()});return el;}
function showDrawer(title,subtitle,body,footer=''){closeUI();document.body.style.overflow='hidden';const el=document.createElement('div');el.id='activeUI';el.className='drawer-wrap';el.innerHTML=`<section class="drawer" role="dialog" aria-modal="true"><div class="modal-head"><div><h2>${title}</h2><p>${subtitle}</p></div><button class="close" data-close aria-label="Tutup">×</button></div><div class="modal-body">${body}</div><div class="modal-foot">${footer}</div></section>`;$('modalRoot').append(el);el.addEventListener('click',e=>{if(e.target===el||e.target.closest('[data-close]'))closeUI()});return el;}
function openEdit(id,startCustom=false){
 const r=S.rows.find(x=>x.id===id);if(!r)return;
 const draft=structuredClone(r);
 draft.customReferences=draft.customReferences||[];
 if(startCustom){draft.referenceMode='custom';if(!draft.customReferences.length)draft.customReferences=[...(groupForRow(draft)?.references||[''])];}
 const fields=[['number','Nomor memorandum'],['name','Nama pegawai'],['nip','NIP (18 angka)'],['rank','Pangkat / Golongan'],['job','Jabatan'],['greeting','Sapaan'],['finding','Atas / uraian teguran']];
 const fieldHtml=fields.map(([key,title])=>`<div class="field ${['job','finding'].includes(key)?'full':''}"><label for="edit_${key}">${title}</label>${key==='greeting'?`<select id="edit_${key}"><option value="Saudara" ${r.greeting==='Saudara'?'selected':''}>Saudara</option><option value="Saudari" ${r.greeting==='Saudari'?'selected':''}>Saudari</option></select>`:['finding','job'].includes(key)?`<textarea rows="${key==='finding'?4:2}" id="edit_${key}">${esc(r[key])}</textarea>`:`<input id="edit_${key}" value="${esc(r[key])}" ${key==='nip'?'inputmode="numeric" maxlength="18"':''}>`}</div>`).join('');
 const body=`<div class="notice" style="margin-bottom:16px">Pilih kelompok audit yang sesuai. Jika daftar referensi surat ini berbeda, centang <b>Gunakan referensi khusus</b>. Perubahan tersebut tidak memengaruhi surat lain.</div><form id="editForm"><div class="twocol">
 <div class="field full"><label for="edit_auditGroup">Kelompok audit</label><select id="edit_auditGroup">${groupSelectorOptions(draft.auditGroupId,false)}</select><div class="mini-muted">Surat menggunakan referensi dari kelompok yang dipilih.</div></div>
 ${fieldHtml}
 <div class="field full"><label class="override-toggle"><input id="edit_useCustom" type="checkbox" ${draft.referenceMode==='custom'?'checked':''}> Gunakan referensi khusus untuk pegawai ini</label><div class="mini-muted">Jika dipilih, daftar referensi khusus digunakan untuk surat ini saja.</div><div id="overrideBox"></div></div>
 </div><div class="notice warn" id="editErrors" style="margin-top:14px">${esc(rowErrors(r).length?rowErrors(r).join(' • '):'Seluruh data penerima telah diisi.')}</div></form>`;
 const el=showDrawer(`Ubah data surat — ${esc(r.name||'Penerima')}`,'Perubahan diterapkan pada surat penerima yang dipilih.',body,`<button class="btn" data-close>Batal</button><button class="btn primary" id="saveRecipient">✓ Simpan perubahan</button>`);
 el.querySelector('#edit_auditGroup').value=groupForRow(draft)?draft.auditGroupId:S.groups[0].id;
 const renderOverride=()=>{
  const box=el.querySelector('#overrideBox');const enabled=el.querySelector('#edit_useCustom').checked;
  box.innerHTML=enabled?`<div class="reference-override" style="margin-top:10px"><div class="mini-muted">Penomoran dan tanda baca referensi diatur otomatis.</div>${draft.customReferences.map((ref,i)=>`<div class="reference-override-row"><span class="refn">${i+1}.</span><textarea rows="2" data-custom-ref="${i}" placeholder="Referensi khusus ${i+1}">${esc(ref)}</textarea><span class="refpunct">${esc(punctuation(i,draft.customReferences.length))}</span><button type="button" class="btn sm ghost" data-remove-custom="${i}" aria-label="Hapus referensi khusus ${i+1}">×</button></div>`).join('')}<button type="button" class="btn sm" id="addCustomRef">＋ Tambah referensi khusus</button></div>`:'';
  if(!enabled)return;
  box.querySelectorAll('[data-custom-ref]').forEach(inp=>inp.addEventListener('input',e=>{draft.customReferences[+inp.dataset.customRef]=e.target.value}));
  box.querySelectorAll('[data-remove-custom]').forEach(b=>b.addEventListener('click',()=>{if(draft.customReferences.length===1)draft.customReferences[0]='';else draft.customReferences.splice(+b.dataset.removeCustom,1);renderOverride()}));
  box.querySelector('#addCustomRef').addEventListener('click',()=>{draft.customReferences.push('');renderOverride();[...box.querySelectorAll('[data-custom-ref]')].at(-1)?.focus()});
 };
 el.querySelector('#edit_useCustom').addEventListener('change',e=>{
  if(e.target.checked&&!draft.customReferences.length){const g=S.groups.find(g=>g.id===el.querySelector('#edit_auditGroup').value);draft.customReferences=g?[...g.references]:[''];}
  renderOverride();
 });
 el.querySelector('#edit_auditGroup').addEventListener('change',()=>{if(!el.querySelector('#edit_useCustom').checked)draft.customReferences=[];});
 renderOverride();
 el.querySelector('#saveRecipient').addEventListener('click',()=>{
  for(const [key] of fields)draft[key]=el.querySelector('#edit_'+key).value.trim();
  draft.auditGroupId=el.querySelector('#edit_auditGroup').value;
  draft.referenceMode=el.querySelector('#edit_useCustom').checked?'custom':'group';
  draft.requestedGroupName='';
  Object.assign(r,draft);markDirty();renderRows();renderReferences();renderSummary();closeUI();toast('Perubahan data penerima berhasil diterapkan.');
 });
}
function parseDelimited(text){const delimiter=(text.split(/\r?\n/)[0]||'').includes('\t')?'\t':',';const rows=[];let row=[],value='',quoted=false;for(let i=0;i<text.length;i++){const c=text[i];if(c==='"'){if(quoted&&text[i+1]==='"'){value+='"';i++;}else quoted=!quoted;}else if(c===delimiter&&!quoted){row.push(value);value='';}else if((c==='\r'||c==='\n')&&!quoted){if(c==='\r'&&text[i+1]==='\n')i++;row.push(value);if(row.some(v=>v.trim()))rows.push(row);row=[];value='';}else value+=c;}row.push(value);if(row.some(v=>v.trim()))rows.push(row);return rows;}
const normalizeHeader=s=>String(s).toLowerCase().trim().replace(/[\s\-/.()]+/g,'_').replace(/^_+|_+$/g,'');
const mapping={number:['nomor_surat','nomor_memorandum','no_surat','nomor'],name:['nama','nama_pegawai','pegawai'],nip:['nip'],rank:['pangkat_gol','pangkat_golongan','pangkat','golongan'],job:['jabatan'],greeting:['sapaan','panggilan'],finding:['atas','uraian_teguran','uraian','temuan','alasan_teguran'],auditGroup:['kelompok_audit','audit','audit_group','grup_audit']};
function interpretRows(raw){
 if(!raw.length)return[];
 const normalized=raw[0].map(normalizeHeader);
 const headerFound=normalized.some(h=>Object.values(mapping).some(aliases=>aliases.includes(h)))&&normalized.some(h=>mapping.name.includes(h));
 const defaultOrder=['number','name','nip','rank','job','greeting','finding','auditGroup'];
 const index={};
 if(headerFound){for(const [key,aliases] of Object.entries(mapping))index[key]=normalized.findIndex(h=>aliases.includes(h));}
 else defaultOrder.forEach((k,i)=>index[k]=i);
 return raw.slice(headerFound?1:0).filter(row=>row.some(x=>x.trim())).map(row=>{
  const get=k=>index[k]>=0?(row[index[k]]||'').trim():'';
  const importedGroup=get('auditGroup');const groupId=importedGroup?resolveGroupId(importedGroup):(S.activeGroupId||S.groups[0].id);
  return {id:nextId++,number:get('number'),name:get('name'),nip:get('nip'),rank:get('rank'),job:get('job'),greeting:get('greeting')||'Saudara',finding:get('finding'),auditGroupId:groupId,requestedGroupName:groupId?'':importedGroup,referenceMode:'group',customReferences:[]};
 });
}
let staged=[];
function openPaste(prefill=''){staged=[];const body=`<div class="notice">Salin tabel dari Excel, termasuk judul kolom jika tersedia, lalu tempel di sini. Jika tidak ada judul kolom, gunakan urutan: <b>nomor_surat, nama, nip, pangkat_gol, jabatan, sapaan, atas, kelompok_audit</b>. Buat kelompok audit terlebih dahulu. Pastikan nama kelompok pada Excel sama dengan nama yang tercantum pada halaman ini. Atur kolom NIP sebagai teks di Excel agar seluruh 18 angka tidak berubah.</div><div class="field" style="margin-top:16px"><label for="pasteArea">Data yang disalin dari Excel atau berkas CSV</label><textarea id="pasteArea" rows="9" placeholder="nomor_surat[TAB]nama[TAB]nip[TAB]pangkat_gol[TAB]jabatan[TAB]sapaan[TAB]atas[TAB]kelompok_audit"></textarea></div><div class="row" style="justify-content:space-between;margin-top:10px"><span id="pasteResult" class="mini-muted">Belum ada data yang ditempel.</span><button id="readPaste" class="btn sm">Periksa data</button></div><div id="pastePreview" style="margin-top:10px"></div>`;
 const el=showModal('Tempel data penerima','Masukkan banyak data penerima ke dalam tabel sekaligus.',body,`<button class="btn" data-close>Batal</button><button class="btn primary" id="applyPaste" disabled>＋ Masukkan ke tabel</button>`);
 if(prefill)el.querySelector('#pasteArea').value=prefill;
 const inspect=()=>{const raw=parseDelimited(el.querySelector('#pasteArea').value);staged=interpretRows(raw);el.querySelector('#pasteResult').textContent=`${staged.length} baris ditemukan.`;el.querySelector('#applyPaste').disabled=!staged.length;el.querySelector('#pastePreview').innerHTML=staged.length?`<div class="table-preview"><table><thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Kelompok audit</th><th>Nomor</th></tr></thead><tbody>${staged.slice(0,5).map(r=>`<tr><td>${esc(r.name)}</td><td>${esc(r.nip)}</td><td>${esc(r.job)}</td><td>${esc(groupForRow(r)?.name||r.requestedGroupName||'Tidak ditemukan')}</td><td>${esc(r.number)}</td></tr>`).join('')}</tbody></table></div><div class="mini-muted" style="margin-top:6px">Menampilkan ${Math.min(5,staged.length)} dari ${staged.length} baris.</div>`:'';};
 el.querySelector('#readPaste').addEventListener('click',inspect);el.querySelector('#pasteArea').addEventListener('input',()=>{staged=[];el.querySelector('#applyPaste').disabled=true;el.querySelector('#pasteResult').textContent='Data telah diubah. Pilih Periksa data untuk meninjau hasilnya.'});el.querySelector('#applyPaste').addEventListener('click',()=>{S.rows.push(...staged);markDirty();renderRows();renderSummary();closeUI();toast(`${staged.length} penerima ditambahkan.`)});if(prefill)inspect();}
function downloadText(name,content,type='text/plain;charset=utf-8'){const a=document.createElement('a');const url=URL.createObjectURL(new Blob([content],{type}));a.href=url;a.download=name;document.body.append(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(url),3000)}
function downloadCsvTemplate(){const template='nomor_surat,nama,nip,pangkat_gol,jabatan,sapaan,atas,kelompok_audit\r\n"CONTOH-001","Nama Contoh","123456789012345678","III/c (Penata)","PPK Pelaksanaan Prasarana Strategis Maluku","Saudara","Uraian teguran contoh","Audit Maluku"\r\n';downloadText('template_penerima_surat.csv','\ufeff'+template,'text/csv;charset=utf-8');toast('Berkas contoh CSV berhasil berhasil diunduh. Ganti nomor dan identitas contoh sebelum digunakan.')}
function commonDataSummary(){const common=commonErrors();const problems=S.rows.flatMap(r=>rowErrors(r).map(e=>({name:r.name||'(tanpa nama)',error:e})));return {common,problems};}
function makeDocPage(r,withApproval){const c=S.common,st=S.settings,refs=safeRefsFor(r);const refHTML=refs.map((ref,i)=>`<li>${esc(ref)}${esc(punctuation(i,refs.length))}</li>`).join('');const cc=ccList().map(x=>`<li>${esc(x)}</li>`).join('');return `<article class="letter-page"><header class="letter-head"><img class="pu-logo" src="${LOGO}" alt="Logo PU"><div class="letter-headtext"><div class="ministry">${esc(st.ministry)}</div><div class="agency">${esc(st.agency)}</div><div class="address">${esc(st.address)}</div></div></header><div class="memo-title">MEMORANDUM<br>NOMOR: ${esc(r.number||'________________')}</div><div class="memo-meta"><span>Yth</span><span>:</span><span>${esc(r.name)}</span><span>Dari</span><span>:</span><span>${esc(c.sender)}</span><span>Hal</span><span>:</span><span>${esc(c.subject)}</span><span>Tanggal</span><span>:</span><span>${esc(formatDate(c.letterDate))}</span></div><div class="memo-rule"></div><div class="memo-ref"><p>Menindaklanjuti:</p><ol>${refHTML}</ol></div><div class="memo-content"><p>Bersama ini disampaikan hal-hal sebagai berikut:</p><ol><li>Sesuai Laporan Hasil Pemeriksaan Inspektorat Jenderal, dengan ini kami memberikan <b>Teguran Tertulis</b>, kepada:<table class="id-table"><tr><td>Nama</td><td>:</td><td>${esc(r.name)}</td></tr><tr><td>NIP</td><td>:</td><td>${esc(r.nip)}</td></tr><tr><td>Pangkat/ Gol</td><td>:</td><td>${esc(r.rank)}</td></tr><tr><td>Jabatan</td><td>:</td><td>${esc(r.job)}</td></tr><tr><td>Atas</td><td>:</td><td>${esc(r.finding)}</td></tr></table></li><li>Untuk selanjutnya, kami instruksikan ${esc(r.greeting)} agar lebih cermat dalam melakukan pengawasan dan pengendalian pada perencanaan dan pelaksanaan pekerjaan.</li></ol><div class="memo-bottom"><p>Atas perhatiannya kami ucapkan terima kasih.</p><div class="signzone"><div>${withApproval?'<table class="approval"><thead><tr><th colspan="3">Paraf</th></tr><tr><th>Petugas</th><th>Pejabat<br>Administrator</th><th>Pimpinan Tinggi<br>Pratama</th></tr></thead><tbody><tr><td></td><td></td><td></td></tr></tbody></table>':''}</div><div class="signarea">${esc(st.signTitle)},<div class="signname">${esc(st.signName)}</div></div></div><div class="cc">Tembusan:<ol>${cc}</ol></div></div></div></article>`;}
function formatDate(d){if(!d)return'—';const [y,m,day]=d.split('-');return`${Number(day)} ${['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][+m]||''} ${y}`;}
function previewFor(id){if(!S.rows.length){toast('Tambahkan paling sedikit satu penerima untuk melihat surat.');return;}S.previewId=S.rows.some(r=>r.id===id)?id:S.rows[0].id;S.page=1;renderPreview();}
function renderPreview(){const current=S.rows.find(x=>x.id===S.previewId)||S.rows[0];const body=`<div class="preview-layout"><aside class="preview-sidebar"><div style="font-size:12px;font-weight:750;margin-bottom:10px">Pilih penerima surat</div><div class="preview-list" id="previewList"></div><div class="notice" style="margin-top:13px;font-size:11px">Pratinjau ini bersifat visual (bukan hasil DOCX final). Gunakan <b>Buat dokumen Word (ZIP)</b> untuk menghasilkan berkas. Periksa hasil akhir di <b>Microsoft Word</b>.</div></aside><div class="preview-sheet-wrap"><div class="preview-switch"><button class="btn sm ${S.page===1?'active':''}" data-page="1">Halaman 1 · Dengan tabel paraf</button><button class="btn sm ${S.page===2?'active':''}" data-page="2">Halaman 2 · Tanpa paraf</button></div><div id="visiblePage"></div></div></div>`;
  const el=showModal('Pratinjau memorandum — 2 halaman',`Penerima: ${esc(current.name)} • ${esc(groupLabel(current))} • Nomor: ${esc(current.number)}`,body,`<button class="btn" data-close>Tutup</button><button class="btn primary" id="printOne">▤ Cetak pratinjau surat ini</button>`,true);
 function rerender(){const r=S.rows.find(x=>x.id===S.previewId);el.querySelector('#previewList').innerHTML=S.rows.map(x=>`<button class="preview-link ${x.id===S.previewId?'active':''}" data-preview-id="${x.id}"><span>${esc(x.name||'Tanpa nama')}<span class="group-preview-tag">${esc(groupLabel(x))}</span></span><small>${rowErrors(x).length?'!':'✓'}</small></button>`).join('');el.querySelector('#visiblePage').innerHTML=makeDocPage(r,S.page===1);el.querySelectorAll('[data-page]').forEach(b=>b.classList.toggle('active',+b.dataset.page===S.page));el.querySelectorAll('[data-preview-id]').forEach(b=>b.addEventListener('click',()=>{S.previewId=+b.dataset.previewId;S.page=1;rerender()}));el.querySelectorAll('[data-page]').forEach(b=>b.addEventListener('click',()=>{S.page=+b.dataset.page;rerender()}));}
 rerender();el.querySelector('#printOne').addEventListener('click',()=>{const r=S.rows.find(x=>x.id===S.previewId);$('printStage').innerHTML=makeDocPage(r,true)+makeDocPage(r,false);setTimeout(()=>window.print(),50)});}
function payloadForWord(){
  const bulan=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  let tanggal=S.common.letterDate||'';
  if(/^\d{4}-\d{2}-\d{2}$/.test(tanggal)){
    const [y,m,d]=tanggal.split('-');
    tanggal=`${Number(d)} ${bulan[Number(m)]||''} ${y}`;
  }
  const signTitle=String(S.settings.signTitle||'').replace(/,\s*$/,'');
  return {
    nama_kumpulan_surat:S.common.batchName||'',
    informasi_bersama:{
      tanggal_surat:tanggal,
      jabatan_pengirim:S.common.sender||'',
      jabatan_penandatangan: signTitle ? (signTitle + ',') : '',
      nama_penandatangan:S.settings.signName||'',
      tembusan:ccList()
    },
    kelompok_audit:S.groups.map(g=>({id:String(g.id),nama:g.name||'',referensi:(g.references||[]).map(x=>String(x??''))})),
    penerima:S.rows.map(r=>({
      nomor_memorandum:r.number||'',
      nama:r.name||'',
      nip:String(r.nip??''),
      pangkat_golongan:r.rank||'',
      jabatan:r.job||'',
      alasan_teguran:r.finding||'',
      sapaan:r.greeting||'',
      kelompok_audit_id:String(r.auditGroupId||''),
      referensi_khusus:r.referenceMode==='custom'?(r.customReferences||[]).map(x=>String(x??'')):null
    }))
  };
}
async function generateWordDocuments(){
  if(!S.rows.length){toast('Belum ada penerima yang ditambahkan.');return;}
  const {common,problems}=commonDataSummary();
  const errorRows=S.rows.filter(r=>rowErrors(r).length);
  const distribution=`<div class="table-preview" style="margin-top:10px"><table><thead><tr><th>Kelompok audit</th><th>Jumlah</th></tr></thead><tbody>${S.groups.map(g=>`<tr><td>${esc(g.name)}</td><td>${S.rows.filter(r=>r.auditGroupId===g.id).length}</td></tr>`).join('')}<tr><td>Referensi khusus</td><td>${S.rows.filter(r=>r.referenceMode==='custom').length}</td></tr></tbody></table></div>`;
  if(common.length||problems.length){
    const body=`<div class="notice warn"><b>Data belum siap diproses.</b> Ditemukan ${common.length} kekurangan pada informasi bersama dan ${errorRows.length} surat yang memerlukan perbaikan.</div>${common.length?`<p><b>Informasi bersama:</b> ${esc(common.join(' • '))}</p>`:''}${distribution}<div class="table-preview" style="max-height:260px;margin-top:14px"><table><thead><tr><th>Penerima</th><th>Kelompok audit</th><th>Masalah</th></tr></thead><tbody>${errorRows.slice(0,30).map(r=>`<tr><td>${esc(r.name||'Tanpa nama')}</td><td>${esc(groupLabel(r))}</td><td>${esc(rowErrors(r).join(' • '))}</td></tr>`).join('')}</tbody></table></div><p class="mini-muted">Perbaiki data melalui tabel atau tombol Ubah sebelum membuat dokumen.</p>`;
    showModal('Data belum lengkap','Perbaiki data yang belum lengkap sebelum melanjutkan.',body,`<button class="btn primary" data-close>Tutup</button>`);
    return;
  }
  const btns=[document.getElementById('simulateGenerate'),document.getElementById('bottomGenerate')].filter(Boolean);
  btns.forEach(b=>{b.disabled=true;b.dataset.orig=b.textContent;b.textContent='Memproses...';});
  try{
    const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
    const response=await fetch('/buat-dokumen-word',{
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json,application/zip','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
      body:JSON.stringify(payloadForWord())
    });
    const ct=(response.headers.get('content-type')||'').toLowerCase();
    if(!response.ok || ct.includes('application/json')){
      let detail={};
      try{ detail=await response.json(); }catch(e){}
      const errs=(detail.errors||detail.rincian||[]).filter(Boolean);
      const msg=detail.pesan||detail.message||'Pembuatan dokumen gagal.';
      const list=errs.length?`<ul style="margin:10px 0 0 18px">${errs.map(x=>`<li>${esc(String(x))}</li>`).join('')}</ul>`:'';
      showModal('Gagal membuat dokumen',msg,`<div class="notice warn"><b>Periksa dan perbaiki data berikut:</b>${list||'<p style="margin-top:8px">Tidak ada rincian tambahan.</p>'}</div>`, `<button class="btn primary" data-close>Tutup</button>`);
      return;
    }
    const blob=await response.blob();
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');a.href=url;a.download='Hasil_Surat_Teguran.zip';document.body.append(a);a.click();a.remove();
    setTimeout(()=>URL.revokeObjectURL(url),4000);
    const body=`<div class="notice" style="background:var(--greenbg);border-color:#b7e9ca;color:#155d3e"><b>✓ Dokumen Word berhasil dibuat.</b></div><div class="summary-big"><div class="sumstat"><b>${S.rows.length}</b><span>Dokumen Word (DOCX)</span></div><div class="sumstat"><b>1</b><span>Berkas ZIP</span></div></div><p>Setiap penerima menghasilkan satu berkas DOCX berisi dua versi memorandum (dengan tabel paraf dan tanpa tabel paraf). Silakan periksa hasil di <b>Microsoft Word</b>, termasuk jumlah halaman dan tata letak.</p>${distribution}`;
    showModal('Dokumen Word siap diunduh','Pembuatan dokumen selesai.',body,`<button class="btn primary" data-close>Tutup</button>`);
    toast('Berkas ZIP berhasil diunduh.');
  }catch(err){
    showModal('Gagal membuat dokumen','Terjadi kesalahan saat menghubungi server.',`<div class="notice warn">${esc(String(err&&err.message?err.message:err))}</div>`, `<button class="btn primary" data-close>Tutup</button>`);
  }finally{
    btns.forEach(b=>{b.disabled=false;b.textContent=b.dataset.orig||b.textContent;});
  }
}
async function postJson(url,payload){
  const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
  const res=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload||{})});
  let data={};try{data=await res.json();}catch(e){}
  if(!res.ok)throw Object.assign(new Error(data.pesan||data.message||'Permintaan gagal.'),{data,status:res.status});
  return data;
}
function openSettings(){const st=S.settings;const body=`<div class="notice" style="margin-bottom:15px">Pengaturan ini disimpan dan dapat digunakan kembali. Perubahan tidak mengubah dokumen yang sudah berhasil diunduh.</div><div class="fields" style="grid-template-columns:1fr 1fr"><div class="field full"><label>Kementerian</label><input id="set_ministry" value="${esc(st.ministry)}"></div><div class="field full"><label>Unit organisasi</label><input id="set_agency" value="${esc(st.agency)}"></div><div class="field full"><label>Alamat kop surat</label><textarea id="set_address">${esc(st.address)}</textarea></div><div class="field full"><label>Jabatan penandatangan</label><input id="set_signTitle" value="${esc(st.signTitle)}"></div><div class="field full"><label>Nama penandatangan</label><input id="set_signName" value="${esc(st.signName)}"></div><div class="field full"><label>Daftar tembusan (satu penerima per baris)</label><textarea id="set_cc" rows="5">${esc(st.cc)}</textarea></div></div><div id="settingsErr" class="notice warn hidden" style="margin-top:10px"></div>`;
 const el=showDrawer('Pengaturan surat','Informasi awal instansi dan penandatangan surat.',body,`<button class="btn" data-close>Batal</button><button class="btn primary" id="saveSettings">Simpan pengaturan</button>`);el.querySelector('#saveSettings').addEventListener('click',async()=>{
  const btn=el.querySelector('#saveSettings');btn.disabled=true;btn.textContent='Menyimpan...';
  for(const k of Object.keys(st))st[k]=el.querySelector('#set_'+k).value;
  try{
    const data=await postJson('/generator/simpan-pengaturan',{sender:S.common.sender||st.sender||'',subject:S.common.subject||st.subject||'Teguran Tertulis',signTitle:st.signTitle,signName:st.signName,cc:st.cc,ministry:st.ministry,agency:st.agency,address:st.address});
    if(data&&data.settings){Object.assign(st,{ministry:data.settings.ministry||st.ministry,agency:data.settings.agency||st.agency,address:data.settings.address||st.address,signTitle:data.settings.signTitle||st.signTitle,signName:data.settings.signName||st.signName,cc:data.settings.cc||st.cc});}
    renderSummary();closeUI();toast('Pengaturan berhasil disimpan.');
  }catch(e){const box=el.querySelector('#settingsErr');box.textContent=String(e&&e.message?e.message:e);box.classList.remove('hidden');toast('Gagal menyimpan pengaturan.');}
  finally{btn.disabled=false;btn.textContent='Simpan pengaturan';}
 });}
function addRow(){const id=nextId++;S.rows.push({id,number:'',name:'',nip:'',rank:'',job:'',greeting:'Saudara',finding:'',auditGroupId:S.activeGroupId,referenceMode:'group',customReferences:[],requestedGroupName:''});markDirty();renderRows();renderSummary();openEdit(id)}
function duplicateSelected(){const picked=S.rows.filter(x=>S.selected.has(x.id));if(!picked.length){toast('Pilih baris penerima yang ingin disalin.');return;}const copied=picked.map(r=>({...structuredClone(r),id:nextId++,number:''}));S.rows.push(...copied);S.selected.clear();markDirty();renderRows();renderSummary();toast(`${copied.length} baris berhasil disalin. Isi nomor memorandum yang berbeda pada setiap baris.`)}
function removeSelected(){const count=S.selected.size;if(!count){toast('Pilih baris penerima yang ingin dihapus.');return;}if(!confirm(`Hapus ${count} penerima dari daftar ini?`))return;S.rows=S.rows.filter(x=>!S.selected.has(x.id));S.selected.clear();markDirty();renderRows();renderSummary();toast(`${count} penerima dihapus.`)}
function fill30(){if(S.rows.some(r=>!r.name.trim())||S.dirty){if(!confirm('Ganti seluruh data penerima dengan 30 data contoh? Data yang telah diisi akan hilang.'))return;}S.rows=Array.from({length:30},(_,i)=>makeExample(i,(S.groups[i<15?0:i<25?1:2]||S.groups[0]).id));S.selected.clear();markDirty();renderRows();renderSummary();toast('Sebanyak 30 data contoh telah ditampilkan. Ganti data contoh dengan data yang sesuai sebelum digunakan.')}
function importCsvFile(file){if(!file)return;const reader=new FileReader();reader.onload=()=>{openPaste(String(reader.result||'').replace(/^\ufeff/,''));};reader.onerror=()=>toast('Berkas tidak dapat dibaca.');reader.readAsText(file);$('importCsv').value='';}
function bind(){attachInputs();$('addRef').addEventListener('click',()=>{activeGroup().references.push('');markDirty();renderReferences();renderRows();renderSummary();[...$('referenceList').querySelectorAll('textarea')].at(-1)?.focus()});
$('addAuditGroup').addEventListener('click',addAuditGroup);
$('duplicateAuditGroup').addEventListener('click',duplicateAuditGroup);
$('removeAuditGroup').addEventListener('click',removeAuditGroup);
const saveGroupsBtn=$('saveAuditGroups');if(saveGroupsBtn)saveGroupsBtn.addEventListener('click',async()=>{
  saveGroupsBtn.disabled=true;const orig=saveGroupsBtn.textContent;saveGroupsBtn.textContent='Menyimpan...';
  try{
    const oldIds=S.groups.map(g=>g.id);
    const payload={groups:S.groups.map(g=>({id:g.id,name:g.name,references:g.references}))};
    const data=await postJson('/generator/simpan-kelompok-audit',payload);
    const saved=Array.isArray(data.groups)&&data.groups.length?data.groups:null;
    if(saved){
      const idMap={};oldIds.forEach((old,i)=>{if(saved[i])idMap[old]=saved[i].id;});
      S.groups=saved.map(g=>({id:String(g.id),name:String(g.name||''),references:Array.isArray(g.references)&&g.references.length?g.references.map(x=>String(x??'')):['']}));
      S.rows.forEach(r=>{if(idMap[r.auditGroupId])r.auditGroupId=idMap[r.auditGroupId];});
      S.activeGroupId=idMap[S.activeGroupId]|| (S.groups[0]&&S.groups[0].id) || S.activeGroupId;
      let m=0;for(const g of S.groups){const n=parseInt(String(g.id).replace(/^g/,''),10);if(!isNaN(n)&&n>m)m=n;}nextGroupId=m+1;
    }
    renderReferences();renderRows();renderSummary();toast('Kelompok audit berhasil disimpan.');
  }catch(e){toast('Gagal menyimpan kelompok audit: '+String(e&&e.message?e.message:e));}
  finally{saveGroupsBtn.disabled=false;saveGroupsBtn.textContent=orig;}
});
$('auditGroupName').addEventListener('input',e=>{
 const name=e.target.value;activeGroup().name=name;markDirty();
 $('auditGroupTabs').querySelectorAll('[data-group-tab]').forEach(b=>{const g=S.groups.find(g=>g.id===b.dataset.groupTab);if(g){b.firstChild.textContent=(g.name||'Tanpa nama')+' ';}});
 renderBulkAuditOptions();renderRows();renderSummary();
});
$('applyBulkAudit').addEventListener('click',()=>{
 const groupId=$('bulkAuditGroup').value;
 if(!S.selected.size){toast('Pilih paling sedikit satu penerima terlebih dahulu.');return;}
 const count=S.selected.size;
 S.rows.filter(r=>S.selected.has(r.id)).forEach(r=>{r.auditGroupId=groupId;r.referenceMode='group';r.requestedGroupName='';});
 markDirty();renderReferences();renderRows();renderSummary();toast(`${count} penerima telah menggunakan kelompok audit yang dipilih.`);
});$('addRowTop').addEventListener('click',addRow);$('pasteOpen').addEventListener('click',()=>openPaste());$('importCsv').addEventListener('change',e=>importCsvFile(e.target.files?.[0]));$('downloadTemplate').addEventListener('click',downloadCsvTemplate);$('duplicateSelected').addEventListener('click',duplicateSelected);$('removeSelected').addEventListener('click',removeSelected);$('search').addEventListener('input',e=>{S.search=e.target.value;renderRows()});$('filter').addEventListener('change',e=>{S.filter=e.target.value;renderRows()});$('selectAll').addEventListener('change',e=>{const visible=S.rows.filter(r=>[r.name,r.nip,r.job,r.rank,r.finding,r.number].some(v=>String(v).toLowerCase().includes(S.search.toLowerCase()))&&(S.filter==='all'||(rowErrors(r).length?'bad':'ok')===S.filter));for(const r of visible)e.target.checked?S.selected.add(r.id):S.selected.delete(r.id);renderRows()});$('settingsOpen').addEventListener('click',openSettings);$('fillExamples').addEventListener('click',fill30);$('previewFirst').addEventListener('click',()=>previewFor(S.rows[0]?.id));$('bottomPreview').addEventListener('click',()=>previewFor(S.rows[0]?.id));$('simulateGenerate').addEventListener('click',generateWordDocuments);$('bottomGenerate').addEventListener('click',generateWordDocuments);document.addEventListener('keydown',e=>{if(e.key==='Escape')closeUI()});}
syncInputs();renderAll();bind();
})();
</script>
</body>
</html>
