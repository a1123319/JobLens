<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "交通運輸及航運";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業供應鏈</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE) ?>);

        function showCompanyList(sector, color) {
            if (companySectors.has(sector)) {
                toggleCompanyList(companySectors, sector, color);
                return;
            }

            const companyListDiv = document.getElementById('company-list');
            companyListDiv.className = 'rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-slate-600';
            companyListDiv.innerHTML = `<p class="mb-1 font-bold text-slate-700">${sector}</p><p class="text-sm">目前尚無可顯示的公司資料。</p>`;
            companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto space-y-12 px-4 py-8">
        <section id="transportation-overview" class="scroll-mt-24" aria-labelledby="transportation-heading">
            <h1 id="transportation-heading" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-sky-500" aria-hidden="true"></span>
                供應鏈結構圖
            </h1>

            <div class="relative rounded-2xl border border-slate-100 bg-white p-6 pb-12 shadow-xl md:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-sky-500 via-cyan-500 to-blue-600" aria-hidden="true"></div>

                <div class="mb-8 text-center">
                    <p class="text-sm text-slate-500">本產業各服務類型為並列關係，點擊類別可查看對應上市公司。</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
                    <button type="button" onclick="showCompanyList('貨櫃航運', 'sky')" class="group min-h-52 rounded-xl border border-sky-200 bg-sky-50/60 p-5 text-left shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-sky-400 hover:bg-sky-50 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                        <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-sky-600 shadow-sm transition-transform group-hover:scale-105" aria-hidden="true"><i class="fa-solid fa-ship"></i></span>
                        <span class="block text-lg font-bold leading-snug text-slate-700">貨櫃航運</span>
                        <span class="mt-2 block text-sm text-slate-500">定期貨櫃海運服務</span>
                    </button>

                    <button type="button" onclick="showCompanyList('散裝航運', 'cyan')" class="group min-h-52 rounded-xl border border-cyan-200 bg-cyan-50/60 p-5 text-left shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600">
                        <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-cyan-600 shadow-sm transition-transform group-hover:scale-105" aria-hidden="true"><i class="fa-solid fa-anchor"></i></span>
                        <span class="block text-lg font-bold leading-snug text-slate-700">散裝航運</span>
                        <span class="mt-2 block text-sm text-slate-500">大宗貨物海運服務</span>
                    </button>

                    <button type="button" onclick="showCompanyList('海陸空貨運承攬', 'blue')" class="group min-h-52 rounded-xl border border-blue-200 bg-blue-50/60 p-5 text-left shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-400 hover:bg-blue-50 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-blue-600 shadow-sm transition-transform group-hover:scale-105" aria-hidden="true"><i class="fa-solid fa-plane"></i></span>
                        <span class="block text-lg font-bold leading-snug text-slate-700">海陸空貨運承攬</span>
                        <span class="mt-2 block text-sm text-slate-500">跨境與複合式貨運服務</span>
                    </button>

                    <button type="button" onclick="showCompanyList('貨櫃運輸集散及倉儲', 'indigo')" class="group min-h-52 rounded-xl border border-indigo-200 bg-indigo-50/60 p-5 text-left shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-indigo-600 shadow-sm transition-transform group-hover:scale-105" aria-hidden="true"><i class="fa-solid fa-warehouse"></i></span>
                        <span class="block text-lg font-bold leading-snug text-slate-700">貨櫃運輸集散及倉儲</span>
                        <span class="mt-2 block text-sm text-slate-500">貨櫃場站、集散與倉儲</span>
                    </button>

                    <button type="button" onclick="showCompanyList('海陸空大眾運輸', 'violet')" class="group min-h-52 rounded-xl border border-violet-200 bg-violet-50/60 p-5 text-left shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-violet-400 hover:bg-violet-50 hover:shadow-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600 md:col-span-2 xl:col-span-1">
                        <span class="mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-violet-600 shadow-sm transition-transform group-hover:scale-105" aria-hidden="true"><i class="fa-solid fa-bus-simple"></i></span>
                        <span class="block text-lg font-bold leading-snug text-slate-700">海陸空大眾運輸</span>
                        <span class="mt-2 block text-sm text-slate-500">海、陸、空客運服務</span>
                    </button>
                </div>

                <div class="mt-16 space-y-6" aria-live="polite">
                    <h2 class="border-l-4 border-sky-500 pl-4 text-xl font-bold text-slate-700">點擊上方類別查看公司列表</h2>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "掌握航運、貨運承攬、倉儲與大眾運輸服務的上市公司資訊");
    </script>
</body>
</html>
