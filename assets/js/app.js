/* Portfolio Generator Engine & Data Handler */
const STORAGE_KEY = 'portfoliocraft_db_v1';

// Application State
let portfoliosDB = [];
let currentPortfolio = null;
let selectedTemplate = 'simple'; // 'simple', 'modern', 'creative'

// Demo sample data generator
const SAMPLE_DATA = {
    id: 'sample-101',
    fullName: 'Sarah Jenkins',
    jobTitle: 'Full-Stack Software Engineer',
    email: 'sarah.j@example.com',
    phone: '+1 (555) 382-9102',
    address: 'Seattle, WA, USA',
    profilePic: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400',
    aboutMe: 'Passionate and results-driven engineer with 5+ years of experience crafting modern, scalable web applications. Dedicated to clean architecture, interactive user experiences, and robust cloud services.',
    eduSchool: 'University of Washington',
    eduDegree: 'B.S. Software Engineering',
    eduYear: '2016 - 2020',
    skills: ['React.js', 'Node.js', 'TypeScript', 'Tailwind CSS', 'PostgreSQL', 'GraphQL', 'AWS', 'Docker'],
    projects: [
        { title: 'CloudMetrics Dashboard', desc: 'Real-time analytics tool for cloud infrastructure monitoring.', link: 'https://github.com/sample/metrics' },
        { title: 'TaskFlow Workspace', desc: 'Collaborative task management tool with drag-and-drop Kanban boards.', link: 'https://github.com/sample/taskflow' }
    ],
    experience: [
        { company: 'TechNova Solutions', role: 'Senior Frontend Developer', duration: '2022 - Present', desc: 'Lead a team of 4 engineers in migrating monolithic app to React/TypeScript micro-frontends, boosting speed by 40%.' },
        { company: 'Apex Digital', role: 'Full-Stack Engineer', duration: '2020 - 2022', desc: 'Developed scalable RESTful APIs and modern frontend user interfaces using Node.js and React.' }
    ],
    socialGithub: 'https://github.com',
    socialLinkedin: 'https://linkedin.com',
    socialWebsite: 'https://example.com'
};

// Initialize Application
window.addEventListener('DOMContentLoaded', () => {
    loadPortfoliosFromStorage();
    if (portfoliosDB.length === 0) {
        portfoliosDB.push(SAMPLE_DATA);
        savePortfoliosToStorage();
    }
    updateSavedBadge();
    navigateTo('home');
});

// Navigation Handler
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

// Storage Operations
function loadPortfoliosFromStorage() {
    try {
        const data = localStorage.getItem(STORAGE_KEY);
        portfoliosDB = data ? JSON.parse(data) : [];
    } catch (e) {
        console.error('Error loading LocalStorage DB', e);
        portfoliosDB = [];
    }
}

function savePortfoliosToStorage() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(portfoliosDB));
        updateSavedBadge();
    } catch (e) {
        console.error('Error saving LocalStorage DB', e);
    }
}

function updateSavedBadge() {
    const badge = document.getElementById('saved-count-badge');
    if (badge) badge.innerText = portfoliosDB.length;
}

// Form Dynamic Input Handler
function addProjectRow(data = { title: '', desc: '', link: '' }) {
    const container = document.getElementById('projects-container');
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
    currentPortfolio = { ...SAMPLE_DATA, id: 'pf-' + Date.now() };
    const existingIdx = portfoliosDB.findIndex(p => p.id === currentPortfolio.id);
    if (existingIdx >= 0) portfoliosDB[existingIdx] = currentPortfolio;
    else portfoliosDB.unshift(currentPortfolio);
    savePortfoliosToStorage();

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
        id: currentPortfolio ? currentPortfolio.id : 'pf-' + Date.now(),
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

    currentPortfolio = formData;

    const existingIdx = portfoliosDB.findIndex(p => p.id === formData.id);
    if (existingIdx >= 0) portfoliosDB[existingIdx] = formData;
    else portfoliosDB.unshift(formData);

    savePortfoliosToStorage();
    showToast('Portfolio information saved!');
    navigateTo('templates');
}

function selectTemplate(tplName) {
    selectedTemplate = tplName;
    renderGeneratedPortfolio();
    navigateTo('preview');
}

function switchTemplateInPreview(tplName) {
    selectedTemplate = tplName;
    renderGeneratedPortfolio();
}

// Render Generated Portfolio Output
function renderGeneratedPortfolio() {
    if (!currentPortfolio) {
        currentPortfolio = SAMPLE_DATA;
    }

    const container = document.getElementById('portfolio-output');
    const data = currentPortfolio;

    ['simple', 'modern', 'creative'].forEach(t => {
        const btn = document.getElementById(`btn-tpl-${t}`);
        if (btn) {
            if (t === selectedTemplate) {
                btn.className = 'px-3 py-1.5 rounded-lg bg-white shadow text-slate-900 font-extrabold';
            } else {
                btn.className = 'px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900';
            }
        }
    });

    if (selectedTemplate === 'simple') {
        container.innerHTML = renderSimpleTemplate(data);
    } else if (selectedTemplate === 'modern') {
        container.innerHTML = renderModernTemplate(data);
    } else if (selectedTemplate === 'creative') {
        container.innerHTML = renderCreativeTemplate(data);
    }
}

// Template 1: Simple Minimalist Design
function renderSimpleTemplate(d) {
    return `
        <div class="p-8 sm:p-12 max-w-4xl mx-auto space-y-10 text-slate-800 bg-white font-serif">
            <div class="border-b border-slate-200 pb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-slate-900">${escapeHTML(d.fullName)}</h1>
                    <p class="text-lg font-sans text-sky-700 font-semibold mt-1">${escapeHTML(d.jobTitle)}</p>
                    <div class="flex flex-wrap gap-4 font-sans text-xs text-slate-500 mt-3">
                        ${d.email ? `<span><i class="fa-solid fa-envelope mr-1"></i>${escapeHTML(d.email)}</span>` : ''}
                        ${d.phone ? `<span><i class="fa-solid fa-phone mr-1"></i>${escapeHTML(d.phone)}</span>` : ''}
                        ${d.address ? `<span><i class="fa-solid fa-location-dot mr-1"></i>${escapeHTML(d.address)}</span>` : ''}
                    </div>
                </div>
                <img src="${escapeHTML(d.profilePic)}" onerror="this.src='https://placehold.co/120x120/e2e8f0/475569?text=Profile'" alt="Profile" class="w-24 h-24 rounded-full object-cover border-2 border-slate-200 shadow-sm shrink-0">
            </div>

            <div class="font-sans">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">About Me</h2>
                <p class="text-slate-700 leading-relaxed">${escapeHTML(d.aboutMe)}</p>
            </div>

            ${d.experience && d.experience.length ? `
            <div class="font-sans space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Work Experience</h2>
                <div class="space-y-6">
                    ${d.experience.map(exp => `
                        <div>
                            <div class="flex justify-between items-baseline">
                                <h3 class="font-bold text-slate-900">${escapeHTML(exp.role)} <span class="text-slate-400 font-normal">at ${escapeHTML(exp.company)}</span></h3>
                                <span class="text-xs text-slate-400">${escapeHTML(exp.duration)}</span>
                            </div>
                            <p class="text-sm text-slate-600 mt-1">${escapeHTML(exp.desc)}</p>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            ${d.projects && d.projects.length ? `
            <div class="font-sans space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Featured Projects</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    ${d.projects.map(p => `
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50">
                            <h3 class="font-bold text-slate-900 text-sm">${escapeHTML(p.title)}</h3>
                            <p class="text-xs text-slate-600 mt-1">${escapeHTML(p.desc)}</p>
                            ${p.link ? `<a href="${escapeHTML(p.link)}" target="_blank" class="inline-block mt-2 text-xs text-sky-600 font-semibold hover:underline">View Project →</a>` : ''}
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            <div class="font-sans grid sm:grid-cols-2 gap-8 border-t border-slate-200 pt-8">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Education</h2>
                    <p class="font-bold text-slate-900 text-sm">${escapeHTML(d.eduDegree || 'Degree Details')}</p>
                    <p class="text-xs text-slate-600">${escapeHTML(d.eduSchool || '')} • ${escapeHTML(d.eduYear || '')}</p>
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Skills</h2>
                    <div class="flex flex-wrap gap-1.5">
                        ${d.skills.map(s => `<span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded-md">${escapeHTML(s)}</span>`).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Template 2: Modern Sleek Card Layout
function renderModernTemplate(d) {
    return `
        <div class="bg-slate-950 text-slate-100 min-h-full font-jakarta p-6 sm:p-10">
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl flex flex-col md:flex-row items-center gap-8 mb-8">
                <img src="${escapeHTML(d.profilePic)}" onerror="this.src='https://placehold.co/150x150/1e293b/ffffff?text=User'" alt="Profile" class="w-32 h-32 rounded-2xl object-cover border-2 border-indigo-500 shadow-lg shadow-indigo-500/20">
                <div class="flex-1 text-center md:text-left space-y-2">
                    <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 rounded-full text-xs font-bold uppercase tracking-wider">Available for Hire</span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">${escapeHTML(d.fullName)}</h1>
                    <p class="text-indigo-400 font-semibold text-lg">${escapeHTML(d.jobTitle)}</p>
                    <p class="text-slate-400 text-sm max-w-2xl pt-1">${escapeHTML(d.aboutMe)}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 text-xs text-slate-400 pt-3">
                        ${d.email ? `<span class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700"><i class="fa-solid fa-envelope text-indigo-400"></i> ${escapeHTML(d.email)}</span>` : ''}
                        ${d.address ? `<span class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700"><i class="fa-solid fa-location-dot text-indigo-400"></i> ${escapeHTML(d.address)}</span>` : ''}
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="space-y-6">
                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-code"></i> Tech Stack
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            ${d.skills.map(s => `<span class="px-3 py-1.5 bg-slate-800 border border-slate-700 text-indigo-200 text-xs font-semibold rounded-lg shadow-sm">${escapeHTML(s)}</span>`).join('')}
                        </div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap"></i> Education
                        </h3>
                        <div class="space-y-1">
                            <p class="font-bold text-white text-sm">${escapeHTML(d.eduDegree || '')}</p>
                            <p class="text-xs text-slate-400">${escapeHTML(d.eduSchool || '')}</p>
                            <p class="text-xs text-indigo-400 font-mono mt-1">${escapeHTML(d.eduYear || '')}</p>
                        </div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-link"></i> Social Links
                        </h3>
                        <div class="space-y-2 text-xs">
                            ${d.socialGithub ? `<a href="${escapeHTML(d.socialGithub)}" target="_blank" class="flex items-center gap-2 text-slate-300 hover:text-indigo-400"><i class="fa-brands fa-github text-base"></i> GitHub Profile</a>` : ''}
                            ${d.socialLinkedin ? `<a href="${escapeHTML(d.socialLinkedin)}" target="_blank" class="flex items-center gap-2 text-slate-300 hover:text-indigo-400"><i class="fa-brands fa-linkedin text-base"></i> LinkedIn Profile</a>` : ''}
                            ${d.socialWebsite ? `<a href="${escapeHTML(d.socialWebsite)}" target="_blank" class="flex items-center gap-2 text-slate-300 hover:text-indigo-400"><i class="fa-solid fa-globe text-base"></i> Personal Website</a>` : ''}
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    ${d.experience && d.experience.length ? `
                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase"></i> Work Experience
                        </h3>
                        <div class="space-y-6">
                            ${d.experience.map(exp => `
                                <div class="border-l-2 border-indigo-500/50 pl-4 space-y-1 relative">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-white text-base">${escapeHTML(exp.role)}</h4>
                                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">${escapeHTML(exp.duration)}</span>
                                    </div>
                                    <p class="text-xs text-indigo-400 font-semibold">${escapeHTML(exp.company)}</p>
                                    <p class="text-xs text-slate-400 leading-relaxed pt-1">${escapeHTML(exp.desc)}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}

                    ${d.projects && d.projects.length ? `
                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-400 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-rocket"></i> Featured Projects
                        </h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            ${d.projects.map(p => `
                                <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-white text-sm">${escapeHTML(p.title)}</h4>
                                        <p class="text-xs text-slate-400 mt-1">${escapeHTML(p.desc)}</p>
                                    </div>
                                    ${p.link ? `<a href="${escapeHTML(p.link)}" target="_blank" class="mt-3 text-xs text-indigo-400 hover:text-indigo-300 font-bold flex items-center gap-1">Link <i class="fa-solid fa-external-link text-[10px]"></i></a>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Template 3: Vibrant Creative Design
function renderCreativeTemplate(d) {
    return `
        <div class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white p-6 sm:p-12 font-sans">
            <div class="creative-card text-slate-900 rounded-3xl p-8 sm:p-10 shadow-2xl mb-10 relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
                    <img src="${escapeHTML(d.profilePic)}" onerror="this.src='https://placehold.co/160x160/ec4899/ffffff?text=Artist'" alt="Creative Profile" class="w-36 h-36 rounded-3xl object-cover shadow-2xl border-4 border-white transform -rotate-3 hover:rotate-0 transition">
                    <div class="text-center md:text-left">
                        <span class="bg-pink-100 text-pink-700 text-xs font-black uppercase px-3 py-1 rounded-full tracking-wider">Creative Mind</span>
                        <h1 class="text-4xl sm:text-5xl font-black text-slate-900 mt-2 tracking-tight">${escapeHTML(d.fullName)}</h1>
                        <p class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">${escapeHTML(d.jobTitle)}</p>
                        <p class="text-slate-600 text-sm mt-3 max-w-xl leading-relaxed">${escapeHTML(d.aboutMe)}</p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="space-y-8">
                    <div class="creative-card text-slate-900 rounded-3xl p-6 shadow-xl space-y-4">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-pink-500"></i> Superpowers
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            ${d.skills.map(s => `<span class="px-3 py-1.5 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-bold rounded-xl shadow-sm">${escapeHTML(s)}</span>`).join('')}
                        </div>
                    </div>

                    <div class="creative-card text-slate-900 rounded-3xl p-6 shadow-xl space-y-4">
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-compass text-indigo-500"></i> Info & Location
                        </h3>
                        <div class="text-xs space-y-2 text-slate-600">
                            <p><strong>Education:</strong> ${escapeHTML(d.eduDegree || '')} (${escapeHTML(d.eduSchool || '')})</p>
                            <p><strong>Location:</strong> ${escapeHTML(d.address || 'Global')}</p>
                            <p><strong>Email:</strong> ${escapeHTML(d.email || '')}</p>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-8">
                    ${d.projects && d.projects.length ? `
                    <div class="creative-card text-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl">
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-palette text-purple-600"></i> Featured Work
                        </h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            ${d.projects.map(p => `
                                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-md hover:shadow-xl transition">
                                    <h4 class="font-black text-slate-900 text-base">${escapeHTML(p.title)}</h4>
                                    <p class="text-xs text-slate-600 mt-2">${escapeHTML(p.desc)}</p>
                                    ${p.link ? `<a href="${escapeHTML(p.link)}" target="_blank" class="inline-block mt-3 text-xs bg-pink-500 hover:bg-pink-600 text-white px-3 py-1.5 rounded-lg font-bold transition">Explore Project</a>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}

                    ${d.experience && d.experience.length ? `
                    <div class="creative-card text-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl">
                        <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-fire text-amber-500"></i> Experience Journey
                        </h3>
                        <div class="space-y-6">
                            ${d.experience.map(exp => `
                                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-2">
                                    <div class="flex justify-between items-center">
                                        <h4 class="font-bold text-slate-900 text-base">${escapeHTML(exp.role)}</h4>
                                        <span class="text-xs font-black bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full">${escapeHTML(exp.duration)}</span>
                                    </div>
                                    <p class="text-xs text-pink-600 font-bold">${escapeHTML(exp.company)}</p>
                                    <p class="text-xs text-slate-600 leading-relaxed">${escapeHTML(exp.desc)}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Render Saved Portfolios List (CRUD UI)
function renderManageList() {
    const container = document.getElementById('manage-list-container');
    if (!container) return;

    if (portfoliosDB.length === 0) {
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

    container.innerHTML = portfoliosDB.map(p => `
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <img src="${escapeHTML(p.profilePic)}" onerror="this.src='https://placehold.co/100x100/e2e8f0/475569?text=User'" class="w-14 h-14 rounded-full object-cover border border-slate-200">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">${escapeHTML(p.fullName)}</h3>
                    <p class="text-sm text-sky-600 font-semibold">${escapeHTML(p.jobTitle)}</p>
                    <p class="text-xs text-slate-400 mt-1">${escapeHTML(p.email)} • ${p.skills.slice(0, 3).join(', ')}</p>
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
    const found = portfoliosDB.find(p => p.id === id);
    if (found) {
        currentPortfolio = found;
        renderGeneratedPortfolio();
        navigateTo('preview');
    }
}

function editPortfolioFromManage(id) {
    const found = portfoliosDB.find(p => p.id === id);
    if (found) {
        currentPortfolio = found;
        populateFormWithData(found);
        navigateTo('form');
    }
}

function editCurrentPortfolio() {
    if (currentPortfolio) {
        populateFormWithData(currentPortfolio);
        navigateTo('form');
    }
}

function deletePortfolioFromManage(id) {
    if (confirm('Are you sure you want to delete this portfolio from the database?')) {
        portfoliosDB = portfoliosDB.filter(p => p.id !== id);
        savePortfoliosToStorage();
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

// Canvas Picture Generator
function generatePortfolioPicture() {
    const d = currentPortfolio || SAMPLE_DATA;
    const canvas = document.getElementById('exportCanvas');
    const ctx = canvas.getContext('2d');

    canvas.width = 800;
    canvas.height = 500;

    const grad = ctx.createLinearGradient(0, 0, 800, 500);
    if (selectedTemplate === 'modern') {
        grad.addColorStop(0, '#0f172a');
        grad.addColorStop(1, '#1e1b4b');
    } else if (selectedTemplate === 'creative') {
        grad.addColorStop(0, '#4c1d95');
        grad.addColorStop(0.5, '#7e22ce');
        grad.addColorStop(1, '#be185d');
    } else {
        grad.addColorStop(0, '#0284c7');
        grad.addColorStop(1, '#0369a1');
    }
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 800, 500);

    ctx.fillStyle = selectedTemplate === 'modern' ? 'rgba(30, 41, 59, 0.9)' : 'rgba(255, 255, 255, 0.95)';
    ctx.shadowColor = 'rgba(0, 0, 0, 0.25)';
    ctx.shadowBlur = 20;
    ctx.beginPath();
    ctx.roundRect(40, 40, 720, 420, 24);
    ctx.fill();
    ctx.shadowBlur = 0;

    const textColor = selectedTemplate === 'modern' ? '#ffffff' : '#0f172a';
    const subTextColor = selectedTemplate === 'modern' ? '#818cf8' : '#0284c7';

    ctx.fillStyle = textColor;
    ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
    ctx.fillText(d.fullName || 'User Name', 80, 110);

    ctx.fillStyle = subTextColor;
    ctx.font = 'bold 20px "Inter", sans-serif';
    ctx.fillText(d.jobTitle || 'Professional Role', 80, 145);

    ctx.fillStyle = selectedTemplate === 'modern' ? '#94a3b8' : '#475569';
    ctx.font = '14px "Inter", sans-serif';
    const shortBio = (d.aboutMe || '').substring(0, 90) + '...';
    ctx.fillText(shortBio, 80, 185);

    ctx.strokeStyle = selectedTemplate === 'modern' ? '#334155' : '#e2e8f0';
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

        ctx.fillStyle = selectedTemplate === 'modern' ? '#312e81' : '#e0f2fe';
        ctx.beginPath();
        ctx.roundRect(skillX, skillY, badgeWidth, 26, 8);
        ctx.fill();

        ctx.fillStyle = selectedTemplate === 'modern' ? '#c7d2fe' : '#0369a1';
        ctx.fillText(skill, skillX + 12, skillY + 17);

        skillX += badgeWidth + 10;
    });

    ctx.fillStyle = selectedTemplate === 'modern' ? '#64748b' : '#94a3b8';
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

// Utilities
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[tag] || tag));
}

function showToast(message) {
    const toast = document.getElementById('toast');
    const msgSpan = document.getElementById('toast-message');
    if (toast && msgSpan) {
        msgSpan.innerText = message;
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }
}
