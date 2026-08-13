# InnovateX 2026

## Problem

Payment confirmation and invoice reconciliation are often disconnected. Teams manually compare references, amounts and bank or chain evidence before updating an invoice and building an audit record.

## Solution

Cherry Pay carries invoice context through payment intent, QR, settlement verification, matching and reconciliation. A judge can run the whole sequence with no external account or real funds.

## Innovation

The prototype treats independently verifiable settlement as a workflow trigger while keeping deterministic controls authoritative. It shows where AI belongs: helping operators understand uncertain exceptions, not inventing certainty at the money boundary.

## Technical architecture

Laravel services separate intent creation, settlement verification and reconciliation. SQLite persists a deliberately small domain model. Blade exposes the merchant, payer and control views. REST endpoints expose the same evidence for integrations.

## Payment flow

```text
Invoice → UUID intent → public slug/QR → simulated USDC evidence
→ verifier → matcher → invoice status → audit trail
```

## Reconciliation logic

Exact intent, amount and currency yield 100% confidence. Exact invoice reference, amount and currency yield 98%. The configurable automatic threshold is 95%. Partial, duplicate or inconsistent evidence never silently marks an invoice paid.

## Web3 component

The repeatable demo adapter proves the interface and workflow without claiming money moved. The optional EVM adapter performs a read-only receipt lookup and fails closed pending complete ERC-20 Transfer verification. No signing capability or private-key configuration exists.

## Demo walkthrough

1. Launch `/demo` and view fictional invoice `INV-1042`.
2. Choose **Create Cherry Pay Request**.
3. Inspect the public link and QR.
4. Choose **Simulate USDC Settlement** under the demo/testnet warning.
5. Observe the 100% match and automatically reconciled invoice.
6. Review the timestamped audit trail and transaction evidence.

## What was built

- Six-record payment and reconciliation domain model
- Invoice-linked, idempotent payment intents and QR codes
- Settlement-verifier interface with demo and experimental EVM adapters
- Deterministic matcher, threshold control and exception path
- Idempotent reconciliation service and audit events
- Judge-facing UI, JSON APIs, seed data, tests, Docker and CI

## What remains prototype/testnet

Real settlement submission, ERC-20 log/decimal/finality verification, identity and access controls, refunds/reversals, multi-currency accounting, operational monitoring and production infrastructure.

## Future roadmap

1. Upgrade to a supported framework line and complete security review.
2. Complete testnet USDC Transfer verification against an allow-listed contract and wallet.
3. Add authenticated merchant workspaces and signed event ingestion.
4. Add controlled manual review and exception-assistance tooling.
5. Validate controls with finance teams before a tightly scoped pilot.
