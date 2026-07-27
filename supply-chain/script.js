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

function fromCompanyDatabase(entities) {
	const sectors = new Map();

	for (const entity of entities) {
		if (entity.Sector) {
			if (!sectors.has(entity.Sector)) {
				sectors.set(entity.Sector, []);
			}
			sectors.get(entity.Sector).push(entity);
		}

		const sub = entity.Subsector || entity.SubSector;
		if (sub && sub !== entity.Sector) {
			if (!sectors.has(sub)) {
				sectors.set(sub, []);
			}
			sectors.get(sub).push(entity);
		}
	}

	return sectors;
}


function toggleCompanyList(sectors, sector, color, iconMap = null) {
	const companies = sectors.get(sector);

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
		};

		for (const company of companies) {
			if (foreignRe.test(company.CompanyName)) {
				res.foreign.push(company);
			} else {
				res.domestic.push(company);
			}
		}

		return res;
	}

	if (companies[0].Subsector !== null) {
		contentHtml += `<div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-10">`;
		const subsectors = new Map();

		for (const company of companies) {
			if (!subsectors.has(company.Subsector)) {
				subsectors.set(company.Subsector, []);
			}

			const subsector = subsectors.get(company.Subsector);
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
			
			const { domestic, foreign } = categorizeDomestic(companiesInSubsectors);

			if (domestic.length > 0) {
				contentHtml += sectionOf("本國上市公司", domestic, color);
			}

			if (foreign.length > 0) {
				contentHtml += sectionOf("外國上市公司", foreign, color);
			}
			contentHtml += "</div>";
		}

		contentHtml += "</div>";
	} else {
		const { domestic, foreign } = categorizeDomestic(companies);

		contentHtml += `<div class="company-details">`;

		if (domestic.length > 0) {
			contentHtml += sectionOf("本國上市公司", domestic, color);
		}

		if (foreign.length > 0) {
			contentHtml += sectionOf("外國上市公司", foreign, color);
		}

		contentHtml += "</div>";
	}

	companyListDiv.innerHTML = contentHtml;
	companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
}