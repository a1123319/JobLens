<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "印刷電路板";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-cyan-500"></span> 供應鏈結構圖
            </h3>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-8 pb-12 shadow-xl">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500"></div>

                <div id="main_printed_circuit_board_panel" class="relative mb-12 mt-4 grid grid-cols-1 gap-12 lg:grid-cols-3">
                    <!-- 上游：材料與設備 -->
                    <div class="chain-col relative">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 font-bold text-cyan-600 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">材料與設備</span></h4>
                        </div>
                        <div class="relative flex h-full flex-col justify-center rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm transition-shadow hover:shadow-md">
                            <div class="absolute left-full top-1/2 z-0 hidden h-1 w-12 bg-slate-300 lg:block"></div>
                            <div class="absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <div class="relative z-10 flex flex-col gap-4">
                                <div onclick="toggleCompanyList(companySectors, '玻璃纖維/玻纖布', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">玻璃纖維／玻纖布</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '環氧樹脂', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">環氧樹脂</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '酚醛樹脂', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">酚醛樹脂</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '銅箔', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">銅箔</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '聚亞醯胺樹脂', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">聚亞醯胺樹脂</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '生產製程及檢測設備', 'cyan')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-cyan-400 hover:bg-cyan-50">
                                    <h5 class="font-bold text-slate-700">生產製程及檢測設備</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 中游：基板製造與組裝 -->
                    <div class="chain-col relative">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-600 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm font-normal text-slate-400">基板製造與組裝</span></h4>
                        </div>
                        <div class="relative flex h-full flex-col justify-center rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm transition-shadow hover:shadow-md">
                            <div class="absolute left-full top-1/2 z-0 hidden h-1 w-12 bg-slate-300 lg:block"></div>
                            <div class="absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <div class="relative z-10 flex flex-col gap-4">
                                <div onclick="toggleCompanyList(companySectors, '銅箔基板', 'blue')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-5 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-blue-400 hover:bg-blue-50">
                                    <h5 class="font-bold text-slate-700">銅箔基板</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '硬板、軟板、IC載板製造', 'blue')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-5 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-blue-400 hover:bg-blue-50">
                                    <h5 class="font-bold text-slate-700">硬板、軟板、IC載板製造</h5>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '基板組裝加工及相關製造', 'blue')" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-5 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-blue-400 hover:bg-blue-50">
                                    <h5 class="font-bold text-slate-700">基板組裝加工及相關製造</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游：終端應用（不可點擊） -->
                    <div class="chain-col">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 font-bold text-purple-600 shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">終端應用</span></h4>
                        </div>
                        <div class="flex h-full flex-col justify-center rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm transition-shadow hover:shadow-md">
                            <div aria-disabled="true" class="flex min-h-[220px] h-full cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-100 p-6 text-center opacity-75">
                                <h5 class="text-lg font-bold text-slate-600">各類電子產品</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-20 mt-20 space-y-6">
                    <h4 class="flex items-center gap-2 border-l-4 border-cyan-500 pl-4 text-xl font-bold text-slate-700">點擊上方圖表查看公司列表</h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從關鍵材料與設備、基板製造及組裝，到各類電子產品應用，掌握印刷電路板產業鏈脈絡。");
    </script>
</body>
</html>
