# WAKA PMS — USSD & Self-Service Interface

**Stack:** USSD gateway integration (telco/aggregator), SMS fallback, WAKA API consumer
**Audience:** USSD/telco integration engineering team / AI coding agent building the WAKA USSD channel

---

## 1. Prompt

```
You are building the USSD self-service channel for WAKA PMS, targeting tenants and landlords
who rely on feature phones or prefer USSD over smartphone apps — critical for markets with
low smartphone/data penetration.

CONTEXT
The USSD interface must let tenants and landlords self-register, pay rent, check balances,
view statements, and raise maintenance issues entirely through a menu-driven session, with
SMS used as a confirmation/fallback channel. It must integrate with the same backend domain
services (Auth, Lease, Rent Collection, Wallet, Maintenance, Notifications) as the web and
mobile clients — it is a thin, session-oriented client over the existing API layer, not a
parallel data store.

FUNCTIONAL SCOPE
1. Session & Menu Design
   - Design a smart, multi-tenant USSD menu tree (e.g., dial *XXX#) with role-aware routing:
     distinct top-level flows for tenants vs. landlords, and smart menu routing based on
     user type, property, unit, and account status.
   - Support short-code provisioning per tenant/property where required, with secure session
     handling (session timeout, session-state persistence keyed by MSISDN + session ID).

2. Identity & Security
   - Tenant/landlord self-service registration, login, and profile status checks over USSD.
   - Cross-channel verification for secure payment authorization via OTP/SMS/email.
   - Secure USSD PIN setup/reset flow, with biometric fallback for mobile-app-linked enrollments.
   - Rate limiting and lockout on repeated failed PIN attempts; full audit logging of every
     USSD action (login attempt, payment, ticket submission, status query).

3. Core Self-Service Flows
   - Rent payment and wallet top-up via integrated mobile money/payment gateway, including
     split/partial payment handling and payment confirmation via SMS.
   - Unit rental status, lease status, and rental balance lookup.
   - Transaction history, recent payments, and wallet history retrieval (paginated for USSD
     character limits).
   - Invoice, receipt, statement, and payment schedule requests — delivered via SMS link or
     automatic email when an email is on file.
   - Maintenance request, complaint, and support ticket submission with a category sub-menu.
   - FAQ / knowledge base / service guide access via USSD text or SMS deep link.
   - Transaction reversal, refund, and payment dispute initiation flows.
   - Scheduled payment reminders and renewal notices delivered via USSD push/SMS.

4. Reliability & Resilience
   - Offline/interrupted-session transaction retry support — if a USSD session drops mid-payment,
     ensure idempotent retry against the backend payment API (no duplicate charges) and a
     recovery notification once the transaction resolves.
   - Graceful degradation messaging when the payment gateway or backend is unavailable.

5. Localization & Multi-Tenant Routing
   - Configurable USSD workflows and copy for local markets/languages.
   - Multi-tenant request routing so payments/tickets land against the correct
     property/department, derived from the MSISDN-to-tenant/lease mapping, not user input.
   - Multi-currency and localized payment messaging.
   - Communication preference and opt-in/opt-out management reachable from the USSD menu.

6. Gateway Integration
   - Integrate with a telco/aggregator USSD gateway (define the adapter interface so the
     telco provider can be swapped) using a standard inbound webhook for session events
     (BEGIN, CONTINUE, END) and outbound responses within the telco's response-time SLA.
   - Integrate with the Notification Engine for SMS confirmations (payments, invoices, ticket
     updates, status checks) and with the Rent Collection / Wallet APIs for all financial actions.

NON-FUNCTIONAL REQUIREMENTS
- USSD responses must return within the telco gateway's timeout window (typically 2–10s) —
  design for fast menu rendering with cached/pre-fetched account context where possible.
- All financial actions over USSD must be idempotent and reconciled against the same ledger
  used by web/mobile/API channels — no separate ledger.
- Every session and action must be audit-logged with MSISDN (masked in logs per privacy rules),
  tenant/property context, and outcome.

DELIVERABLES
- USSD menu-tree specification (state diagram) covering tenant and landlord flows.
- Session-state machine design (states, transitions, timeout handling, idempotency keys).
- Adapter interface contract for the USSD gateway provider, decoupled from the menu logic.
- SMS fallback/confirmation template set for each financial and ticketing flow.
- Test plan covering dropped-session recovery, duplicate-payment prevention, and PIN lockout.

ACCEPTANCE CRITERIA
- A tenant can dial in, authenticate with PIN/OTP, pay rent via mobile money, and receive an
  SMS confirmation, with the payment reflected identically in the web portal and mobile app.
- A dropped session mid-payment never results in a duplicate charge on retry.
- Menu routing correctly isolates one tenant/property's data from another's based on the
  caller's MSISDN mapping, with no manual tenant selection step exposed to the user.
```

---

## 2. Work Plan

### Phase 0 — Discovery & Provider Selection (Weeks 1–2)
| Task | Output |
|---|---|
| Select telco/aggregator USSD gateway provider(s) per target market | Provider selection doc |
| Define session protocol with provider (inbound webhook format, response SLA, short-code allocation) | Provider integration spec |
| Confirm [USSD callback API contract](API.md) with the API team | Signed-off callback contract |
| Define MSISDN-to-tenant/account mapping strategy | Identity mapping design |

**Exit criteria:** provider sandbox access secured; callback contract frozen.

### Phase 1 — Session Infrastructure (Weeks 3–5)
| Task | Output |
|---|---|
| Session-state machine (BEGIN/CONTINUE/END handling, timeout, Redis-backed state store) | Session engine |
| Adapter interface for the USSD gateway, decoupled from menu/business logic | `ussd/adapter` interface + first provider implementation |
| MSISDN authentication: PIN setup/verification, OTP fallback, lockout policy | Identity flow |
| Audit logging hook for every inbound/outbound USSD event (masked MSISDN) | Audit integration |

**Exit criteria:** a test session can authenticate via PIN/OTP end-to-end in the provider sandbox, with state persisted and timed out correctly.

### Phase 2 — Menu Tree & Core Read Flows (Weeks 6–8)
| Task | Output |
|---|---|
| Full menu-tree specification for tenant and landlord top-level flows | Menu state diagram |
| Smart routing logic based on user type/property/unit/account status | Routing engine |
| Read-only flows: registration status, lease/unit status, balance lookup, transaction/wallet history (paginated) | Read flows implemented |
| Localization framework for menu copy (multi-language) | i18n layer |

**Exit criteria:** a tenant or landlord can navigate the full menu tree and retrieve account/lease/balance data correctly scoped to their MSISDN.

### Phase 3 — Payment & Financial Flows (Weeks 9–12)
| Task | Output |
|---|---|
| Rent payment / wallet top-up flow integrated with mobile money gateway via the Wallet/Payments API | Payment flow |
| Idempotency-key handling for payment initiation, with safe retry on dropped session | Idempotent payment logic |
| SMS payment confirmation templates and delivery | Notification integration |
| Split/partial payment handling within USSD character/step constraints | Split payment flow |
| Transaction reversal, refund, and dispute initiation flow | Dispute flow |

**Exit criteria:** a dropped session mid-payment, on retry, never produces a duplicate charge; SMS confirmation arrives reliably.

### Phase 4 — Document & Ticketing Flows (Weeks 13–14)
| Task | Output |
|---|---|
| Invoice/receipt/statement/payment-schedule request flow (delivered via SMS link or email) | Document request flow |
| Maintenance request / complaint / support ticket submission sub-menu | Ticketing flow |
| FAQ/knowledge-base access via USSD text or SMS deep link | KB access flow |

**Exit criteria:** a tenant can request a statement and receive it via SMS/email, and submit a maintenance ticket that appears in the backend ticket queue.

### Phase 5 — Reminders, Preferences & Resilience (Weeks 15–16)
| Task | Output |
|---|---|
| Scheduled payment reminder/renewal notice delivery via USSD push/SMS | Reminder scheduler integration |
| Communication preference / opt-in/opt-out management menu | Preferences flow |
| Graceful-degradation messaging for payment gateway/backend outages | Degradation handling |
| Offline/dropped-session recovery notification flow | Recovery notifications |

**Exit criteria:** reminders fire on schedule; outage scenarios produce clear user-facing messaging instead of silent failure.

### Phase 6 — Multi-Tenant Hardening & Load Testing (Weeks 17–18)
| Task | Output |
|---|---|
| Multi-tenant routing correctness test (cross-property/cross-tenant isolation) | Isolation test report |
| Response-time load test against telco SLA window (2–10s) | Performance test report |
| PIN-lockout and abuse-prevention test (brute-force simulation) | Security test report |
| Full audit-trail review for every flow | Audit completeness review |

**Exit criteria:** all flows respond within the telco SLA under load; no cross-tenant leakage; lockout policy verified.

### Phase 7 — Pilot & Rollout (Weeks 19–20)
| Task | Output |
|---|---|
| Pilot short-code launch with a single tenant/property | Pilot results |
| Feedback-driven menu copy/flow adjustments | Revised menu spec |
| Full rollout across configured tenants/properties with monitoring dashboards | Production rollout |

**Exit criteria:** pilot tenant completes real rent payments via USSD with no reconciliation discrepancies against the web/mobile ledger.

### Dependencies & Sequencing Notes
- Depends on [Backend](BACKEND.md) Phase 3 (Lease & Rent Collection) being available before Phase 3 of this plan starts.
- Depends on the [API](API.md) team's USSD callback contract (their Phase 7) being frozen before this plan's Phase 1.
- Shares the Notification Engine interface with [Backend](BACKEND.md) Phase 6 — coordinate template ownership to avoid duplicate SMS template definitions.
