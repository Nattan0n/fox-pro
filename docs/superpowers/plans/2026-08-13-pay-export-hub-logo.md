# Pay Export Hub Logo Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace UOB-only app branding with neutral Pay Export Hub branding for favicon, login, and navbar.

**Architecture:** Use two static SVG assets in `public/images`: one square icon for favicon and compact placements, and one horizontal logo for navbar/larger placements. Update only Blade layout/navigation references so bank-specific UOB and SCB pages keep their existing workflow branding.

**Tech Stack:** Laravel Blade, Livewire Volt navigation, Tailwind utility classes, static SVG assets, Vite frontend build.

## Global Constraints

- Create `public/images/payment-hub-logo.svg` for navbar and larger logo placements.
- Create `public/images/payment-hub-icon.svg` for favicon and small square placements.
- Replace favicon references in `resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php`.
- Replace the navbar UOB-only logo in `resources/views/livewire/layout/navigation.blade.php`.
- Update the login header in `resources/views/layouts/guest.blade.php` from UOB-specific wording to neutral platform wording.
- Keep bank-specific branding inside bank-specific pages.
- Use blue and purple accents to align with the current UOB/Oracle and SCB page themes without copying either bank brand.
- Changes are limited to branding assets and Blade references.

---

## File Structure

- `public/images/payment-hub-icon.svg`: square SVG mark for favicon and small UI placements.
- `public/images/payment-hub-logo.svg`: horizontal SVG logo lockup for navbar and larger placements.
- `resources/views/layouts/app.blade.php`: authenticated layout favicon reference.
- `resources/views/layouts/guest.blade.php`: guest layout favicon, login hero logo, and neutral login copy.
- `resources/views/livewire/layout/navigation.blade.php`: authenticated navbar logo asset and neutral logo comments/alt text.

### Task 1: Create Pay Export Hub SVG Assets

**Files:**
- Create: `public/images/payment-hub-icon.svg`
- Create: `public/images/payment-hub-logo.svg`

**Interfaces:**
- Consumes: No earlier task output.
- Produces: `asset('images/payment-hub-icon.svg')` and `asset('images/payment-hub-logo.svg')` paths for Blade templates.

- [ ] **Step 1: Run pre-implementation asset check**

Run:

```powershell
Test-Path public\images\payment-hub-icon.svg
Test-Path public\images\payment-hub-logo.svg
```

Expected: both commands print `False` before asset creation.

- [ ] **Step 2: Create `public/images/payment-hub-icon.svg`**

Create the file with this exact SVG:

```xml
<svg width="512" height="512" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title desc">
  <title id="title">Pay Export Hub icon</title>
  <desc id="desc">A neutral payment export icon with a check document, export arrow, and blue purple connector colors.</desc>
  <defs>
    <linearGradient id="bg" x1="72" y1="72" x2="440" y2="440" gradientUnits="userSpaceOnUse">
      <stop stop-color="#2563EB"/>
      <stop offset="1" stop-color="#7E22CE"/>
    </linearGradient>
    <linearGradient id="accent" x1="164" y1="176" x2="352" y2="344" gradientUnits="userSpaceOnUse">
      <stop stop-color="#38BDF8"/>
      <stop offset="1" stop-color="#C084FC"/>
    </linearGradient>
  </defs>
  <rect x="32" y="32" width="448" height="448" rx="108" fill="url(#bg)"/>
  <path d="M164 132H304L368 196V356C368 371.464 355.464 384 340 384H164C148.536 384 136 371.464 136 356V160C136 144.536 148.536 132 164 132Z" fill="white" fill-opacity="0.96"/>
  <path d="M304 132V190C304 197.732 310.268 204 318 204H368" fill="#DBEAFE"/>
  <path d="M182 276L226 320L322 224" stroke="url(#accent)" stroke-width="28" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M284 344H374V254" stroke="white" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M280 348L374 254" stroke="white" stroke-width="26" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="116" cy="256" r="14" fill="#93C5FD"/>
  <circle cx="396" cy="256" r="14" fill="#D8B4FE"/>
  <path d="M130 256H164" stroke="#BFDBFE" stroke-width="12" stroke-linecap="round"/>
  <path d="M368 256H382" stroke="#E9D5FF" stroke-width="12" stroke-linecap="round"/>
</svg>
```

- [ ] **Step 3: Create `public/images/payment-hub-logo.svg`**

Create the file with this exact SVG:

```xml
<svg width="760" height="180" viewBox="0 0 760 180" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="title desc">
  <title id="title">Pay Export Hub logo</title>
  <desc id="desc">Pay Export Hub horizontal logo for a neutral Oracle to bank payment export platform.</desc>
  <defs>
    <linearGradient id="markBg" x1="26" y1="26" x2="154" y2="154" gradientUnits="userSpaceOnUse">
      <stop stop-color="#2563EB"/>
      <stop offset="1" stop-color="#7E22CE"/>
    </linearGradient>
    <linearGradient id="markAccent" x1="70" y1="58" x2="124" y2="124" gradientUnits="userSpaceOnUse">
      <stop stop-color="#38BDF8"/>
      <stop offset="1" stop-color="#C084FC"/>
    </linearGradient>
  </defs>
  <rect x="14" y="14" width="152" height="152" rx="38" fill="url(#markBg)"/>
  <path d="M62 44H108L130 66V122C130 128.627 124.627 134 118 134H62C55.373 134 50 128.627 50 122V56C50 49.373 55.373 44 62 44Z" fill="white" fill-opacity="0.96"/>
  <path d="M108 44V63C108 66.866 111.134 70 115 70H130" fill="#DBEAFE"/>
  <path d="M66 94L82 110L116 76" stroke="url(#markAccent)" stroke-width="11" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M104 126H138V92" stroke="white" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M103 127L138 92" stroke="white" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="32" cy="90" r="5" fill="#93C5FD"/>
  <circle cx="148" cy="90" r="5" fill="#D8B4FE"/>
  <path d="M37 90H50" stroke="#BFDBFE" stroke-width="5" stroke-linecap="round"/>
  <path d="M130 90H143" stroke="#E9D5FF" stroke-width="5" stroke-linecap="round"/>
  <text x="196" y="82" fill="#0F172A" font-family="Figtree, Arial, sans-serif" font-size="42" font-weight="800">Pay Export Hub</text>
  <text x="198" y="124" fill="#475569" font-family="Figtree, Arial, sans-serif" font-size="22" font-weight="600">Oracle to bank payment files</text>
  <circle cx="594" cy="90" r="6" fill="#2563EB"/>
  <path d="M612 90H666" stroke="#64748B" stroke-width="4" stroke-linecap="round"/>
  <circle cx="684" cy="90" r="6" fill="#7E22CE"/>
  <text x="598" y="124" fill="#64748B" font-family="Figtree, Arial, sans-serif" font-size="16" font-weight="700">UOB / SCB</text>
</svg>
```

- [ ] **Step 4: Validate SVG files parse as XML**

Run:

```powershell
[xml](Get-Content -Raw public\images\payment-hub-icon.svg) | Out-Null
[xml](Get-Content -Raw public\images\payment-hub-logo.svg) | Out-Null
```

Expected: both commands complete with no XML parse error.

- [ ] **Step 5: Commit asset files**

Run:

```powershell
git add public\images\payment-hub-icon.svg public\images\payment-hub-logo.svg
git commit -m "feat: add pay export hub logo assets"
```

Expected: commit includes only the two new SVG files.

### Task 2: Update Layout And Navbar Branding References

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/livewire/layout/navigation.blade.php`

**Interfaces:**
- Consumes: `asset('images/payment-hub-icon.svg')` and `asset('images/payment-hub-logo.svg')` from Task 1.
- Produces: Neutral favicon, login header, and authenticated navbar branding.

- [ ] **Step 1: Run pre-change UOB-only reference check**

Run:

```powershell
rg -n "uob-icon\.png|uob-logo-color\.png|Export Check Payment to UOB|Oracle & UOB Integration Platform|Logo - UOB Only|alt=\"UOB\"" resources\views\layouts resources\views\livewire\layout
```

Expected: matches in `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`, and `resources/views/livewire/layout/navigation.blade.php`.

- [ ] **Step 2: Update favicons**

In `resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php`, replace:

```blade
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/uob-icon.png') }}">
```

with:

```blade
<link rel="icon" type="image/svg+xml" href="{{ asset('images/payment-hub-icon.svg') }}">
```

- [ ] **Step 3: Update guest login logo block**

In `resources/views/layouts/guest.blade.php`, replace the current `<!-- Oracle + UOB Logo -->` logo block with:

```blade
<!-- Pay Export Hub Logo -->
<div class="flex justify-center items-center">
    <div class="w-28 h-28 bg-white rounded-2xl flex items-center justify-center shadow-2xl hover:shadow-3xl transition-shadow duration-300 p-3 border border-slate-100">
        <img src="{{ asset('images/payment-hub-icon.svg') }}"
            alt="Pay Export Hub"
            class="w-full h-full object-contain">
    </div>
</div>
```

Then replace the login title block with:

```blade
<div class="mt-6 text-center">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Export Check Payment Platform</h1>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Oracle, UOB &amp; SCB Integration Platform</p>
</div>
```

- [ ] **Step 4: Update authenticated navbar logo**

In `resources/views/livewire/layout/navigation.blade.php`, replace the comment:

```blade
<!-- Logo - UOB Only -->
```

with:

```blade
<!-- Logo - Pay Export Hub -->
```

Replace the current UOB logo wrapper:

```blade
<div class="w-20 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg border border-slate-200 dark:border-slate-600 p-1.5 transition-all duration-300 group-hover:shadow-2xl group-hover:scale-105">
    <img src="{{ asset('images/uob-logo-color.png') }}"
        alt="UOB"
        class="w-full h-full object-contain">
</div>
```

with:

```blade
<div class="w-36 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg border border-slate-200 dark:border-slate-600 px-2 py-1 transition-all duration-300 group-hover:shadow-2xl group-hover:scale-105">
    <img src="{{ asset('images/payment-hub-logo.svg') }}"
        alt="Pay Export Hub"
        class="w-full h-full object-contain">
</div>
```

- [ ] **Step 5: Run post-change reference check**

Run:

```powershell
rg -n "uob-icon\.png|Export Check Payment to UOB|Oracle & UOB Integration Platform|Logo - UOB Only" resources\views\layouts resources\views\livewire\layout
```

Expected: no matches.

Run:

```powershell
rg -n "payment-hub-icon\.svg|payment-hub-logo\.svg|Export Check Payment Platform|Oracle, UOB &amp; SCB Integration Platform" resources\views\layouts resources\views\livewire\layout
```

Expected: matches for both layout favicon references, guest login header, and navbar logo.

- [ ] **Step 6: Commit Blade branding updates**

Run:

```powershell
git add resources\views\layouts\app.blade.php resources\views\layouts\guest.blade.php resources\views\livewire\layout\navigation.blade.php
git commit -m "feat: apply neutral payment export branding"
```

Expected: commit includes only the three Blade files.

### Task 3: Build And Visual Verification

**Files:**
- Read: `resources/views/layouts/app.blade.php`
- Read: `resources/views/layouts/guest.blade.php`
- Read: `resources/views/livewire/layout/navigation.blade.php`
- Read: `public/images/payment-hub-icon.svg`
- Read: `public/images/payment-hub-logo.svg`

**Interfaces:**
- Consumes: asset and Blade updates from Tasks 1 and 2.
- Produces: verified branding update ready for user review.

- [ ] **Step 1: Run frontend build**

Run:

```powershell
npm run build
```

Expected: Vite build completes successfully.

- [ ] **Step 2: Run final branding search**

Run:

```powershell
rg -n "uob-icon\.png|Export Check Payment to UOB|Oracle & UOB Integration Platform|Logo - UOB Only" resources\views\layouts resources\views\livewire\layout public\images
```

Expected: no matches.

Run:

```powershell
rg -n "payment-hub-icon\.svg|payment-hub-logo\.svg|Pay Export Hub|Export Check Payment Platform" resources\views\layouts resources\views\livewire\layout public\images
```

Expected: matches in the two SVG assets, both layouts, guest login copy, and navbar.

- [ ] **Step 3: Inspect rendered pages**

Run the app through the existing local Laravel/XAMPP setup and inspect:

```text
/login
/dashboard
/oracle
/scb
```

Expected:

- `/login` shows a neutral Pay Export Hub icon and no UOB-only headline.
- `/dashboard`, `/oracle`, and `/scb` show the neutral navbar logo.
- `/oracle` may still show UOB-specific workflow content inside the page.
- `/scb` remains SCB-specific inside the page.

- [ ] **Step 4: Capture final status**

Run:

```powershell
git status --short
```

Expected: only files intentionally changed by these tasks are modified or committed; unrelated pre-existing worktree changes remain untouched.
