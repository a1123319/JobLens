<?php 
function nav($id = null) { ?>
    <nav class="bg-white text-slate-800 border-b border-slate-200 p-4 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a class="flex items-center gap-3 cursor-pointer" href="index.php">
                <img src="assets/magnifying-glass.png" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-xl font-bold tracking-wider">JobLens</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                <?php if ($id !== null): ?>
                <a href="search.php?id=<?= $id ?>" class="hover:text-cyan-600 transition">企業資訊</a>
                <?php endif; ?>
                <a href="about.html" class="border border-cyan-600 text-cyan-700 px-5 py-2 rounded-full font-bold hover:bg-cyan-600 hover:text-white transition-all">
                    關於我們
                </a>
            </div>
        </div>
    </nav>
<?php }
function footer() { ?>
    <footer class="border-t-2 bg-slate-100 border-slate-300 mt-12 py-8 text-center text-xs text-slate-500 [&_a]:underline">
        <p>JobLens 2026 | 本系統使用政府開放資料</p>
        <p>Icons by <a href="https://www.flaticon.com">Flaticon</a> and <a href="https://www.iconpacks.net">Iconpacks</a></p>
    </footer>
<?php }

/**
 * Renders and initializes the complete JobLens Search Component.
 * Require include fuse 7.3.0 (e.g., <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.3.0"></script>)
 * * @param PDO $pdo An active database connection instance.
 */
function renderSearch(
    PDO $pdo, 
    string $inputId = 'searchInput',
    string $buttonId = 'searchButton',
    string $boxId = 'suggestionBox') {
    // 1. Fetch Company Data directly within the component
    try {
        // Code 是顯示與搜尋用的代碼：外國公司（自編 Id）用股票代碼，其餘仍是 Id。
        // foreigncompany 還沒建立的資料庫退回只用 Id 的寫法，搜尋不受影響。
        $sql = "
            SELECT
                c.Id,
                c.Name,
                %s AS Code,
                GROUP_CONCAT(DISTINCT cc.Category SEPARATOR ',') AS Category,
                GROUP_CONCAT(DISTINCT n.Name SEPARATOR ' ') AS Nickname
            FROM company c
            JOIN companycategory cc ON c.Id = cc.CompanyId
            LEFT JOIN nickname n ON c.Id = n.CompanyId
            %s
            GROUP BY c.Id, c.Name%s
        ";
        try {
            $stmt = $pdo->prepare(sprintf($sql, 'COALESCE(fc.Ticker, CAST(c.Id AS CHAR))',
                'LEFT JOIN foreigncompany fc ON fc.CompanyId = c.Id', ', fc.Ticker'));
            $stmt->execute();
        } catch (PDOException $e) {
            $stmt = $pdo->prepare(sprintf($sql, 'CAST(c.Id AS CHAR)', '', ''));
            $stmt->execute();
        }
        $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p class='text-red-500'>Search component error: " . htmlspecialchars($e->getMessage()) . "</p>";
        return;
    }

    // 3. Render HTML & CSS Markup
    ?>
    <div class="relative max-w-xl mx-auto data-joblens-search">
        <input type="text" id="<?php echo $inputId; ?>" autocomplete="off"
               placeholder="輸入公司股票代碼或名稱" 
               class="w-full p-4 pl-6 rounded-full text-slate-900 shadow-2xl focus:outline-none focus:ring-4 focus:ring-cyan-600/50 transition text-lg border border-slate-100">
        
        <a id="<?php echo $buttonId; ?>"
            class="absolute right-2 top-2 bg-cyan-600 hover:bg-cyan-700 text-white px-8 py-2.5 rounded-full transition font-bold text-lg shadow-lg opacity-50 cursor-not-allowed">
            透視
        </a>

        <div id="<?php echo $boxId; ?>" class="hidden absolute w-full bg-white mt-2 rounded-2xl shadow-xl overflow-hidden z-40 text-left border border-slate-100 max-h-64 overflow-y-auto no-scrollbar">
        </div>
    </div>

    <script type="module" >
        import Fuse from 'https://cdn.jsdelivr.net/npm/fuse.js@7.3.0';

        const companyData = <?php echo json_encode($companies); ?>;
        
        for (const company of companyData)
        {
            company.Category = company.Category.split(',');
        }

        const searchInput = document.getElementById('<?php echo $inputId; ?>');
        const searchButton = document.getElementById('<?php echo $buttonId; ?>');
        const suggestionBox = document.getElementById('<?php echo $boxId; ?>');

        let currentResults = [];

        const fuse = new Fuse(companyData, {
            keys: [
                { name: 'Code', weight: 0.7 },
                { name: 'Name', weight: 0.5 },
                { name: 'Nickname', weight: 0.3 }
            ],
            threshold: 0.3,
            includeMatches: true
        });

        function isSubsequence(query, target) {
            let queryIdx = 0, targetIdx = 0;
            while (queryIdx < query.length && targetIdx < target.length) {
                if (query[queryIdx] === target[targetIdx]) queryIdx++;
                targetIdx++;
            }
            return queryIdx === query.length;
        }

        function updateButtonState() {
            if (currentResults.length === 1) {
                searchButton.classList.remove('opacity-50', 'cursor-not-allowed');
                searchButton.classList.add('cursor-pointer');
                searchButton.href = `search.php?id=${encodeURIComponent(currentResults[0].item.Id)}`;
            } else {
                searchButton.classList.add('opacity-50', 'cursor-not-allowed');
                searchButton.classList.remove('cursor-pointer');
                searchButton.href = "javascript:void(0)";
            }
        }

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length === 0) {
                currentResults = [];
                updateButtonState();
                suggestionBox.classList.add('hidden');
                return;
            }

            if (/^\d+$/.test(query)) {
                currentResults = companyData
                    .filter(c => isSubsequence(query, String(c.Code)))
                    .map(c => ({ item: c }));
            } else {
                currentResults = fuse.search(query);
            }

            updateButtonState();

            if (currentResults.length === 0) {
                suggestionBox.innerHTML = `<div class="p-4 text-sm text-slate-500 text-center">找不到符合的公司</div>`;
                suggestionBox.classList.remove('hidden');
                return;
            }

            suggestionBox.innerHTML = currentResults.slice(0, 10).map(res => {
                const item = res.item;
                let categoryHtml = "";
                let remaining = 0;

                if (item.Category)
                {
                    categoryHtml = item.Category.map(cat =>
                        `<span class="text-xs text-slate-400 border border-slate-200 px-2 py-0.5 rounded-full group-hover:border-emerald-200 group-hover:text-cyan-600 min-w-max">${cat}</span>`
                    ).join('');
                }

                return `<a data-name="${item.Name}" href="search.php?id=${encodeURIComponent(item.Id)}" class="joblens-item p-4 hover:bg-slate-50 border-b border-slate-100 last:border-none cursor-pointer flex items-center justify-between transition-colors group">
                    <div class="flex flex-col min-w-40 shrink-[10] gap-1">
                        <span class="font-bold text-slate-700 group-hover:text-cyan-800">${item.Name}</span>
                        ${item.Nickname ? `<span class="text-xs text-slate-400">${item.Nickname}</span>` : ''}
                    </div>
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="category-container flex items-left gap-3 text-right flex-initial overflow-hidden">
                            ${categoryHtml}
                        </div>
                        <span class="text-xs text-slate-400 flex-none" hidden></span>
                        <span class="font-mono font-bold text-cyan-700 bg-cyan-50 px-2 py-0.5 rounded text-sm">${item.Code}</span>
                    </div>
                </a>`
            }).join('');

            suggestionBox.classList.remove('hidden');

            const boxes = suggestionBox.children;

            for (const box of boxes) {
                const container = box.querySelector('.category-container');
                const remainingLabel = container.nextElementSibling;
                let remaining = 0;

                while (container.scrollWidth > container.clientWidth && container.childElementCount > 1) {
                    container.lastElementChild.remove();
                    remaining++;

                    remainingLabel.textContent = `...以及其他${remaining}個產業`;
                    remainingLabel.hidden = false;
                }

                box.addEventListener('click', (e) => {
                    searchInput.value = box.dataset.name;
                    suggestionBox.hidden = true;
                });
            }
        });

        // NEW: Listen for 'Enter' key presses inside the search input field
        searchInput.addEventListener('keydown', (e) => {
            if (currentResults.length == 1 && e.key === 'Enter') {
                searchButton.click();
            }
        });

        // Event Delegation pattern avoids needing global function names on window object

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                suggestionBox.classList.add('hidden');
            }
        });
    </script>
    <?php
}
?>