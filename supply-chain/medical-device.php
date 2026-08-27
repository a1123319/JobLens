<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "醫療器材";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=<?= time() ?>"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE) ?>);
        
        // 醫療器材 Icon Map：設定展開後各細項對應的 Icon 與顏色
        const iconMap = new Map([
            ["電子零組件、塑化材料、五金零件", new Map([
                ["塑化材料", "fa-solid fa-bottle-droplet text-cyan-500"],
                ["金屬零件", "fa-solid fa-gears text-cyan-600"],
                ["電子零組件", "fa-solid fa-microchip text-cyan-500"]
            ])],
            ["醫療器材研發、設計、製造", new Map([
                ["體溫計、血壓計", "fa-solid fa-heart-pulse text-teal-500"],
                ["人工關節", "fa-solid fa-bone text-teal-600"],
                ["隱型眼鏡、光學鏡片", "fa-solid fa-glasses text-emerald-500"],
                ["衛生用品", "fa-solid fa-box-tissue text-teal-500"],
                ["醫療耗材", "fa-solid fa-syringe text-emerald-600"],
                ["生物檢測", "fa-solid fa-vial-virus text-teal-600"],
                ["顯示器", "fa-solid fa-display text-emerald-500"],
                ["其他", "fa-solid fa-notes-medical text-teal-400"]
            ])],
            ["醫療器材代理銷售及通路", new Map([
                ["醫療器材代理", "fa-solid fa-truck-medical text-purple-500"],
                ["醫療院所、醫療器材專賣店、藥局等通路", "fa-solid fa-hospital text-purple-600"]
            ])]
        ]);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        
        .chain-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 50%;
            left: calc(100% + 0.5rem);
            width: calc(1.5rem - 2px);
            height: 2px;
            background: #cbd5e1;
        }
        .chain-step:not(:last-child)::before {
            content: "";
            position: absolute;
            top: calc(50% - 5px);
            right: -0.75rem;
            width: 0;
            height: 0;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
            border-left: 7px solid #cbd5e1;
            z-index: 1;
        }
        @media (max-width: 1023px) {
            .chain-step:not(:last-child)::after,
            .chain-step:not(:last-child)::before { display: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-600 w-2 h-8 rounded-full"></span>
                供應鏈結構圖
            </h2>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-500 via-teal-500 to-purple-600 rounded-t-2xl"></div>

                <div class="mb-8 text-center">
                    <p class="text-sm text-slate-500">依醫療器材產業鏈分類，點擊各區塊查看對應公司。</p>
                </div>

                <!-- 上中下游 三大按鈕區塊 -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 1. 上游 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h3 class="text-xl font-bold text-slate-700">上游</h3>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '電子零組件、塑化材料、五金零件', 'cyan', iconMap.get('電子零組件、塑化材料、五金零件'))" 
                             class="cursor-pointer flex-1 bg-white hover:bg-cyan-50/50 border border-slate-200 hover:border-cyan-400 p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 hover:-translate-y-0.5 group">
                            <div class="w-12 h-12 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-microchip"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-700 text-lg leading-snug">電子零組件、塑化材料、五金零件</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. 中游 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h3 class="text-xl font-bold text-slate-700">中游</h3>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '醫療器材研發、設計、製造', 'teal', iconMap.get('醫療器材研發、設計、製造'))" 
                             class="cursor-pointer flex-1 bg-white hover:bg-teal-50/50 border border-slate-200 hover:border-teal-400 p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 hover:-translate-y-0.5 group">
                            <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-stethoscope"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-700 text-lg leading-snug">醫療器材研發、設計、製造</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. 下游 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h3 class="text-xl font-bold text-slate-700">下游</h3>
                        </div>
                        
                        <div onclick="toggleCompanyList(companySectors, '醫療器材代理銷售及通路', 'purple', iconMap.get('醫療器材代理銷售及通路'))" 
                             class="cursor-pointer flex-1 bg-white hover:bg-purple-50/50 border border-slate-200 hover:border-purple-400 p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4 hover:-translate-y-0.5 group">
                            <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl flex-shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-hospital-user"></i>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-700 text-lg leading-snug">醫療器材代理銷售及通路</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 點擊後顯示廠商列表的容器 -->
                <div class="mt-8 space-y-6 relative z-20">
                    <h3 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-600">點擊上方區塊查看公司列表</h3>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從原材料供應到醫療院所通路，一站式掌握醫療器材生態系");
    </script>
</body>
</html>