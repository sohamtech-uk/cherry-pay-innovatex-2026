# Clean-room extraction map

The private source repository was inspected read-only before this standalone application was authored. No source file, dependency manifest, data file or Git object was copied wholesale.

| Source concept inspected | Standalone adaptation | Dependencies intentionally rejected |
|---|---|---|
| Invoice-linked payment-intent creation | `PaymentIntentService` with merchant/invoice only | Full company/user models, personal payments, notification and risk services |
| Random public slug, reference and pay URL | UUID intent, random slug, URL and local QR | Provider account infrastructure and bank redirect sessions |
| Payment status/event recording | Small intent lifecycle and `AuditEvent` | Provider-specific status fields and webhook machinery |
| Confirmed payment to invoice update | Transactional `ReconciliationService` | Full accounting ledger and gateway payment model |
| Amount/reference matching ideas | Standalone deterministic `ReconciliationMatcher` | Bank feeds, expense matching and user/company authorization graph |
| Pending settlement lookup concept | `SettlementVerifier` boundary | Production provider polling and credentials |

The resulting repository has one purpose: demonstrate invoice → payment intent → settlement verification → deterministic matching → reconciliation → audit.
