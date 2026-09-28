function banner(title, subtitle) {
	const headerHTML = `
        <header class="bg-gradient-to-r from-slate-800 to-slate-900 text-white py-10 px-4">
            <div class="container mx-auto text-center max-w-2xl">
                <h1 class="text-2xl md:text-3xl font-bold mb-3">${title}</h1>
                <p class="text-slate-400 mb-6 text-sm">${subtitle}</p>
            </div>
        </header>
    `;
	document.getElementById('banner').innerHTML = headerHTML;
}

window.addEventListener('DOMContentLoaded', () => {
	// create footer
	const footer = document.getElementById('footer');
	footer.innerHTML = `
	<footer class="border-t border-slate-200 mt-12 pt-8 text-center text-xs text-slate-400 pb-8">
		<p>JobLens 2026 | 本系統使用政府資料開放平臺</p>
		<a style="display: block" href="https://www.flaticon.com/free-icons/eye" title="eye icons">Icons created by Vectors Market - Flaticon</a>
	</footer>
	`;
});

// 子產業比對用的名稱：忽略空白與括號說明。公司總覽（櫃買中心產業鏈）的名稱有時帶說明，
// 例如「電容器材料(如電蝕／化成鋁箔、介面瓷粉)」，頁面按鈕則是「電容器材料」「處理器 / IC」。
function sectorKey(name) {
	return name.split(/[(（]/)[0].replace(/\s+/g, '');
}

// 按鈕上的子產業名稱對應到的所有公司
function companiesOf(sectors, sector) {
	const key = sectorKey(sector);
	return [...sectors].filter(([name]) => name === sector || sectorKey(name) === key).flatMap(([, list]) => list);
}

// 頁面上的灰色「(無上市公司)」節點：該子產業若已有公司（目前是外國企業），就改成可點擊。
// 各頁的公司資料放在 companySectors 或 companyChainNodes（頁面頂層 const）。
function enableSectorsWithCompanies() {
	const sectors = typeof companySectors !== 'undefined' ? companySectors
		: typeof companyChainNodes !== 'undefined' ? companyChainNodes : null;
	if (!(sectors instanceof Map)) return;

	const nodes = document.querySelectorAll('.cursor-not-allowed, [aria-disabled="true"]');
	for (const node of nodes) {
		// 節點文字的第一段就是子產業名稱，例如「PET膜 (無上市公司)」「軟體工具 (無上市公司)\n（例如…）」
		const name = node.textContent.trim().split(/[\n(（]/)[0].trim();
		if (!name || companiesOf(sectors, name).length === 0) continue;

		// 顏色沿用同一區塊裡既有按鈕的 toggleCompanyList(…, '顏色')
		let color = 'cyan';
		for (let el = node.parentElement; el; el = el.parentElement) {
			const m = el.querySelector('[onclick*="toggleCompanyList"]')?.getAttribute('onclick').match(/,\s*'(\w+)'\s*[,)]/);
			if (m) { color = m[1]; break; }
		}

		node.removeAttribute('aria-disabled');
		node.setAttribute('role', 'button');
		node.tabIndex = 0;
		node.classList.remove('cursor-not-allowed', 'opacity-60', 'opacity-70', 'bg-slate-100', 'bg-slate-100/60');
		node.classList.add('cursor-pointer', 'bg-white', `hover:border-${color}-400`, `hover:bg-${color}-50`, 'hover:shadow-md', 'transition');
		for (const el of [node, ...node.querySelectorAll('*')]) {
			el.classList.replace('text-slate-400', 'text-slate-700');
			el.classList.replace('text-slate-500', 'text-slate-700');
		}
		// 「(無上市公司)」改成「(外國企業)」
		const walker = document.createTreeWalker(node, NodeFilter.SHOW_TEXT);
		while (walker.nextNode()) {
			walker.currentNode.nodeValue = walker.currentNode.nodeValue.replace('無上市公司', '外國企業');
		}

		const open = () => toggleCompanyList(sectors, name, color);
		node.addEventListener('click', open);
		node.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); } });
	}
}

window.addEventListener('DOMContentLoaded', enableSectorsWithCompanies);

function fromCompanyDatabase(entities) {
	const sectors = new Map();

	for (const entity of entities) {
		if (!sectors.has(entity.Sector)) {
			sectors.set(entity.Sector, []);
		}
		
		const sector = sectors.get(entity.Sector);
		sector.push(entity);
	}

	return sectors;
}


function toggleCompanyList(sectors, sector, color, iconMap = null) {
	const companies = companiesOf(sectors, sector);

	const companyListDiv = document.getElementById('company-list');
	companyListDiv.className = `bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-${color}-200`;
	let contentHtml = `
	<p class="font-bold text-slate-700 text-lg mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
		<span class="w-3 h-3 rounded-full bg-${color}-500"></span>
		${sector} <span class="text-sm font-normal text-slate-500 ml-2">(共${companies.length}家)</span>
	</p>`;

	function sectionOf(name, companies, color) {
		const tagsHtml = companies.map(company =>
			`<a class= "bg-slate-100 text-slate-600 hover:text-${color}-600 hover:bg-${color}-50 hover:border-${color}-200 active:bg-${color}-100 text-sm px-4 py-1.5 rounded-full border border-slate-200/50 font-medium cursor-pointer select-none transition-all duration-100 shadow-sm hover:shadow" href="../search.php?id=${company.CompanyId}" > ${company.CompanyName}</a>`
		).join('');

		return `
			<div class="mb-6">
				<div class="text-md font-bold text-slate-600 mb-3 bg-slate-50 p-2 rounded border-l-4 border-${color}-400">
					${name} (${companies.length}家)
				</div>
				<div class="flex flex-wrap gap-2 px-1">
					${tagsHtml}
				</div>
			</div>
		`;
	}

	function categorizeDomestic(companies) {
		const foreignRe = /.+?-\w+?/u;
		const res = {
			domestic: [],
			foreign: [],
			enterprise: [],
		};

		for (const company of companies) {
			// 知名外國企業（company.Id 從 1000000 起）
			if (+company.CompanyId >= 1000000) {
				res.enterprise.push(company);
			} else if (foreignRe.test(company.CompanyName)) {
				res.foreign.push(company);
			} else {
				res.domestic.push(company);
			}
		}

		return res;
	}

	// 只要有一家公司有細分產業就分組；沒有細分產業的公司放在「其他」
	if (companies.some(company => company.Subsector !== null)) {
		contentHtml += `<div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-10">`;
		const subsectors = new Map();

		for (const company of companies) {
			const key = company.Subsector ?? '其他';
			if (!subsectors.has(key)) {
				subsectors.set(key, []);
			}

			const subsector = subsectors.get(key);
			subsector.push(company);
		}

		for (const [subsector, companiesInSubsectors] of subsectors) {
			contentHtml += `<div class="company-details">
			<h5 class="text-md font-bold text-slate-600 mb-4 border-b border-slate-200 pb-1 flex items-center">`

			if (iconMap !== null && iconMap.has(subsector))
			{
				contentHtml += `<i class="${iconMap.get(subsector)} mr-2"></i>`;
			}

			contentHtml += `${subsector}</h5>`;
			
			const { domestic, foreign, enterprise } = categorizeDomestic(companiesInSubsectors);

			if (domestic.length > 0) {
				contentHtml += sectionOf("本國上市公司", domestic, color);
			}

			if (foreign.length > 0) {
				contentHtml += sectionOf("外國上市公司", foreign, color);
			}

			if (enterprise.length > 0) {
				contentHtml += sectionOf("外國企業", enterprise, color);
			}
			contentHtml += "</div>";
		}

		contentHtml += "</div>";
	} else {
		const { domestic, foreign, enterprise } = categorizeDomestic(companies);

		contentHtml += `<div class="company-details">`;

		if (domestic.length > 0) {
			contentHtml += sectionOf("本國上市公司", domestic, color);
		}

		if (foreign.length > 0) {
			contentHtml += sectionOf("外國上市公司", foreign, color);
		}

		if (enterprise.length > 0) {
			contentHtml += sectionOf("外國企業", enterprise, color);
		}

		contentHtml += "</div>";
	}

	companyListDiv.innerHTML = contentHtml;
	companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}