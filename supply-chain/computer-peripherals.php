<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "電腦周邊";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>供應鏈分析</title>
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
        .chart-container { position: relative; height: 300px; width: 100%; }
        /* 隱藏捲軸但保持功能 */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id);?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 relative mb-12">
                    
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">零組件</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 relative z-10 content-start" id="upstream-container"></div>
                        </div>
                    </div>
                
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">終端設備與系統</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 relative z-10 content-start" id="downstream-container"></div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>
    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從零組件到終端與設備系統，全面透視產業鏈夥伴");

        // 上游公司名單
        const upstreamItems = [
            { icon: 'fa-microchip', text: '中央處理器' },
            { icon: 'fa-server', text: '晶片組' },
            { icon: 'fa-tv', text: '面板' },
            { icon: 'fa-desktop', text: '顯示器模組' },
            { icon: 'fa-memory', text: '記憶體' },
            { icon: 'fa-computer', text: '主機板' },
            { icon: 'fa-box', text: '機殼' },
            { icon: 'fa-plug', text: '電源供應器' },
            { icon: 'fa-network-wired', text: '網路卡' },
            { icon: 'fa-battery-full', text: '電池' },
            { icon: 'fa-fan', text: '散熱片、風扇馬達、散熱模組' },
            { icon: 'fa-sliders', text: '輸出入模組/介面卡' },
            { icon: 'fa-microchip', text: '顯示卡' },
            { icon: 'fa-hard-drive', text: '硬碟機' },
            { icon: 'fa-compact-disc', text: '光碟機' },
            { icon: 'fa-database', text: '磁碟儲存系統' },
            { icon: 'fa-code', text: 'BIOS(嵌入式軟體)' },
            { icon: 'fa-usb', text: '隨身碟、記憶卡讀卡機' },
            { icon: 'fa-video', text: '多功能視訊卡' },
            { icon: 'fa-camera', text: '光學鏡片、鏡頭' },
            { icon: 'fa-compact-disc', text: '光碟片' },
            { icon: 'fa-link', text: '連接線' },
            { icon: 'fa-layer-group', text: '機構樞紐' },
            { icon: 'fa-cubes', text: '金屬、塑膠模具' },
            { icon: 'fa-puzzle-piece', text: '其他電腦及週邊設備之零組件' },
        ];

        // 下游公司名單
        const downstreamItems = [
            { icon: 'fa-laptop', text: '筆記型電腦' },
            { icon: 'fa-computer', text: '桌上型電腦' },
            { icon: 'fa-laptop-code', text: '精簡型電腦' },
            { icon: 'fa-print', text: '印表機、傳真機、掃瞄器、多功能事務機、投影機' },
            { icon: 'fa-industry', text: '工業電腦' },
            { icon: 'fa-server', text: '伺服器' },
            { icon: 'fa-shield-halved', text: '安全監控系統' },
            { icon: 'fa-plug', text: '其他電腦及週邊設備' },
        ];

        const containerAndItems = [
            [ 
                document.getElementById('upstream-container'), 
                upstreamItems.map(item => ({...item, color: 'cyan'}))
            ], [ 
                document.getElementById('downstream-container'),
                downstreamItems.map(item => ({...item, color: 'blue'}))
            ]
        ];

        // 渲染選單按鈕
        for (const [container, items] of containerAndItems) {
            for (const item of items) {
                const div = document.createElement("div");

                div.className = `cursor-pointer bg-white border border-slate-200 hover:border-${item.color}-400 hover:bg-${item.color}-50 rounded-lg p-2 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm`;
                div.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-${item.color}-50 text-${item.color}-500 flex  items-center justify-center text-sm flex-shrink-0"><i class="fa-solid ${item.icon}"></i></div>
                <div class="text-left"><h6 class="font-bold text-slate-700 text-xs leading-tight">${item.text}</h6></div>
                `;
                
                div.addEventListener("click", () => toggleCompanyList(companySectors, item.text, item.color));
                container.append(div);
            }
        }
    </script>
</body>
</html>