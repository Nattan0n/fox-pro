# Pay Export Hub Logo Design

## Goal

Replace UOB-only system branding with a neutral payment export identity that fits the existing Laravel/Blade UI and supports both UOB and SCB workflows.

## Approved Direction

Use the "Pay Export Hub" concept:

- A central payment/export symbol, not a bank logo.
- Visual cues for check/payment export: document/check shape, outbound arrow, and connector lines.
- Blue and purple accents to align with the current UOB/Oracle and SCB page themes without copying either bank brand.
- Compact enough for favicon and navbar usage.

## Scope

Create reusable web assets:

- `public/images/payment-hub-logo.svg` for navbar and larger logo placements.
- `public/images/payment-hub-icon.svg` for favicon and small square placements.

Update branding references:

- Replace favicon references in `resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php`.
- Replace the navbar UOB-only logo in `resources/views/livewire/layout/navigation.blade.php`.
- Update the login header in `resources/views/layouts/guest.blade.php` from UOB-specific wording to neutral platform wording.

Keep bank-specific branding inside bank-specific pages:

- Oracle/UOB export page may continue to show UOB context where it describes the UOB export workflow.
- SCB page remains purple and SCB-specific where it describes SCB supplier payment.

## Visual Design

The icon will use a rounded square mark with a white or light background, a blue-to-purple accent, a simple check/document glyph, and an export arrow. The full logo will combine the icon with the text "Pay Export Hub" and a small subtitle or compact lockup suitable for the navbar.

The style should match the existing UI: clean, rounded, subtle shadow-friendly, readable at small sizes, and not overly decorative.

## Success Criteria

- The app no longer appears UOB-only from the favicon, login page, or main navbar.
- The new logo remains clear at navbar size and favicon size.
- Existing UOB and SCB workflows remain visually distinct in their own pages.
- Changes are limited to branding assets and Blade references.

## Verification

- Run the frontend build.
- Check that updated assets are referenced by the layouts and navbar.
- Visually inspect the login page and authenticated navigation after the change.
