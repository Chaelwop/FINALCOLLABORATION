/**
 * PortfolioCraft front-end runtime.
 *
 * Small, dependency free modules that power the interface: theme switching,
 * navigation, toasts, modals, the portfolio editor and the export card.
 */

const PortfolioCraft = (() => {
    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

    const csrfToken = () => $('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

    const escapeHtml = (value) =>
        String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
        })[character] ?? character);

    /* ------------------------------------------------------------------ Theme */
    const Theme = {
        init() {
            // The interface is light only, so any previously stored preference is cleared.
            localStorage.removeItem('portfoliocraft-theme');
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        },
    };

    /* -------------------------------------------------------------- Dropdowns */
    const Dropdown = {
        init() {
            $$('[data-dropdown]').forEach((dropdown) => {
                const toggle = $('[data-dropdown-toggle]', dropdown);
                const menu = $('[data-dropdown-menu]', dropdown);
                if (!toggle || !menu) {
                    return;
                }

                const close = () => {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                };

                toggle.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const isOpen = !menu.classList.contains('hidden');
                    menu.classList.toggle('hidden', isOpen);
                    toggle.setAttribute('aria-expanded', String(!isOpen));
                });

                document.addEventListener('click', (event) => {
                    if (!dropdown.contains(event.target)) {
                        close();
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        close();
                    }
                });
            });
        },
    };

    /* ------------------------------------------------------------ Mobile menu */
    const MobileMenu = {
        init() {
            const toggle = $('[data-mobile-menu-toggle]');
            const menu = $('#mobile-menu');
            const backdrop = $('#mobile-menu-backdrop');
            if (!menu) {
                return;
            }

            const close = () => {
                menu.classList.add('translate-x-full');
                setTimeout(() => menu.classList.add('hidden'), 300);
                backdrop?.classList.add('opacity-0');
                setTimeout(() => backdrop?.classList.add('hidden'), 300);
                document.body.classList.remove('overflow-hidden');
            };

            const open = () => {
                menu.classList.remove('hidden');
                requestAnimationFrame(() => menu.classList.remove('translate-x-full'));
                backdrop?.classList.remove('hidden');
                requestAnimationFrame(() => backdrop?.classList.remove('opacity-0'));
                document.body.classList.add('overflow-hidden');
            };

            document.querySelectorAll('[data-mobile-menu-toggle]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const isOpen = !menu.classList.contains('hidden') && !menu.classList.contains('translate-x-full');
                    if (isOpen) {
                        close();
                    } else {
                        open();
                    }
                });
            });

            backdrop?.addEventListener('click', close);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    close();
                }
            });
        },
    };

    /* ------------------------------------------------------------- Header scroll */
    const Header = {
        init() {
            const header = $('[data-header]');
            if (!header) {
                return;
            }

            const update = () => {
                header.classList.toggle('shadow-sm', window.scrollY > 8);
            };

            window.addEventListener('scroll', update, { passive: true });
            update();
        },
    };

    /* ---------------------------------------------------------- Password toggle */
    const PasswordToggle = {
        init() {
            $$('[data-password-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.passwordToggle);
                    if (!input) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    $('[data-eye-open]', button)?.classList.toggle('hidden', isHidden);
                    $('[data-eye-closed]', button)?.classList.toggle('hidden', !isHidden);
                    button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                });
            });
        },
    };

    /* ---------------------------------------------------------- Loading button */
    const LoadingButton = {
        init() {
            $$('[data-loading-button]').forEach((button) => {
                const start = () => {
                    if (button.dataset.busy === 'true') {
                        return;
                    }

                    button.dataset.busy = 'true';
                    button.classList.add('pointer-events-none', 'opacity-70');

                    const label = $('[data-loading-label]', button);
                    if (label) {
                        label.dataset.originalLabel = label.textContent;
                        label.textContent = 'Working…';
                    }
                };

                if (button.type === 'submit' && button.form) {
                    button.form.addEventListener('submit', start);
                } else {
                    button.addEventListener('click', start);
                }
            });
        },

        reset(button) {
            if (!button) {
                return;
            }

            button.dataset.busy = 'false';
            button.classList.remove('pointer-events-none', 'opacity-70');

            const label = $('[data-loading-label]', button);
            if (label && label.dataset.originalLabel) {
                label.textContent = label.dataset.originalLabel;
            }
        },
    };

    /* ----------------------------------------------------------------- Toasts */
    const Toast = {
        show(message, type = 'info', timeout = 4000) {
            const stack = $('#toast-stack');
            if (!stack) {
                return;
            }

            const tones = {
                success: 'border-slate-200 bg-white text-slate-900',
                error: 'border-slate-200 bg-white text-slate-900',
                info: 'border-slate-200 bg-white text-slate-900',
            };

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-start gap-2.5 rounded-xl border px-3.5 py-2.5 text-sm shadow-lift backdrop-blur transition-all duration-300 translate-y-2 opacity-0 ${tones[type] ?? tones.info}`;
            toast.innerHTML = `
                <span class="flex-1">${escapeHtml(message)}</span>
                <button type="button" class="shrink-0 text-slate-400 transition hover:text-slate-600" aria-label="Dismiss">✕</button>
            `;

            const remove = () => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 250);
            };

            $('button', toast).addEventListener('click', remove);
            stack.appendChild(toast);

            requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
            setTimeout(remove, timeout);
        },
    };

    /* ----------------------------------------------------------------- Modals */
    const Modal = {
        open(id) {
            const modal = document.getElementById(id);
            if (!modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            const panel = $('[data-modal-panel]', modal);
            requestAnimationFrame(() => {
                panel?.classList.remove('scale-95', 'opacity-0');
            });
        },

        close(id) {
            const modal = id ? document.getElementById(id) : $('[data-modal].flex');
            if (!modal) {
                return;
            }

            const panel = $('[data-modal-panel]', modal);
            panel?.classList.add('scale-95', 'opacity-0');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        },
    };

    /* ------------------------------------------------------------- Copy links */
    const CopyLink = {
        init() {
            $$('[data-copy-link]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const url = button.dataset.copyLink;
                    if (!url) {
                        Toast.show('Publish your portfolio first to get a shareable link.', 'info');
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(url);
                        Toast.show('Public link copied to your clipboard.');
                    } catch (error) {
                        Toast.show('Could not copy the link. Copy it from the address bar instead.', 'error');
                    }
                });
            });
        },
    };

    /* --------------------------------------------------------- Reveal on scroll */
    const Reveal = {
        init() {
            const targets = $$('.reveal, .stagger');
            if (!targets.length) {
                return;
            }

            if (!('IntersectionObserver' in window)) {
                targets.forEach((target) => target.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

            targets.forEach((target) => observer.observe(target));
        },
    };

    /* ------------------------------------------------------------------ Forms */
    const api = {
        async request(url, method = 'GET', body = null) {
            const response = await fetch(url, {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                    ...(body ? { 'Content-Type': 'application/json' } : {}),
                },
                body: body ? JSON.stringify(body) : null,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                const fields = payload?.errors ?? {};
                const firstError = Object.values(fields).flat().find(Boolean);
                const error = new Error(firstError || payload?.message || 'Something went wrong. Please try again.');
                error.fields = fields;
                throw error;
            }

            return payload;
        },
    };

    /* ----------------------------------------------------------------- Editor */
    const Editor = {
        template: 'simple',
        previewTimer: null,

        init() {
            const form = $('#portfolio-form');
            if (!form) {
                return;
            }

            this.form = form;
            this.templateInput = $('[data-template-input]', form);
            this.template = this.templateInput?.value ?? 'simple';

            this.initSkills();
            this.initRows();
            this.initTemplates();
            this.initLivePreview();
            this.initSave();
            this.initDemo();
            this.initFieldErrors();
            this.restoreDraft();
        },

        /* Field errors --------------------------------------------------- */
        initFieldErrors() {
            $$('[name]', this.form).forEach((input) => {
                input.addEventListener('input', () => {
                    const wrapper = input.closest('div.w-full') ?? input.parentElement;

                    wrapper?.querySelector('p.field-error[data-client]')?.remove();
                    input.classList.remove('input-error');
                });
            });
        },

        /* Skills ---------------------------------------------------------- */
        initSkills() {
            const wrapper = $('[data-skill-input]');
            if (!wrapper) {
                return;
            }

            const field = $('[data-skill-field]', wrapper);
            const list = $('[data-skill-list]', wrapper);
            const values = $('[data-skill-values]', wrapper);

            const sync = () => {
                values.value = $$('[data-skill]', list)
                    .map((chip) => chip.dataset.skill)
                    .join(',');
                list.classList.toggle('hidden', list.children.length === 0);
                this.schedulePreview();
            };

            const add = (raw) => {
                const skill = raw.trim().replace(/,$/, '');
                if (!skill) {
                    return;
                }

                if ($$('[data-skill]', list).some((chip) => chip.dataset.skill.toLowerCase() === skill.toLowerCase())) {
                    field.value = '';
                    return;
                }

                const chip = document.createElement('span');
                chip.dataset.skill = skill;
                chip.className = 'inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100 ring-inset';
                chip.innerHTML = `<span>${escapeHtml(skill)}</span><button type="button" class="text-brand-400 transition hover:text-brand-700" aria-label="Remove ${escapeHtml(skill)}">✕</button>`;

                $('button', chip).addEventListener('click', () => {
                    chip.remove();
                    sync();
                });

                list.appendChild(chip);
                field.value = '';
                sync();
            };

            (values.value ? values.value.split(',') : [])
                .map((skill) => skill.trim())
                .filter(Boolean)
                .forEach(add);

            field.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ',') {
                    event.preventDefault();
                    add(field.value);
                }

                if (event.key === 'Backspace' && field.value === '' && list.lastElementChild) {
                    list.lastElementChild.remove();
                    sync();
                }
            });

            field.addEventListener('blur', () => {
                if (field.value.trim()) {
                    add(field.value);
                }
            });
        },

        /* Repeatable rows ------------------------------------------------ */
        initRows() {
            $('[data-add-project]')?.addEventListener('click', () => {
                this.addRow('projects');
            });

            $('[data-add-experience]')?.addEventListener('click', () => {
                this.addRow('experience');
            });

            $$('[data-remove-row]').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('[data-project-row], [data-experience-row]');
                    row?.remove();
                    this.reindex();
                    this.schedulePreview();
                });
            });

            this.updateEmptyHints();
        },

        addRow(kind) {
            const list = $(kind === 'projects' ? '[data-project-list]' : '[data-experience-list]');
            if (!list) {
                return;
            }

            const index = list.children.length;
            const row = document.createElement('div');
            row.className = 'rounded-xl border border-slate-200 bg-slate-50/70 p-4';
            row.setAttribute(kind === 'projects' ? 'data-project-row' : 'data-experience-row', '');

            const fields = kind === 'projects'
                ? [
                    ['title', 'Title', 'text', 'CloudMetrics Dashboard'],
                    ['link', 'Link', 'url', 'https://github.com/…'],
                    ['desc', 'Description', 'textarea', 'What did you build and why does it matter?'],
                ]
                : [
                    ['role', 'Role', 'text', 'Senior Frontend Developer'],
                    ['company', 'Company', 'text', 'TechNova Solutions'],
                    ['duration', 'Duration', 'text', '2022 - Present'],
                    ['desc', 'Highlights', 'textarea', 'What did you achieve?'],
                ];

            const grid = kind === 'projects' ? 'sm:grid-cols-2' : 'sm:grid-cols-3';

            row.innerHTML = `
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wide text-slate-400 uppercase">${kind === 'projects' ? 'Project' : 'Role'} ${index + 1}</span>
                    <button type="button" data-remove-row class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50">✕</button>
                </div>
                <div class="grid gap-3 ${grid}">
                    ${fields.map(([name, label, type, placeholder]) => `
                        <div class="${name === 'desc' ? 'sm:col-span-3' : ''}">
                            <label class="label">${label}</label>
                            ${type === 'textarea'
                                ? `<textarea rows="2" name="${kind}[${index}][${name}]" placeholder="${placeholder}" class="input"></textarea>`
                                : `<input type="${type}" name="${kind}[${index}][${name}]" placeholder="${placeholder}" class="input">`}
                        </div>
                    `).join('')}
                </div>
            `;

            $('[data-remove-row]', row).addEventListener('click', () => {
                row.remove();
                this.reindex();
                this.schedulePreview();
            });

            list.appendChild(row);
            this.updateEmptyHints();
            this.schedulePreview();

            $('input, textarea', row)?.focus();
        },

        reindex() {
            const reindexList = (list, kind) => {
                if (!list) {
                    return;
                }

                Array.from(list.children).forEach((row, index) => {
                    $$('[name]', row).forEach((input) => {
                        input.name = input.name.replace(new RegExp(`^${kind}\\[\\d+\\]`), `${kind}[${index}]`);
                    });
                });
            };

            reindexList($('[data-project-list]'), 'projects');
            reindexList($('[data-experience-list]'), 'experience');
            this.updateEmptyHints();
        },

        updateEmptyHints() {
            ['projects', 'experience'].forEach((kind) => {
                const list = $(`[data-${kind}-list]`);
                const hint = $(`[data-empty-hint="${kind}"]`);
                if (!list || !hint) {
                    return;
                }

                hint.classList.toggle('hidden', list.children.length > 0);
            });
        },

        /* Templates ------------------------------------------------------ */
        initTemplates() {
            $$('[data-template-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    this.setTemplate(option.dataset.templateOption);
                });
            });
        },

        setTemplate(template) {
            this.template = template;
            if (this.templateInput) {
                this.templateInput.value = template;
            }

            $$('[data-template-option]').forEach((option) => {
                const isActive = option.dataset.templateOption === template;
                option.classList.toggle('ring-2', isActive);
                option.classList.toggle('ring-brand-500', isActive);
                option.classList.toggle('ring-offset-2', isActive);
                option.classList.toggle('ring-offset-white', isActive);
            });

            this.schedulePreview();
        },

        /* Live preview --------------------------------------------------- */
        initLivePreview() {
            this.form.addEventListener('input', () => {
                this.schedulePreview();
                this.scheduleDraft();
            });
            this.form.addEventListener('change', () => {
                this.schedulePreview();
                this.scheduleDraft();
            });
        },

        scheduleDraft() {
            window.clearTimeout(this.draftTimer);
            this.draftTimer = window.setTimeout(() => {
                const draft = this.collect();
                const simple = {};
                Object.entries(draft).forEach(([key, value]) => {
                    if (typeof value === 'string') {
                        simple[key] = value;
                    }
                });

                localStorage.setItem('portfoliocraft-draft', JSON.stringify(simple));
            }, 900);
        },

        schedulePreview() {
            window.clearTimeout(this.previewTimer);
            this.previewTimer = window.setTimeout(() => this.renderPreview(), 400);
        },

        async renderPreview() {
            const canvas = $('[data-preview-canvas]');
            if (!canvas) {
                return;
            }

            const endpoint = this.form?.dataset.previewEndpoint ?? '/portfolios/preview';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: JSON.stringify({
                        template: this.template,
                        portfolio: this.collect(),
                    }),
                });

                if (!response.ok) {
                    return;
                }

                canvas.innerHTML = await response.text();
            } catch (error) {
                // A failed preview should never interrupt editing.
            }
        },

        /* Payload -------------------------------------------------------- */
        collect() {
            const form = this.form;
            const value = (name) => $(`[name="${name}"]`, form)?.value.trim() ?? '';

            const projects = $$('[data-project-row]', form).map((row) => ({
                title: $('[name$="[title]"]', row)?.value.trim() ?? '',
                link: $('[name$="[link]"]', row)?.value.trim() ?? '',
                desc: $('[name$="[desc]"]', row)?.value.trim() ?? '',
            })).filter((project) => project.title);

            const experience = $$('[data-experience-row]', form).map((row) => ({
                role: $('[name$="[role]"]', row)?.value.trim() ?? '',
                company: $('[name$="[company]"]', row)?.value.trim() ?? '',
                duration: $('[name$="[duration]"]', row)?.value.trim() ?? '',
                desc: $('[name$="[desc]"]', row)?.value.trim() ?? '',
            })).filter((job) => job.role || job.company);

            const skills = ($('[data-skill-values]', form)?.value ?? '')
                .split(',')
                .map((skill) => skill.trim())
                .filter(Boolean);

            return {
                fullName: value('fullName'),
                jobTitle: value('jobTitle'),
                email: value('email'),
                phone: value('phone'),
                address: value('address'),
                profilePic: value('profilePic'),
                aboutMe: value('aboutMe'),
                eduSchool: value('eduSchool'),
                eduDegree: value('eduDegree'),
                eduYear: value('eduYear'),
                skills,
                projects,
                experience,
                socialGithub: value('socialGithub'),
                socialLinkedin: value('socialLinkedin'),
                socialWebsite: value('socialWebsite'),
                template: this.template,
                isPublic: $('[name="isPublic"]', form)?.checked ?? false,
            };
        },

        /* Saving --------------------------------------------------------- */
        initSave() {
            $$('[data-save-portfolio]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const status = $('[data-save-status]');
                    if (status) {
                        status.textContent = 'Saving…';
                    }

                    try {
                        const payload = this.collect();
                        const method = button.dataset.method === 'PUT' ? 'PUT' : 'POST';
                        await api.request(button.dataset.url, method, payload);

                        if (status) {
                            status.textContent = `Saved ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
                        }

                        localStorage.removeItem('portfoliocraft-draft');
                        Toast.show(method === 'PUT' ? 'Portfolio updated.' : 'Portfolio created — nice work!');

                        if (method === 'POST') {
                            window.location.assign('/dashboard');
                        }
                    } catch (error) {
                        this.showFieldErrors(error.fields ?? {});

                        if (status) {
                            status.textContent = 'Could not save — check the highlighted fields';
                        }
                        Toast.show(error.message, 'error');
                    } finally {
                        LoadingButton.reset(button);
                    }
                });
            });
        },

        /* Field errors --------------------------------------------------- */
        showFieldErrors(fields) {
            const form = this.form;

            if (!form) {
                return;
            }

            $$('[name]', form).forEach((input) => {
                const wrapper = input.closest('div.w-full') ?? input.parentElement;
                const previous = wrapper?.querySelector('p.field-error[data-client]');

                previous?.remove();
                input.classList.remove('input-error');

                const message = fields[input.name]?.[0];

                if (!message || !wrapper) {
                    return;
                }

                input.classList.add('input-error');

                const paragraph = document.createElement('p');
                paragraph.className = 'field-error';
                paragraph.dataset.client = 'true';
                paragraph.textContent = message;

                wrapper.appendChild(paragraph);
            });

            const firstInvalid = $('[name].input-error', form);

            firstInvalid?.focus();
        },

        /* Demo data ------------------------------------------------------ */
        initDemo() {
            $('[data-fill-demo]')?.addEventListener('click', () => {
                const demo = {
                    fullName: 'Sarah Jenkins',
                    jobTitle: 'Full-Stack Software Engineer',
                    email: 'sarah.j@example.com',
                    phone: '+1 (555) 382-9102',
                    address: 'Seattle, WA, USA',
                    profilePic: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400',
                    aboutMe: 'Passionate and results-driven engineer with 5+ years of experience crafting modern, scalable web applications. Dedicated to clean architecture, delightful user experiences and robust cloud services.',
                    eduSchool: 'University of Washington',
                    eduDegree: 'B.S. Software Engineering',
                    eduYear: '2016 - 2020',
                    skills: ['React.js', 'Node.js', 'TypeScript', 'Tailwind CSS', 'PostgreSQL', 'GraphQL', 'AWS', 'Docker'],
                    socialGithub: 'https://github.com',
                    socialLinkedin: 'https://linkedin.com',
                    socialWebsite: 'https://example.com',
                    projects: [
                        { title: 'CloudMetrics Dashboard', desc: 'Real-time analytics tool for cloud infrastructure monitoring.', link: 'https://github.com/sample/metrics' },
                        { title: 'TaskFlow Workspace', desc: 'Collaborative task management tool with drag-and-drop Kanban boards.', link: 'https://github.com/sample/taskflow' },
                    ],
                    experience: [
                        { company: 'TechNova Solutions', role: 'Senior Frontend Developer', duration: '2022 - Present', desc: 'Led a team of four engineers migrating a monolith to React and TypeScript micro-frontends, boosting delivery speed by 40%.' },
                        { company: 'Apex Digital', role: 'Full-Stack Engineer', duration: '2020 - 2022', desc: 'Developed scalable RESTful APIs and modern frontend interfaces using Node.js and React.' },
                    ],
                };

                Object.entries(demo).forEach(([key, val]) => {
                    const field = $(`[name="${key}"]`, this.form);
                    if (field && typeof val === 'string') {
                        field.value = val;
                    }
                });

                const skillsField = $('[data-skill-values]', this.form);
                const skillList = $('[data-skill-list]', this.form);
                if (skillsField && skillList) {
                    skillsField.value = demo.skills.join(',');
                    skillList.innerHTML = '';
                    demo.skills.forEach((skill) => {
                        const chip = document.createElement('span');
                        chip.dataset.skill = skill;
                        chip.className = 'inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-100 ring-inset';
                        chip.innerHTML = `<span>${escapeHtml(skill)}</span><button type="button" class="text-brand-400 transition hover:text-brand-700" aria-label="Remove">✕</button>`;
                        $('button', chip).addEventListener('click', () => {
                            chip.remove();
                            skillsField.value = $$('[data-skill]', skillList).map((item) => item.dataset.skill).join(',');
                        });
                        skillList.appendChild(chip);
                    });
                }

                const projectList = $('[data-project-list]', this.form);
                if (projectList) {
                    projectList.innerHTML = '';
                    demo.projects.forEach(() => this.addRow('projects'));
                    $$('[data-project-row]', projectList).forEach((row, index) => {
                        $('[name$="[title]"]', row).value = demo.projects[index].title;
                        $('[name$="[desc]"]', row).value = demo.projects[index].desc;
                        $('[name$="[link]"]', row).value = demo.projects[index].link;
                    });
                }

                const experienceList = $('[data-experience-list]', this.form);
                if (experienceList) {
                    experienceList.innerHTML = '';
                    demo.experience.forEach(() => this.addRow('experience'));
                    $$('[data-experience-row]', experienceList).forEach((row, index) => {
                        $('[name$="[role]"]', row).value = demo.experience[index].role;
                        $('[name$="[company]"]', row).value = demo.experience[index].company;
                        $('[name$="[duration]"]', row).value = demo.experience[index].duration;
                        $('[name$="[desc]"]', row).value = demo.experience[index].desc;
                    });
                }

                this.updateEmptyHints();
                this.schedulePreview();
                Toast.show('Demo content loaded — edit anything you like.', 'info');
            });
        },

        /* Draft persistence ----------------------------------------------- */
        restoreDraft() {
            const stored = localStorage.getItem('portfoliocraft-draft');
            if (!stored || this.form.querySelector('[name="fullName"]')?.value) {
                return;
            }

            try {
                const draft = JSON.parse(stored);
                Object.entries(draft).forEach(([key, val]) => {
                    const field = $(`[name="${key}"]`, this.form);
                    if (field && typeof val === 'string') {
                        field.value = val;
                    }
                });
                Toast.show('Restored your unsaved draft.', 'info');
            } catch (error) {
                localStorage.removeItem('portfoliocraft-draft');
            }
        },
    };

    /* --------------------------------------------------------- Dashboard actions */
    const Dashboard = {
        init() {
            const grid = $('#portfolio-grid');
            const search = $('#portfolio-search');
            const chips = $$('[data-filter]');

            const applyFilter = () => {
                const query = (search?.value ?? '').trim().toLowerCase();
                const mode = document.querySelector('[data-filter].is-active')?.dataset.filter ?? 'all';

                $$('#portfolio-grid [data-portfolio-card]').forEach((card) => {
                    const title = (card.dataset.title ?? '').toLowerCase();
                    const template = card.dataset.template ?? '';
                    const isPublic = card.dataset.public === '1';

                    let visible = true;
                    if (query && !title.includes(query)) {
                        visible = false;
                    }
                    if (mode === 'published' && !isPublic) {
                        visible = false;
                    }
                    if (mode === 'draft' && isPublic) {
                        visible = false;
                    }

                    card.classList.toggle('hidden', !visible);
                });

                const visibleCount = $$('#portfolio-grid [data-portfolio-card]').filter((card) => !card.classList.contains('hidden')).length;
                const empty = document.querySelector('[data-empty-state]');
                if (empty) {
                    empty.classList.toggle('hidden', visibleCount > 0);
                }
            };

            search?.addEventListener('input', applyFilter);

            chips.forEach((chip) => {
                chip.addEventListener('click', () => {
                    chips.forEach((c) => c.classList.remove('is-active'));
                    chip.classList.add('is-active');
                    applyFilter();
                });
            });

        },
    };

    /* ------------------------------------------------------------ Preview page */
    const Preview = {
        init() {
            const canvas = $('[data-preview-canvas]');
            const dataNode = $('#portfolio-data');

            $$('[data-preview-template]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const template = button.dataset.previewTemplate;
                    if (!canvas) {
                        return;
                    }

                    $$('[data-preview-template]').forEach((item) => {
                        const isActive = item === button;
                        item.classList.toggle('bg-white', isActive);
                        item.classList.toggle('text-slate-900', isActive);
                        item.classList.toggle('shadow-sm', isActive);
                        item.classList.toggle('text-slate-500', !isActive);
                    });

                    try {
                        const response = await fetch('/portfolios/preview', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'text/html',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken(),
                            },
                            body: JSON.stringify({
                                template,
                                portfolio: dataNode ? JSON.parse(dataNode.textContent) : {},
                            }),
                        });

                        if (response.ok) {
                            canvas.innerHTML = await response.text();
                            this.persistTemplate(template, dataNode);
                        }
                    } catch (error) {
                        // Keep the current render when the request fails.
                    }
                });
            });

            Export.init();
        },

        /**
         * Store the selected template on the portfolio so it is used everywhere.
         */
        async persistTemplate(template, dataNode) {
            const canvas = $('[data-preview-canvas]');
            const id = canvas?.dataset.portfolioId;
            if (!id || !dataNode) {
                return;
            }

            const payload = { ...JSON.parse(dataNode.textContent), template };

            try {
                await api.request(`/api/portfolios/${id}`, 'PUT', payload);
                Toast.show(`Switched to the ${template} template.`);
            } catch (error) {
                Toast.show('The preview changed but the template could not be saved.', 'error');
            }
        },
    };

    /* ------------------------------------------------------------- PNG export */
    const Export = {
        init() {
            $$('[data-export-card]').forEach((button) => {
                button.addEventListener('click', () => {
                    this.render(button);
                    Modal.open('export-modal');
                });
            });
        },

        render(button) {
            const canvas = $('[data-export-canvas]');
            if (!canvas) {
                return;
            }

            const context = canvas.getContext('2d');
            const template = button.dataset.template ?? 'simple';
            const width = 1200;
            const height = 750;

            canvas.width = width;
            canvas.height = height;

            const gradient = context.createLinearGradient(0, 0, width, height);
            if (template === 'modern') {
                gradient.addColorStop(0, '#040916');
                gradient.addColorStop(1, '#071a35');
            } else if (template === 'creative') {
                gradient.addColorStop(0, '#241a5c');
                gradient.addColorStop(0.55, '#5b21b6');
                gradient.addColorStop(1, '#0962c2');
            } else {
                gradient.addColorStop(0, '#0962c2');
                gradient.addColorStop(1, '#1875d7');
            }

            context.fillStyle = gradient;
            context.fillRect(0, 0, width, height);

            context.fillStyle = 'rgba(255,255,255,0.08)';
            context.beginPath();
            context.arc(width - 120, 120, 220, 0, Math.PI * 2);
            context.fill();

            context.save();
            context.shadowColor = 'rgba(0,0,0,0.25)';
            context.shadowBlur = 40;
            context.fillStyle = 'rgba(255,255,255,0.96)';
            this.roundRect(context, 60, 60, width - 120, height - 120, 36);
            context.fill();
            context.restore();

            const dark = template === 'modern';
            const text = dark ? '#ffffff' : '#0f172a';
            const accent = dark ? '#7ebce2' : '#0962c2';
            const muted = dark ? '#94a3b8' : '#64748b';

            context.fillStyle = text;
            context.font = 'bold 58px "Plus Jakarta Sans", sans-serif';
            context.fillText(this.truncate(button.dataset.name ?? 'Your Name', 26), 120, 190);

            context.fillStyle = accent;
            context.font = 'bold 30px "Instrument Sans", sans-serif';
            context.fillText(this.truncate(button.dataset.title ?? 'Your Role', 40), 120, 240);

            context.strokeStyle = dark ? 'rgba(255,255,255,0.12)' : 'rgba(15,23,42,0.08)';
            context.lineWidth = 2;
            context.beginPath();
            context.moveTo(120, 290);
            context.lineTo(width - 120, 290);
            context.stroke();

            context.fillStyle = muted;
            context.font = '24px "Instrument Sans", sans-serif';
            context.fillText('CORE SKILLS', 120, 350);

            const skills = (button.dataset.skills ?? '').split(',').map((skill) => skill.trim()).filter(Boolean).slice(0, 7);
            let x = 120;
            let y = 385;

            skills.forEach((skill) => {
                context.font = 'bold 24px "Instrument Sans", sans-serif';
                const badgeWidth = context.measureText(skill).width + 40;

                if (x + badgeWidth > width - 120) {
                    x = 120;
                    y += 58;
                }

                context.fillStyle = dark ? 'rgba(9,96,194,0.25)' : 'rgba(9,96,194,0.1)';
                this.roundRect(context, x, y, badgeWidth, 44, 14);
                context.fill();

                context.fillStyle = dark ? '#a5d8f0' : '#0b4d9e';
                context.fillText(skill, x + 20, y + 29);
                x += badgeWidth + 14;
            });

            context.fillStyle = muted;
            context.font = '22px "Instrument Sans", sans-serif';
            const contact = [button.dataset.email, button.dataset.location].filter(Boolean).join('   •   ');
            context.fillText(this.truncate(contact, 60), 120, height - 100);

            context.fillStyle = accent;
            context.font = 'bold 22px "Instrument Sans", sans-serif';
            context.fillText('PortfolioCraft', width - 300, height - 100);

            const link = $('[data-export-download]');
            if (link) {
                link.href = canvas.toDataURL('image/png');
            }
        },

        roundRect(context, x, y, width, height, radius) {
            context.beginPath();
            if (typeof context.roundRect === 'function') {
                context.roundRect(x, y, width, height, radius);
                return;
            }

            context.moveTo(x + radius, y);
            context.arcTo(x + width, y, x + width, y + height, radius);
            context.arcTo(x + width, y + height, x, y + height, radius);
            context.arcTo(x, y + height, x, y, radius);
            context.arcTo(x, y, x + width, y, radius);
            context.closePath();
        },

        truncate(value, length) {
            const text = String(value ?? '');
            return text.length > length ? `${text.slice(0, length - 1)}…` : text;
        },
    };

    /* ------------------------------------------------------------------- Boot */
    function boot() {
        Theme.init();
        Dropdown.init();
        MobileMenu.init();
        Header.init();
        PasswordToggle.init();
        LoadingButton.init();
        CopyLink.init();
        Reveal.init();
        Dashboard.init();
        Editor.init();
        Preview.init();

        document.addEventListener('click', (event) => {
            const closeButton = event.target.closest('[data-modal-close]');
            if (closeButton) {
                Modal.close(closeButton.dataset.modalClose);
                return;
            }

            const overlay = event.target.closest('[data-modal]');
            if (overlay && event.target === overlay) {
                Modal.close(overlay.id);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                Modal.close();
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    return { toast: Toast.show, openModal: Modal.open, closeModal: Modal.close };
})();

window.PortfolioCraft = PortfolioCraft;
