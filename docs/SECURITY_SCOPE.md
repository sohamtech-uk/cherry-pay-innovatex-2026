# Security scope

This repository is a sanitised, standalone hackathon extraction. It has a new Git history and deliberately excludes:

- the complete Cherry Money source and application shell;
- production secrets, credentials and configuration;
- production payment, banking and provider integrations;
- customer information and operational data;
- unrelated accounting, tax, payroll, marketplace, travel, lending, wealth and personal-finance modules;
- wallet signing, private keys and seed phrases.

All demo merchants, customers, references, wallet-like strings and settlement values are fictional. Demo settlement identifiers are encoded test evidence, not blockchain transaction hashes.

## Trust boundaries

The demo verifier is trusted only to provide repeatable local evidence. The EVM adapter is experimental and fails closed. The reconciliation matcher requires deterministic evidence at or above the configured confidence threshold.

## Not production ready

This prototype omits authentication, authorization, rate-limit tuning, production key management, chain reorganisation/finality handling, token decimal verification, sanctions/fraud controls, refunds, disputes, monitoring and independent assurance. Laravel 10 is retained only because the build brief requires it; upgrade to a supported framework release before further use.

As of 13 August 2026, `composer audit` reports three Laravel framework advisories (two related records for email-rule CRLF injection and one temporary signed-URL path-confusion advisory). This prototype exposes neither mail validation nor signed-URL routes, but the dependency finding remains unresolved while the Laravel 10 requirement remains in place. It must not be deployed as a production service.

## Publication checks

Before every public release:

1. Run tests and formatting checks.
2. Scan the complete working tree for secrets and personal information.
3. Review all tracked files and Git objects.
4. Confirm the only remote is this standalone repository.
5. Confirm no original source history is reachable.
