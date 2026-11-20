# Security Policy

## Supported versions

| Version | Security updates |
|---|---|
| 1.x | Supported |

## Reporting a vulnerability

Please do not report security vulnerabilities in public issues. Email **mah.alqudsi@gmail.com** with a description of the issue and steps to reproduce it, or use the repository's **Report a vulnerability** option on GitHub.

We aim to acknowledge reports within seven days. We will coordinate disclosure after a fix is available and credit the reporter if they wish.

## Scope

Blade UI renders dynamic values through Blade's escaped `{{ }}` syntax. Security reports are especially useful when they identify a path to cross-site scripting, such as unescaped output or unsafe interpolation into Alpine.js attributes like `x-data` and `x-bind`.
