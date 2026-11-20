# Contributing to Blade UI

Contributions are welcome, including bug fixes, accessibility improvements, new components, and documentation updates.

## Development setup

```bash
git clone https://github.com/Mahmoud-Alqudsi/blade-ui.git
cd blade-ui
composer install
composer test
```

## Component guidelines

- Keep each component self-contained under `resources/components/ui` or `resources/components/forms`.
- Start each component file with an English comment describing its props, slots, and a usage example.
- Declare component props with `@props`, keep PHP logic in an `@php` block, and then render the markup.
- Use logical Tailwind utilities such as `ps-*`, `pe-*`, `ms-*`, `me-*`, `start-*`, `end-*`, and `text-start` so layouts work in both LTR and RTL. Avoid physical left/right spacing and alignment utilities when a logical utility is available.
- Provide appropriate `dark:` styles for colors and surfaces.
- Keep accessibility in mind: use suitable ARIA attributes, visible keyboard focus styles, and `motion-reduce:` variants where animation is involved. Prefer `focus-visible:outline-hidden` over `outline-none`.
- Keep Tailwind class names statically detectable. Do not construct class names dynamically; use complete class strings in a `match()` expression instead.
- Merge consumer attributes and classes with `$attributes->merge([...])` or `$attributes->class([...])`.
- Use JavaScript only for interactions that need it, and follow the existing Alpine.js patterns.

## Tests

Add Pest coverage under `tests/Feature/Components` for new component behavior. Cover relevant default output, variants, accessibility attributes, and edge cases. Run the full suite before opening a pull request:

```bash
composer test
```

## Pull requests

1. Create a focused branch from `main`, using a descriptive name such as `feat/rating` or `fix/modal-focus`.
2. Keep the pull request focused and explain the problem or motivation, not only the implementation details.
3. Update `CHANGELOG.md` and the component's inline documentation. Update `README.md` when adding a component or changing setup and usage.
4. Confirm that the CI checks pass.

## Releases

Releases follow [Semantic Versioning](https://semver.org/). Maintainers should update `CHANGELOG.md`, create a version tag, and push it:

```bash
git tag v1.1.0
git push origin main --tags
```
