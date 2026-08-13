# Architecture

## Domain model

- `Merchant` owns invoices and carries an optional public receiving-wallet address.
- `Invoice` records the fictional customer, amount, currency and payment state.
- `PaymentIntent` binds a unique UUID, public slug, reference, amount and expiry to one invoice.
- `Settlement` records verifier output and enforces a globally unique transaction hash.
- `Reconciliation` records confidence, reason and automatic/suggested disposition.
- `AuditEvent` records each material transition without depending on the wider accounting platform.

## Request lifecycle

The merchant creates or opens an invoice, creates one active payment intent, and shares its public URL or QR. Opening the URL records an event. Submitting demo settlement evidence invokes the configured verifier and then the reconciliation service inside a database transaction.

## Payment-intent lifecycle

```text
created → opened → settlement_pending → paid
                        ↘ expired
```

Creating a second active request for the same invoice returns the existing request. Public slugs are random and unique. The QR contains only the public payment URL.

## Settlement verification

`SettlementVerifier` isolates evidence acquisition from reconciliation:

- `DemoSettlementVerifier` signs nothing, moves no funds and decodes only locally generated fictional evidence.
- `EvmSettlementVerifier` can fetch a receipt over JSON-RPC but deliberately returns unverified until ERC-20 contract, Transfer event, recipient and amount checks are implemented and reviewed.

No adapter holds a payer private key.

## Reconciliation algorithm

The matcher prefers deterministic evidence:

| Evidence | Confidence | Action |
|---|---:|---|
| Intent + amount + currency | 100% | Automatic |
| Reference + amount + currency | 98% | Automatic |
| Partial payment | 65% | Review |
| Reference with amount/currency variance | 55% | Review |
| Amount/currency without authoritative link | 50% | Review |

The default automatic threshold is `0.95`. AI may later summarize exceptions or propose candidates, but cannot override this control.

## Idempotency

Payment-intent creation reuses an active invoice intent. Settlement transaction hashes are unique. Replaying the same verified settlement returns the existing reconciliation without duplicating records or audit events. Reusing a transaction hash for another intent is rejected.

## Audit events

Creation, opening, settlement confirmation/failure, match selection, automatic reconciliation and review-required outcomes are recorded with subject identifiers and compact JSON evidence.

## Production considerations

Before real funds: upgrade the framework to a supported release, implement full EVM token transfer verification, add merchant identity and authorization controls, verify webhook provenance/finality, model reversals/refunds, encrypt operational data, add monitoring and obtain independent security and financial-controls review.
