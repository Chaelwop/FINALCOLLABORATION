import { SAMPLE_DATA } from './data/sampleData.js';
import { renderSimpleTemplate, renderModernTemplate, renderCreativeTemplate } from './templates/renderers.js';
import { loadPortfoliosFromStorage, savePortfoliosToStorage, STORAGE_KEY } from './utils/storage.js';
import { escapeHTML, showToast, updateSavedBadge } from './utils/helpers.js';

const portfolioState = {
    portfoliosDB: [],
    currentPortfolio: null,
    selectedTemplate: 'simple',
};

window.addEventListener('DOMContentLoaded', () => {
    portfolioState.portfoliosDB = loadPortfoliosFromStorage();
    if (portfolioState.portfoliosDB.length === 0) {
        portfolioState.portfoliosDB.push(SAMPLE_DATA);
        savePortfoliosToStorage(portfolioState.portfoliosDB);
    }
    updateSavedBadge(portfolioState.portfoliosDB);
    navigateTo('home');
});

function navigateTo(viewName) {
    document.querySelectorAll('.view-section').forEach(sec => sec.classList.add('hidden'));
    const targetView = document.getElementById(`view-${viewName}`);
    if (targetView) targetView.classList.remove('hidden');

    ['home', 'form', 'manage'].forEach(nav => {
        const btn = document.getElementById(`nav-${nav}`);
        if (btn) {
            if (nav === viewName) {
                btn.classList.add('text-sky-600', 'bg-sky-50');
                btn.classList.remove('text-slate-600');
            } else {
                btn.classList.remove('text-sky-600', 'bg-sky-50');
                btn.classList.add('text-slate-600');
            }
        }
    });

    if (viewName === 'manage') renderManageList();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function addProjectRow(data = { title: '', desc: '', link: '' }) {
    const container = document.getElementById('projects-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2 relative group';
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 text-xs font-bold p-1">
            <i class="fa-solid fa-trash"></i> Remove
        </button>
        <div class="grid sm:grid-cols-2 gap-2 pr-12">
            <input type="text" placeholder="Project Title" value="${data.title || ''}" class="proj-title w-full px-3 py-1.5 rounded border border-slate-300 text-sm">
            <input type="url" placeholder="Project Link (URL)" value="${data.link || ''}" class="proj-link w-full px-3 py-1.5 rounded border border-slate-300 text-sm">
        </div>
        <textarea placeholder="Brief description of the project..." rows="2" class="proj-desc w-full px-3 py-1.5 rounded border border-slate-300 text-sm">${data.desc || ''}</textarea>
    `;
    container.appendChild(div);
}

function addExperienceRow(data = { company: '', role: '', duration: '', desc: '' }) {
    const container = document.getElementById('experience-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-2 relative';
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 text-xs font-bold p-1">
            <i class="fa-solid fa-trash"></i> Remove
        </button>
        <div class="grid sm:grid-cols-3 gap-2 pr-12">
            <input type="text" placeholder="Company Name" value="${data.company || ''}" class="exp-company w-full px-3 py-1.5 rounded border border-slate-300 text-sm">
            <input type="text" placeholder="Role / Position" value="${data.role || ''}" class="exp-role w-full px-3 py-1.5 rounded border border-slate-300 text-sm">
            <input type="text" placeholder="Duration (e.g. 2021 - Present)" value="${data.duration || ''}" class="exp-duration w-full px-3 py-1.5 rounded border border-slate-300 text-sm">
        </div>
        <textarea placeholder="Key accomplishments and duties..." rows="2" class="exp-desc w-full px-3 py-1.5 rounded border border-slate-300 text-sm">${data.desc || ''}</textarea>
    `;
    container.appendChild(div);
}

function loadSampleDataIntoForm() {
    document.getElementById('fullName').value = SAMPLE_DATA.fullName;
    document.getElementById('jobTitle').value = SAMPLE_DATA.jobTitle;
    document.getElementById('email').value = SAMPLE_DATA.email;
    document.getElementById('phone').value = SAMPLE_DATA.phone;
    document.getElementById('address').value = SAMPLE_DATA.address;
    document.getElementById('profilePic').value = SAMPLE_DATA.profilePic;
    document.getElementById('aboutMe').value = SAMPLE_DATA.aboutMe;
    document.getElementById('eduSchool').value = SAMPLE_DATA.eduSchool;
    document.getElementById('eduDegree').value = SAMPLE_DATA.eduDegree;
    document.getElementById('eduYear').value = SAMPLE_DATA.eduYear;
    document.getElementById('skillsInput').value = SAMPLE_DATA.skills.join(', ');
    document.getElementById('socialGithub').value = SAMPLE_DATA.socialGithub;
    document.getElementById('socialLinkedin').value = SAMPLE_DATA.socialLinkedin;
    document.getElementById('socialWebsite').value = SAMPLE_DATA.socialWebsite;

    document.getElementById('projects-container').innerHTML = '';
    SAMPLE_DATA.projects.forEach(p => addProjectRow(p));

    document.getElementById('experience-container').innerHTML = '';
    SAMPLE_DATA.experience.forEach(e => addExperienceRow(e));

    showToast('Loaded demo data into form!');
}

function loadSampleAndCreate() {
    portfolioState.currentPortfolio = { ...SAMPLE_DATA, id: 'pf-' + Date.now() };
    const existingIdx = portfolioState.portfoliosDB.findIndex(p => p.id === portfolioState.currentPortfolio.id);
    if (existingIdx >= 0) portfolioState.portfoliosDB[existingIdx] = portfolioState.currentPortfolio;
    else portfolioState.portfoliosDB.unshift(portfolioState.currentPortfolio);
    savePortfoliosToStorage(portfolioState.portfoliosDB);
    updateSavedBadge(portfolioState.portfoliosDB);

    navigateTo('templates');
    showToast('Demo portfolio created!');
}

function handleFormSubmit(event) {
    event.preventDefault();

    const projects = [];
    document.querySelectorAll('#projects-container > div').forEach(div => {
        const title = div.querySelector('.proj-title')?.value.trim();
        const link = div.querySelector('.proj-link')?.value.trim();
        const desc = div.querySelector('.proj-desc')?.value.trim();
        if (title) projects.push({ title, link, desc });
    });

    const experience = [];
    document.querySelectorAll('#experience-container > div').forEach(div => {
        const company = div.querySelector('.exp-company')?.value.trim();
        const role = div.querySelector('.exp-role')?.value.trim();
        const duration = div.querySelector('.exp-duration')?.value.trim();
        const desc = div.querySelector('.exp-desc')?.value.trim();
        if (company || role) experience.push({ company, role, duration, desc });
    });

    const rawSkills = document.getElementById('skillsInput').value;
    const skills = rawSkills.split(',').map(s => s.trim()).filter(s => s.length > 0);

    const formData = {
        id: portfolioState.currentPortfolio ? portfolioState.currentPortfolio.id : 'pf-' + Date.now(),
        fullName: document.getElementById('fullName').value.trim(),
        jobTitle: document.getElementById('jobTitle').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        address: document.getElementById('address').value.trim(),
        profilePic: document.getElementById('profilePic').value.trim() || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
        aboutMe: document.getElementById('aboutMe').value.trim(),
        eduSchool: document.getElementById('eduSchool').value.trim(),
        eduDegree: document.getElementById('eduDegree').value.trim(),
        eduYear: document.getElementById('eduYear').value.trim(),
        skills: skills.length ? skills : ['JavaScript', 'Web Development'],
        projects,
        experience,
        socialGithub: document.getElementById('socialGithub').value.trim(),
        socialLinkedin: document.getElementById('socialLinkedin').value.trim(),
        socialWebsite: document.getElementById('socialWebsite').value.trim(),
    };

    portfolioState.currentPortfolio = formData;

    const existingIdx = portfolioState.portfoliosDB.findIndex(p => p.id === formData.id);
    if (existingIdx >= 0) portfolioState.portfoliosDB[existingIdx] = formData;
    else portfolioState.portfoliosDB.unshift(formData);

    savePortfoliosToStorage(portfolioState.portfoliosDB);
    updateSavedBadge(portfolioState.portfoliosDB);
    showToast('Portfolio information saved!');
    navigateTo('templates');
}

function selectTemplate(tplName) {
    portfolioState.selectedTemplate = tplName;
    renderGeneratedPortfolio();
    navigateTo('preview');
}

function switchTemplateInPreview(tplName) {
    portfolioState.selectedTemplate = tplName;
    renderGeneratedPortfolio();
}

function renderGeneratedPortfolio() {
    if (!portfolioState.currentPortfolio) {
        portfolioState.currentPortfolio = SAMPLE_DATA;
    }

    const container = document.getElementById('portfolio-output');
    const data = portfolioState.currentPortfolio;

    ['simple', 'modern', 'creative'].forEach(t => {
        const btn = document.getElementById(`btn-tpl-${t}`);
        if (btn) {
            if (t === portfolioState.selectedTemplate) {
                btn.className = 'px-3 py-1.5 rounded-lg bg-white shadow text-slate-900 font-extrabold';
            } else {
                btn.className = 'px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900';
            }
        }
    });

    if (portfolioState.selectedTemplate === 'simple') {
        container.innerHTML = renderSimpleTemplate(data);
    } else if (portfolioState.selectedTemplate === 'modern') {
        container.innerHTML = renderModernTemplate(data);
    } else if (portfolioState.selectedTemplate === 'creative') {
        container.innerHTML = renderCreativeTemplate(data);
    }
}

function renderManageList() {
    const container = document.getElementById('manage-list-container');
    if (!container) return;

    if (portfolioState.portfoliosDB.length === 0) {
        container.innerHTML = `
            <div class="bg-white p-12 text-center rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-2xl mx-auto">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Portfolios Found</h3>
                <p class="text-slate-500 text-sm max-w-sm mx-auto">You haven't saved any portfolio entries yet. Create one now or try with sample data.</p>
                <button onclick="navigateTo('form')" class="px-5 py-2.5 bg-sky-600 text-white font-bold text-sm rounded-xl shadow hover:bg-sky-700 transition">
                    Create First Portfolio
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = portfolioState.portfoliosDB.map(p => `
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <img src="${escapeHTML(p.profilePic)}" onerror="this.src='https://placehold.co/100x100/e2e8f0/475569?text=User'" class="w-14 h-14 rounded-full object-cover border border-slate-200">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">${escapeHTML(p.fullName)}</h3>
                    <p class="text-sm text-sky-600 font-semibold">${escapeHTML(p.jobTitle)}</p>
                    <p class="text-xs text-slate-400 mt-1">${escapeHTML(p.email)} • ${(p.skills || []).slice(0, 3).join(', ')}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2 w-full sm:w-auto justify-end border-t sm:border-0 pt-3 sm:pt-0">
                <button onclick="viewPortfolioFromManage('${p.id}')" class="px-3 py-2 bg-sky-50 text-sky-700 hover:bg-sky-100 text-xs font-bold rounded-lg transition flex items-center gap-1">
                    <i class="fa-solid fa-eye"></i> View
                </button>
                <button onclick="editPortfolioFromManage('${p.id}')" class="px-3 py-2 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition flex items-center gap-1">
                    <i class="fa-solid fa-pen"></i> Edit
                </button>
                <button onclick="deletePortfolioFromManage('${p.id}')" class="px-3 py-2 bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold rounded-lg transition flex items-center gap-1">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

function viewPortfolioFromManage(id) {
    const found = portfolioState.portfoliosDB.find(p => p.id === id);
    if (found) {
        portfolioState.currentPortfolio = found;
        renderGeneratedPortfolio();
        navigateTo('preview');
    }
}

function editPortfolioFromManage(id) {
    const found = portfolioState.portfoliosDB.find(p => p.id === id);
    if (found) {
        portfolioState.currentPortfolio = found;
        populateFormWithData(found);
        navigateTo('form');
    }
}

function editCurrentPortfolio() {
    if (portfolioState.currentPortfolio) {
        populateFormWithData(portfolioState.currentPortfolio);
        navigateTo('form');
    }
}

function deletePortfolioFromManage(id) {
    if (confirm('Are you sure you want to delete this portfolio from the database?')) {
        portfolioState.portfoliosDB = portfolioState.portfoliosDB.filter(p => p.id !== id);
        savePortfoliosToStorage(portfolioState.portfoliosDB);
        updateSavedBadge(portfolioState.portfoliosDB);
        renderManageList();
        showToast('Portfolio deleted successfully');
    }
}

function populateFormWithData(data) {
    document.getElementById('form-heading').innerText = 'Edit Portfolio Details';
    document.getElementById('fullName').value = data.fullName || '';
    document.getElementById('jobTitle').value = data.jobTitle || '';
    document.getElementById('email').value = data.email || '';
    document.getElementById('phone').value = data.phone || '';
    document.getElementById('address').value = data.address || '';
    document.getElementById('profilePic').value = data.profilePic || '';
    document.getElementById('aboutMe').value = data.aboutMe || '';
    document.getElementById('eduSchool').value = data.eduSchool || '';
    document.getElementById('eduDegree').value = data.eduDegree || '';
    document.getElementById('eduYear').value = data.eduYear || '';
    document.getElementById('skillsInput').value = (data.skills || []).join(', ');
    document.getElementById('socialGithub').value = data.socialGithub || '';
    document.getElementById('socialLinkedin').value = data.socialLinkedin || '';
    document.getElementById('socialWebsite').value = data.socialWebsite || '';

    const pContainer = document.getElementById('projects-container');
    pContainer.innerHTML = '';
    if (data.projects && data.projects.length) {
        data.projects.forEach(p => addProjectRow(p));
    }

    const eContainer = document.getElementById('experience-container');
    eContainer.innerHTML = '';
    if (data.experience && data.experience.length) {
        data.experience.forEach(e => addExperienceRow(e));
    }
}

function generatePortfolioPicture() {
    const d = portfolioState.currentPortfolio || SAMPLE_DATA;
    const canvas = document.getElementById('exportCanvas');
    const ctx = canvas.getContext('2d');

    canvas.width = 800;
    canvas.height = 500;

    const grad = ctx.createLinearGradient(0, 0, 800, 500);
    if (portfolioState.selectedTemplate === 'modern') {
        grad.addColorStop(0, '#0f172a');
        grad.addColorStop(1, '#1e1b4b');
    } else if (portfolioState.selectedTemplate === 'creative') {
        grad.addColorStop(0, '#4c1d95');
        grad.addColorStop(0.5, '#7e22ce');
        grad.addColorStop(1, '#be185d');
    } else {
        grad.addColorStop(0, '#0284c7');
        grad.addColorStop(1, '#0369a1');
    }
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 800, 500);

    ctx.fillStyle = portfolioState.selectedTemplate === 'modern' ? 'rgba(30, 41, 59, 0.9)' : 'rgba(255, 255, 255, 0.95)';
    ctx.shadowColor = 'rgba(0, 0, 0, 0.25)';
    ctx.shadowBlur = 20;
    ctx.beginPath();
    ctx.roundRect(40, 40, 720, 420, 24);
    ctx.fill();
    ctx.shadowBlur = 0;

    const textColor = portfolioState.selectedTemplate === 'modern' ? '#ffffff' : '#0f172a';
    const subTextColor = portfolioState.selectedTemplate === 'modern' ? '#818cf8' : '#0284c7';

    ctx.fillStyle = textColor;
    ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
    ctx.fillText(d.fullName || 'User Name', 80, 110);

    ctx.fillStyle = subTextColor;
    ctx.font = 'bold 20px "Inter", sans-serif';
    ctx.fillText(d.jobTitle || 'Professional Role', 80, 145);

    ctx.fillStyle = portfolioState.selectedTemplate === 'modern' ? '#94a3b8' : '#475569';
    ctx.font = '14px "Inter", sans-serif';
    const shortBio = (d.aboutMe || '').substring(0, 90) + '...';
    ctx.fillText(shortBio, 80, 185);

    ctx.strokeStyle = portfolioState.selectedTemplate === 'modern' ? '#334155' : '#e2e8f0';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(80, 215);
    ctx.lineTo(720, 215);
    ctx.stroke();

    ctx.fillStyle = textColor;
    ctx.font = 'bold 16px "Inter", sans-serif';
    ctx.fillText('CORE SKILLS', 80, 250);

    let skillX = 80;
    let skillY = 270;
    (d.skills || []).slice(0, 6).forEach(skill => {
        ctx.font = 'bold 12px "Inter", sans-serif';
        const metrics = ctx.measureText(skill);
        const badgeWidth = metrics.width + 24;

        if (skillX + badgeWidth > 720) {
            skillX = 80;
            skillY += 35;
        }

        ctx.fillStyle = portfolioState.selectedTemplate === 'modern' ? '#312e81' : '#e0f2fe';
        ctx.beginPath();
        ctx.roundRect(skillX, skillY, badgeWidth, 26, 8);
        ctx.fill();

        ctx.fillStyle = portfolioState.selectedTemplate === 'modern' ? '#c7d2fe' : '#0369a1';
        ctx.fillText(skill, skillX + 12, skillY + 17);

        skillX += badgeWidth + 10;
    });

    ctx.fillStyle = portfolioState.selectedTemplate === 'modern' ? '#64748b' : '#94a3b8';
    ctx.font = '12px "Inter", sans-serif';
    ctx.fillText(`Email: ${d.email || 'N/A'}  |  Location: ${d.address || 'N/A'}`, 80, 420);

    ctx.fillStyle = subTextColor;
    ctx.font = 'bold 12px "Inter", sans-serif';
    ctx.fillText('Generated by PortfolioCraft', 560, 420);

    const modal = document.getElementById('pictureModal');
    const downloadBtn = document.getElementById('downloadCanvasBtn');
    downloadBtn.href = canvas.toDataURL('image/png');
    modal.classList.remove('hidden');
}

function closePictureModal() {
    document.getElementById('pictureModal').classList.add('hidden');
}

Object.assign(window, {
    navigateTo,
    addProjectRow,
    addExperienceRow,
    loadSampleDataIntoForm,
    loadSampleAndCreate,
    handleFormSubmit,
    selectTemplate,
    switchTemplateInPreview,
    renderManageList,
    viewPortfolioFromManage,
    editPortfolioFromManage,
    editCurrentPortfolio,
    deletePortfolioFromManage,
    populateFormWithData,
    generatePortfolioPicture,
    closePictureModal,
    renderGeneratedPortfolio,
});

window.STORAGE_KEY = STORAGE_KEY;
