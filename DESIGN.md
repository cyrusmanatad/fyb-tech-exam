# DESIGN.md — Benta Door UI/UX Standards

Visual and interaction standards for the **bentador** Vue SPA. Derived from the original `bentador/GEMINI.md` design mandates and current implementation.

---

## Core stack (presentation)

| Layer | Choice |
|-------|--------|
| Framework | Vue 3 (Composition API) |
| Styling | Tailwind CSS 4 (`@tailwindcss/vite`) |
| Icons | Heroicons (`@heroicons/vue`) |
| Typography | Inter (loaded globally) |
| Charts | ApexCharts (`vue3-apexcharts`) |

Avoid utility-class bloat: prefer layout primitives (`flex`, `grid`, `gap`) and reusable component structure over one-off long class strings.

---

## Responsiveness

- **Mobile-first** is mandatory.
- Sidebar becomes a drawer below the `lg` breakpoint (`translate-x` + overlay).
- Tables and dense admin views should scroll horizontally on small screens when needed.
- Order Entry uses its own header and filter sidebar patterns—test touch targets on mobile.

---

## Aesthetics

| Element | Standard |
|---------|----------|
| Cards | `rounded-2xl` or `rounded-3xl` |
| Modals | `rounded-3xl`, soft shadow |
| Page background | `bg-[#F9FAFB]` / `dark:bg-dark-bg` |
| Card surface | `bg-white` / `dark:bg-dark-card` |
| Borders | `border-gray-100` / `dark:border-dark-border` |
| Primary accent | Account Settings chooses Green (Tailwind teal) or Blue (`#ADE1FB`, `#266CA9`, `#0F2573`, `#041D56`, `#01082D`) |
| Shadows | `shadow-sm` on cards; `shadow-teal-500/20` on primary CTAs |

---

## Dark mode

- **Class-based** dark mode on `document.documentElement`.
- Toggle via `useUiStore().toggleDarkMode()`.
- Persist preference: `localStorage` key `darkMode`.
- Color theme: `localStorage` key `colorTheme` (`green` or `blue`). Blue adds `theme-blue` on `document.documentElement`. Unset stays blue.
- Token pairs:
  - Page: `dark:bg-dark-bg`
  - Cards/modals: `dark:bg-dark-card`
  - Muted text: `dark:text-slate-400`

Account Settings exposes the same toggle for consistency with the sidebar menu.

---

## Interactive feedback

- Buttons: `:active:scale-95` and smooth `transition` on primary actions.
- Hover states on nav links: `hover:bg-gray-50` / `dark:hover:bg-slate-800/50`.
- Active route: `text-teal-600` / `dark:text-teal-400` (those utilities follow the selected color theme), often with `font-bold`.
- Toasts: bottom-right, `rounded-2xl`, used for save confirmations (profile, password).

---

## Accessibility

- Interactive controls use explicit `type="button"` where not submitting a form.
- Form inputs need visible labels (uppercase micro-labels are the project pattern).
- Modals: Escape to close, backdrop click where `BaseModal` supports it.
- Prefer semantic headings inside page sections, not only visual size.

---

## Component mandates

### Modals (`BaseModal.vue`)

- Teleport to `<body>`.
- Backdrop blur (`backdrop-blur-sm`).
- Close on Escape and optional outside click.
- Form modals: consistent `p-6`, header `bg-gray-50/50`, aligned footer actions.
- **Logout** always uses a confirmation modal before clearing session.

### Filter bar / dropdowns

- Dropdowns: `z-[70]` or higher so they float above tables.
- Date range: `From` / `To` with dynamic label on trigger.
- Edge-aligned menus: `right-0` to avoid horizontal clipping.

### Tables

- Loading: `TableSpinner.vue`.
- Pagination: `Pagination.vue`.
- Empty states: centered copy, muted color.

### Avatars

- Generated via UI Avatars API:  
  `https://ui-avatars.com/api/?name={encodedName}&background={0D9488|0F2573}&color=fff`
- Background is `#0D9488` on Green and `#0F2573` on Blue.
- Used in Sidebar, Profile Settings, and Order Entry header.

---

## Page layout patterns

### Dashboard shell (`AppLayout`)

- Fixed sidebar (`w-64`), main content `lg:ml-64`.
- Page padding: `p-4 md:p-8` or `p-4 sm:p-6 lg:p-8`.
- Header: `AppHeader` with title, description, breadcrumbs, `action-items`.

### Order Entry

- Full-width commerce UI: hero, product grid, cart slide-over, checkout modal.
- Sticky header with search, sort, cart, user menu.

### Settings pages (Profile & Account)

- Max width `max-w-3xl mx-auto` for readable forms.
- Section cards with icon headers (blue, with orange and purple only where a topic already uses them).
- Inline validation errors: `text-xs text-red-500` under fields.
- Success: inline message + toast via `useToastStore`.

---

## Brand

- Product name: **Benta Door**
- Document title pattern: `Benta Door: {page title}`
- Logo mark: rounded square in the active accent (`teal-500`) + grid icon (`Squares2X2Icon`)

---

## Related docs

- [FRONTEND.md](./FRONTEND.md) — Routes and navigation structure
- [AGENT.md](./AGENT.md) — Implementation rules for agents
