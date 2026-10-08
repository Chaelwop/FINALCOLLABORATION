import { escapeHTML } from '../utils/helpers.js';

export function renderSimpleTemplate(d) {
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

export function renderModernTemplate(d) {
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

export function renderCreativeTemplate(d) {
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
