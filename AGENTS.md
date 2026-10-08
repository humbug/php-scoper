# AGENTS.md

This file provides guidance to coding agents working with code in this repository.

- Development commands, running the tests, the specification format and the end-to-end tests: [CONTRIBUTING.md](CONTRIBUTING.md).
- Code structure (scopers, node visitors, Reflector, exposed symbols): [docs/architecture.md](docs/architecture.md).
- User-facing behaviour and configuration: [README.md](README.md) and `docs/`.

Agent-specific notes:

- Install dependencies with `make vendor`, not `composer install` (it requires `COMPOSER_ROOT_VERSION` to be set).
- After debugging specifications in `_specs/`, move them back to `specs/` before finishing.
- Run `make cs` after changing PHP files, and `make autoreview` before considering a change complete.
