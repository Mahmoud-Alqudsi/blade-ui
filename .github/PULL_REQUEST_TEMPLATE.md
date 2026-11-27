## What does this PR change?

<!-- Briefly explain the motivation and the change -->

## Type of change

- [ ] Bug fix
- [ ] New component or feature
- [ ] Accessibility / RTL / dark mode improvement
- [ ] Documentation or tests

## Checklist

- [ ] Added or updated Pest tests and `composer test` passes
- [ ] Classes use logical (RTL-safe) utilities and include `dark:` variants
- [ ] For interactive components: added a fixture page, Playwright spec, and axe scenario (`npm run browser`)
- [ ] Changed colors? `npm run audit:contrast` passes
- [ ] Used `->twClass(` and private internal references `<x-blade-ui::…>` (enforced by tests)
- [ ] No hard-coded UI strings: added keys to both `lang/ar` and `lang/en`
- [ ] Added the English header comment (props and usage example) at the top of the file
- [ ] Updated `CHANGELOG.md` (and `README.md` when adding a component)
