# Payment Hub Corporate Connector Logo Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the checklist-like Pay Export Hub SVGs with a cleaner Corporate Connector identity.

**Architecture:** Update only the two existing SVG assets that Blade already references. The icon becomes a compact hub-and-flow mark; the horizontal logo becomes the same mark plus readable text for the navbar.

**Tech Stack:** Static SVG, Laravel public assets, Blade asset references, ImageMagick preview rendering, Vite build verification.

## Global Constraints

- Modify only `public/images/payment-hub-icon.svg` and `public/images/payment-hub-logo.svg`.
- Keep existing Blade references unchanged.
- Do not use document/checkmark metaphors.
- Use source, hub, destination nodes and directional connector lines.
- Keep blue and purple accents without copying bank logos.
- SVGs must parse as XML and render clearly at favicon and navbar sizes.

---

## File Structure

- `public/images/payment-hub-icon.svg`: square Corporate Connector mark for favicon/login icon.
- `public/images/payment-hub-logo.svg`: horizontal navbar logo using the same mark and text lockup.

### Task 1: Replace SVG Artwork

**Files:**
- Modify: `public/images/payment-hub-icon.svg`
- Modify: `public/images/payment-hub-logo.svg`

**Interfaces:**
- Consumes: Existing Blade references to `asset('images/payment-hub-icon.svg')` and `asset('images/payment-hub-logo.svg')`.
- Produces: Revised SVG assets at the same paths, so no layout changes are required.

- [ ] **Step 1: Confirm only the two SVG files will be edited**

Run:

```powershell
git status --short public\images\payment-hub-icon.svg public\images\payment-hub-logo.svg resources\views\layouts resources\views\livewire\layout
```

Expected: no pending changes in the two SVG files before editing; layout files do not need changes.

- [ ] **Step 2: Replace SVGs with Corporate Connector artwork**

Overwrite `payment-hub-icon.svg` with a rounded square mark containing a central hub, source node, two bank destination nodes, and a northeast flow arrow.

Overwrite `payment-hub-logo.svg` with the same mark and a text lockup reading `Pay Export Hub` with subtitle `Oracle to bank payment files`.

- [ ] **Step 3: Validate SVG XML**

Run:

```powershell
[xml](Get-Content -Raw public\images\payment-hub-icon.svg) | Out-Null
[xml](Get-Content -Raw public\images\payment-hub-logo.svg) | Out-Null
```

Expected: no XML parse errors.

- [ ] **Step 4: Render previews**

Run:

```powershell
magick public\images\payment-hub-icon.svg "$env:TEMP\payment-hub-icon-corporate-preview.png"
magick public\images\payment-hub-logo.svg "$env:TEMP\payment-hub-logo-corporate-preview.png"
magick public\images\payment-hub-logo.svg -resize 144x40 "$env:TEMP\payment-hub-logo-corporate-navbar-preview.png"
```

Expected: rendered previews show a clean connector mark, not a checklist/document icon.

- [ ] **Step 5: Run build**

Run:

```powershell
npm run build
```

Expected: Vite build exits 0.

- [ ] **Step 6: Commit SVG revision**

Run:

```powershell
git add public\images\payment-hub-icon.svg public\images\payment-hub-logo.svg
git commit -m "style: revise payment hub connector logo"
```

Expected: commit includes only the two SVG files.
