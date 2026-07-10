<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "資通訊安全";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-500 via-cyan-500 to-violet-600 rounded-t-2xl"></div>
                
                <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 relative mb-12">
                    
                    <div class="xl:col-span-3 bg-slate-100/60 p-6 rounded-2xl border border-slate-200">
                        <div class="text-center mb-6">
                            <span class="text-slate-700 font-bold text-lg tracking-wider border-b-4 border-slate-300">
                                資安產品
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col h-full">
                                <h4 class="text-lg font-bold mb-3 flex items-center gap-2 mx-auto text-cyan-900">
                                    營運安全
                                </h4>
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3 flex-1 flex flex-col justify-center">
                                    <div onclick="toggleCompanyList(companySectors, '安全營運與事件回應', 'cyan')"
                                         class="cursor-pointer border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-shield-heart"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">安全營運與事件回應</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '資安治理', 'cyan')"
                                         class="cursor-pointer border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50/50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-gavel"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">資安治理</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <h4 class="text-lg font-bold mb-3 flex items-center gap-2 mx-auto text-blue-900">
                                    應用安全
                                </h4>
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3 flex-1 flex flex-col justify-center">
                                    <div onclick="toggleCompanyList(companySectors, '資料安全', 'blue')"
                                         class="cursor-pointer border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-database"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">資料安全</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '網頁內容安全', 'blue')"
                                         class="cursor-pointer border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-globe"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">網頁內容安全</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '雲端安全', 'blue')"
                                         class="cursor-pointer border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-cloud"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">雲端安全</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <h4 class="text-lg font-bold mb-3 flex items-center gap-2 mx-auto text-indigo-900">
                                    網路與基礎設施安全
                                </h4>
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3 flex-1 flex flex-col justify-center">
                                    <div onclick="toggleCompanyList(companySectors, '網路基礎設施', 'indigo')"
                                         class="cursor-pointer border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-server"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">網路基礎設施</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '網路安全防護', 'indigo')"
                                         class="cursor-pointer border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">網路安全防護</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '物聯網安全', 'indigo')"
                                         class="cursor-pointer border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-network-wired"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">物聯網安全</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col h-full">
                                <h4 class="text-lg font-bold mb-3 flex items-center gap-2 mx-auto text-violet-900">
									端點安全
                                </h4>
                                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm space-y-3 flex-1 flex flex-col justify-center">
                                    <div onclick="toggleCompanyList(companySectors, '端點安全防護', 'violet')"
                                         class="cursor-pointer border border-slate-200 hover:border-violet-400 hover:bg-violet-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-violet-50 text-violet-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-laptop-medical"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">端點安全防護</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '身分認證與訪問管理', 'violet')"
                                         class="cursor-pointer border border-slate-200 hover:border-violet-400 hover:bg-violet-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-violet-50 text-violet-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-user-shield"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">身分認證與訪問管理</h5>
                                    </div>
                                    <div onclick="toggleCompanyList(companySectors, '行動安全', 'violet')"
                                         class="cursor-pointer border border-slate-200 hover:border-violet-400 hover:bg-violet-50/50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                        <div class="w-10 h-10 rounded-full bg-violet-50 text-violet-500 flex items-center justify-center text-lg flex-shrink-0">
                                            <i class="fa-solid fa-mobile-screen"></i>
                                        </div>
                                        <h5 class="font-bold text-slate-700 text-sm">行動安全</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col h-full bg-slate-50 p-6 rounded-2xl border border-slate-200">
                        <div class="text-center mb-6">
                            <span class="text-slate-700 font-bold text-lg tracking-wider border-b-4 border-slate-300">
                                資安服務
                            </span>
                        </div>
                        <div class="flex flex-col justify-between gap-4 flex-1">
                            <div onclick="toggleCompanyList(companySectors, '資安防護能力分析與鑑識服務', 'purple')"
                                 class="cursor-pointer bg-white border border-purple-200 hover:border-purple-400 hover:bg-purple-50 rounded-xl p-5 shadow-sm transition-all text-center flex-1 flex flex-col justify-center items-center group hover:-translate-y-1">
                                <div class="text-purple-500 mb-2 group-hover:scale-110 transition-transform"><i class="fa-solid fa-magnifying-glass-chart text-2xl"></i></div>
                                <h5 class="font-bold text-slate-700 text-sm leading-snug">資安防護能力分析<br>與鑑識服務</h5>
                            </div>
                            
                            <div onclick="toggleCompanyList(companySectors, '資安營運管理服務', 'purple')"
                                 class="cursor-pointer bg-white border border-purple-200 hover:border-purple-400 hover:bg-purple-50 rounded-xl p-5 shadow-sm transition-all text-center flex-1 flex flex-col justify-center items-center group hover:-translate-y-1">
                                <div class="text-purple-500 mb-2 group-hover:scale-110 transition-transform"><i class="fa-solid fa-screwdriver-wrench text-2xl"></i></div>
                                <h5 class="font-bold text-slate-700 text-sm leading-snug">資安營運管理服務</h5>
                            </div>
                            
                            <div onclick="toggleCompanyList(companySectors, '資安顧問服務', 'purple')"
                                 class="cursor-pointer bg-white border border-purple-200 hover:border-purple-400 hover:bg-purple-50 rounded-xl p-5 shadow-sm transition-all text-center flex-1 flex flex-col justify-center items-center group hover:-translate-y-1">
                                <div class="text-purple-500 mb-2 group-hover:scale-110 transition-transform"><i class="fa-solid fa-user-tie text-2xl"></i></div>
                                <h5 class="font-bold text-slate-700 text-sm leading-snug">資安顧問服務</h5>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-600 flex items-center gap-2">
                        點擊上方圖表區塊查看對應資安廠商
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從資安產品到資安服務，全面透視產業鏈夥伴");
    </script>
</body>
</html>