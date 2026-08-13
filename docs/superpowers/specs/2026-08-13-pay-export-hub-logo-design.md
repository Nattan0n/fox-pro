# Pay Export Hub Logo Design

## Goal

Replace UOB-only system branding with a neutral payment export identity that fits the existing Laravel/Blade UI and supports both UOB and SCB workflows.

## Approved Direction

Use the revised "Corporate Connector" version of the Pay Export Hub concept:

- A central integration hub symbol, not a bank logo.
- Visual cues for Oracle-to-bank payment flow: source node, hub node, bank destination nodes, and directional connector lines.
- Blue and purple accents to align with the current UOB/Oracle and SCB page themes without copying either bank brand.
- Compact enough for favicon and navbar usage.
- Avoid document/checkmark metaphors because they read as generic checklist/task app branding.

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

The icon will use a rounded square mark with a restrained blue-to-purple background, a central hub node, and clean connector lines to source/destination nodes. The full logo will combine the icon with the text "Pay Export Hub" and a compact subtitle suitable for the navbar.

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
