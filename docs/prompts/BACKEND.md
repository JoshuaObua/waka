# WAKA PMS — Web App Backend

**Stack:** Golang, Gin/Fiber, PostgreSQL, Redis, Supabase Storage, Supabase Auth (optional), WebSockets, JWT
**Audience:** Backend engineering team / AI coding agent implementing the WAKA PMS core services

---

## 1. Prompt

```
You are a senior Golang backend engineer building the core backend services for WAKA PMS,
an enterprise-grade, multi-tenant property management platform ("Waka" = "Home" in Luganda).

CONTEXT
WAKA must support landlords, property management companies, and enterprise estate operators
managing residential and commercial properties from a single, secure, multi-tenant platform.
The backend is the system of record for every module below and must be API-first, since it
serves a separate web frontend (Vue 3/Nuxt 4), native mobile apps (Flutter), and a USSD gateway.

TECH STACK
- Language/Framework: Golang with Gin or Fiber
- Database: PostgreSQL (primary store), Redis (caching, sessions, rate limiting, pub/sub)
- Storage: Supabase Storage for documents/media; Supabase Auth optional (evaluate vs. custom JWT)
- Realtime: WebSockets for live notifications, dashboards, and ticket/maintenance updates
- AuthN/AuthZ: JWT-based authentication with refresh tokens, MFA (OTP/email/SMS/authenticator/biometric)
- Containerization: Docker, Kubernetes-ready manifests/Helm charts
- CI/CD: pipeline-ready with automated tests, linting, migrations, and blue/green or canary rollout support

SCOPE — IMPLEMENT THE FOLLOWING DOMAIN MODULES AS SERVICES/PACKAGES
1. Authentication & Security — registration, login, MFA, SSO (OAuth2/OIDC/SAML), session/device
   management, password reset, account lockout, impersonation with audit trail.
2. Multi-Tenant Architecture — tenant-aware data partitioning by tenant ID, property, portfolio,
   or business unit; logical isolation for shared config; scoped admin/owner portals.
3. RBAC Engine — dynamic role CRUD (create/edit/clone/disable/archive/restore), permission
   categories (Property, Unit, Lease, Financial, Maintenance, Reports, Administration, etc.),
   scope assignment at tenant/property/building/unit/portfolio level, hierarchical role
   inheritance, role groups, time-boxed/temporary access, dual-approval for sensitive ops.
4. Property Classification & Property Management — category → type → unit-category hierarchy
   (Residential/Commercial), dynamic property/unit type management, property/building/floor/unit
   CRUD with full metadata (GPS, land title, documents, photos), amenities management, portfolio
   and asset performance tracking.
5. Tenant Management — full tenant profile (KYC, guarantor, bank details, screening), lifecycle
   (onboarding, verification, assignment/transfer, eviction, exit/deposit reconciliation),
   multi-step onboarding workflows with document upload and approval gateways, bulk import/export.
6. Lease Management — lease CRUD with full clause set (rent, deposit, billing cycle, escalation,
   penalties), reusable penalty/fee rule engine, billing-cycle engine (weekly→annual, prorated,
   consolidated, split-cycle), lease lifecycle workflows (approval, renewal, termination, amendment,
   e-signature integration), full audit trail/version comparison.
7. Rent Collection — pluggable payment-method abstraction (mobile money, bank transfer, wallet,
   card/PCI tokenization, cash, cheque, POS, QR, direct debit, biometric auth), invoice/receipt
   generation, reconciliation, partial/split payments, late-fee engine, arrears/aging/dunning/
   collections workflows with legal escalation handoff.
8. Wallet & Finance — ledger-backed wallets (tenant/owner/vendor/platform), holds/escrow,
   multi-currency balances, expense management with GL coding, approval chains, budget tracking,
   three-way PO matching.
9. Utility Billing — meter lifecycle, tariff engine (fixed/tiered/slab/time-of-use/seasonal),
   consumption billing with shared/split allocation, dispute/exception handling, disconnection
   workflows.
10. Service Charge Management — budget planning, allocation rules, CAM fee calculation,
    reconciliation, waivers/abatements, statement generation.
11. Maintenance & Repairs — request intake (multi-channel), SLA-driven work-order engine,
    technician/vendor assignment, asset linkage, escalation on SLA breach.
12. Vendor Management — vendor profiles/KYC, contract lifecycle, SLA performance tracking,
    payment terms, vendor self-service portal endpoints.
13. Visitor Management — pre-registration, badge/QR/NFC issuance, host notification, watchlist
    screening, check-in/out audit trail.
14. Documents Management — versioned storage via Supabase Storage, metadata/tagging, OCR hooks,
    expiry alerts, e-signature integration, retention/legal-hold policies.
15. Notification Engine — multi-channel dispatcher (email/SMS/push/in-app/webhook/USSD/IVR/
    WhatsApp) with templating, localization, retry/dead-letter handling, delivery analytics.
16. Reporting & Analytics — query services for occupancy, rent collection, P&L, arrears aging,
    custom report builder, scheduled exports (PDF/Excel/CSV/JSON), BI/data-warehouse hooks.
17. Audit & Compliance — immutable, tamper-evident audit log service for every state-changing
    action; compliance report generation (KYC/AML/GDPR); retention and legal-hold workflows.
18. User & Role Management — user CRUD, bulk import/export, session/device management, role
    assignment, consent/privacy preference tracking.
19. Support & Knowledge Base — ticketing engine, department routing, SLA tracking, CMS for
    KB articles/FAQs.

CROSS-CUTTING REQUIREMENTS
- Tenant isolation must be enforced at the data-access layer (row-level security or
  query-scoping middleware) — never trust client-supplied tenant context alone.
- Every mutating endpoint must emit an audit event with actor, before/after state, and timestamp.
- All financial operations (payments, wallet, expenses) must be transactional/idempotent and
  reconciliation-safe — design for exactly-once posting with retry-safe idempotency keys.
- Background jobs (billing runs, reminders, SLA breach detection) via a job queue (e.g.,
  Redis-backed worker pool); must be horizontally scalable and independently deployable from
  the API tier.
- Structured logging, metrics (Prometheus-style), and distributed tracing hooks for
  observability; health/readiness endpoints for Kubernetes probes.
- Input validation, rate limiting, and anti-CSRF/anti-replay protections on all endpoints.

DELIVERABLES
- Service/package architecture proposal (modular monolith vs. domain services) with rationale.
- Database schema (PostgreSQL) with migrations, including tenant-scoping strategy.
- Core domain models and repository/service layer for at least Auth, RBAC, Property/Unit,
  Tenant, Lease, and Rent Collection modules as the first vertical slice.
- Background worker setup for billing and notification dispatch.
- Dockerfile(s), docker-compose for local dev, and a basic Kubernetes manifest set.
- Automated test suite (unit + integration) for the implemented slice.

ACCEPTANCE CRITERIA
- A tenant admin can: create a property → unit → lease → tenant, and collect a rent payment,
  all scoped correctly so no other tenant's data is visible or mutable.
- RBAC denies an action when the caller's role lacks the permission, scoped correctly to
  property/unit/portfolio.
- Every action above produces a corresponding audit log entry.
```

---

## 2. Work Plan

### Phase 0 — Foundation & Platform Setup (Weeks 1–3)
| Task | Output |
|---|---|
| Repo scaffolding, module layout, lint/format/CI baseline | `go.mod`, CI pipeline, pre-commit hooks |
| PostgreSQL schema strategy decision (shared schema + tenant_id vs. schema-per-tenant) | ADR (Architecture Decision Record) |
| Core infra: Docker Compose (Postgres, Redis), config/env management, structured logging | Local dev environment |
| Migration tooling (e.g., golang-migrate / goose) | Migration runner wired into CI |
| Base HTTP server skeleton (Gin/Fiber), middleware chain: request ID, logging, recovery, tenant resolution | `cmd/api` bootstrapped service |
| Health/readiness endpoints, Prometheus metrics endpoint | `/healthz`, `/readyz`, `/metrics` |

**Exit criteria:** empty service boots in Docker, passes CI, exposes health checks, connects to Postgres/Redis.

### Phase 1 — Identity, Tenancy & RBAC (Weeks 4–7)
| Task | Output |
|---|---|
| Tenant model + tenant-scoping middleware (every query auto-scoped) | `internal/tenancy` |
| Auth service: registration, login, JWT + refresh tokens, password reset | `internal/auth` |
| MFA: OTP via SMS/email, authenticator app support | MFA endpoints |
| SSO scaffolding: OAuth2/OIDC client integration points | SSO adapter interface |
| RBAC engine: roles, permissions, scopes (tenant/property/building/unit/portfolio), role CRUD + clone/archive/restore | `internal/rbac` |
| Permission-check middleware/decorator usable by all future modules | Reusable `RequirePermission(scope, action)` |
| Audit log service (write-only, append-only table) wired to a publish hook | `internal/audit` |

**Exit criteria:** a user can register, log in with MFA, be assigned a scoped role, and have access denied/allowed correctly; every action is audited.

### Phase 2 — Property, Unit & Tenant Domain (Weeks 8–11)
| Task | Output |
|---|---|
| Property classification hierarchy (Category → Type → Unit Category), admin-configurable | `internal/property` |
| Property/Building/Floor/Unit CRUD with metadata, media via Supabase Storage | Property domain service |
| Amenities management (assignable to property/building/unit) | Amenities sub-module |
| Tenant profile model (KYC, guarantor, screening) + onboarding workflow state machine | `internal/tenant` |
| Bulk import/export (CSV/Excel) for tenants and units | Import/export jobs |

**Exit criteria:** full property → building → unit tree can be created, tenants onboarded and KYC-gated before unit assignment.

### Phase 3 — Lease & Rent Collection (Weeks 12–16)
| Task | Output |
|---|---|
| Lease domain model + lifecycle state machine (draft→approved→active→renewed/terminated) | `internal/lease` |
| Penalty/fee rule engine (late fees, escalation, proration) | Rule engine module |
| Billing cycle engine (weekly→annual, prorated, consolidated) | Billing scheduler (background worker) |
| Payment method abstraction layer + first 2 provider integrations (mobile money, card) | `internal/payments` |
| Invoice/receipt generation, reconciliation, idempotent payment posting | Payments service |
| Arrears/aging engine + dunning notice triggers | Collections sub-module |

**Exit criteria:** a lease can be created, billed on schedule, paid via at least one real payment provider, and reconciled; arrears age correctly and trigger reminders.

### Phase 4 — Wallet, Utility Billing & Service Charges (Weeks 17–20)
| Task | Output |
|---|---|
| Ledger-backed wallet service (tenant/owner/vendor/platform), holds/escrow | `internal/wallet` |
| Expense management with GL coding and approval chains | `internal/expense` |
| Meter + tariff engine, consumption billing, shared allocation | `internal/utility` |
| Service charge budget/allocation/CAM engine | `internal/servicecharge` |

**Exit criteria:** utility and service-charge invoices generate correctly and post against the same ledger as rent.

### Phase 5 — Maintenance, Vendor & Visitor Management (Weeks 21–24)
| Task | Output |
|---|---|
| Maintenance request intake + SLA-driven work order engine | `internal/maintenance` |
| Vendor profile, contract lifecycle, SLA performance tracking | `internal/vendor` |
| Visitor pre-registration, badge/QR issuance, check-in/out | `internal/visitor` |

**Exit criteria:** a maintenance request can be raised, assigned to a vendor, tracked against SLA, and closed with audit trail.

### Phase 6 — Documents, Notifications, Reporting, Support (Weeks 25–28)
| Task | Output |
|---|---|
| Document storage/versioning/retention via Supabase Storage | `internal/documents` |
| Notification engine: multi-channel dispatcher, templates, retry/dead-letter | `internal/notifications` |
| Reporting service: occupancy, collections, P&L, custom report builder, scheduled export | `internal/reporting` |
| Support/ticketing + KB CMS | `internal/support` |

**Exit criteria:** notifications fire on key domain events; reports export correctly; tickets route to the right department.

### Phase 7 — Hardening, Observability & Launch Readiness (Weeks 29–32)
| Task | Output |
|---|---|
| Load testing, query optimization, caching pass (Redis) | Performance report |
| Security review: OWASP Top 10 pass, secrets management, pen-test fixes | Security sign-off |
| Full audit/compliance report generation (KYC/AML/GDPR) | Compliance module complete |
| Kubernetes production manifests, autoscaling, blue/green pipeline | Production deployment |
| Disaster recovery + backup/restore drill | DR runbook |

**Exit criteria:** production-ready, passes security review, deployable via CI/CD with rollback.

### Dependencies & Sequencing Notes
- Phase 1 (Tenancy/RBAC/Audit) is a hard blocker for every later phase — do not parallelize ahead of it.
- Phase 3 (Lease/Payments) depends on Phase 2 (Property/Unit/Tenant).
- Phase 4 and Phase 5 can run in parallel once Phase 3 is stable (independent domains sharing only the ledger and notification interfaces).
- Notification Engine (Phase 6) should expose its interface early (Phase 1) even if full implementation lands later, since Phases 3–5 need to emit events against it.
